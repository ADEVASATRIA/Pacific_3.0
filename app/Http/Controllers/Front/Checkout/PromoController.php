<?php

namespace App\Http\Controllers\Front\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PromoController extends Controller
{
    public function validatePromo(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string',
            'items' => 'required|array',
            'sub_total' => 'required|numeric|min:0',
        ]);

        $code = strtoupper($request->input('promo_code'));
        $promo = Promo::where('code', $code)->where('is_active', 1)->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Kode promo tidak ditemukan atau tidak aktif.']);
        }

        $now = now();
        if ($promo->start_date && $now->lt(Carbon::parse($promo->start_date))) {
            return response()->json(['success' => false, 'message' => 'Promo belum aktif.']);
        }
        if ($promo->expired_date && $now->gt(Carbon::parse($promo->expired_date))) {
            return response()->json(['success' => false, 'message' => 'Promo telah berakhir.']);
        }
        if ($promo->quota <= 0) {
            return response()->json(['success' => false, 'message' => 'Promo sudah habis (quota habis).']);
        }

        $validTicketIds = $promo->ticket_types ?? [];
        $items = collect($request->items);

        // 🔹 Filter item yang eligible untuk promo
        $eligibleItems = $items->filter(fn($item) => in_array($item['id'], $validTicketIds));

        if ($eligibleItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Kode promo tidak berlaku untuk tipe tiket ini.']);
        }

        $subTotal = $request->input('sub_total');

        // 🔹 Total harga tiket yang eligible
        $eligibleTotal = $eligibleItems->sum(fn($item) => $item['price'] * $item['qty']);

        if ($promo->min_purchase && $eligibleTotal < $promo->min_purchase) {
            return response()->json(['success' => false, 'message' => 'Total pembelian tiket promo belum mencapai minimum.']);
        }

        // 🔹 Hitung diskon per tiket
        $discount = 0;
        $discountedItems = [];

        foreach ($eligibleItems as $item) {
            $itemDiscount = 0;

            if ($promo->type == 1) {
                // Diskon persentase per tiket
                $itemDiscount = $item['price'] * ($promo->value / 100);
            } elseif ($promo->type == 2) {
                // Diskon nominal per tiket
                $itemDiscount = $promo->value;
            }

            $newPrice = max($item['price'] - $itemDiscount, 0);
            $newTotal = $newPrice * $item['qty'];

            $discountedItems[] = [
                'id' => $item['id'],
                'old_price' => $item['price'],
                'new_price' => $newPrice,
                'old_total' => $item['price'] * $item['qty'],
                'new_total' => $newTotal,
            ];

            $discount += $itemDiscount * $item['qty'];
        }

        // 🔹 Batasi maksimal total diskon
        if ($promo->max_discount && $discount > $promo->max_discount) {
            $discount = $promo->max_discount;
        }

        // 🔹 Kurangi quota promo (satu kali per penggunaan)
        DB::transaction(function () use ($promo) {
            $promo->decrement('quota', 1);
        });

        // 🔹 Hitung total baru
        $newTotal = max($subTotal - $discount, 0);

        return response()->json([
            'success' => true,
            'message' => "Promo {$promo->code} berhasil diterapkan! Diskon sebesar Rp " . number_format($discount, 0, ',', '.'),
            'promo_id' => $promo->id,
            'discount' => $discount,
            'new_total' => $newTotal,
            'formatted_subtotal' => number_format($subTotal, 0, ',', '.'),
            'formatted_discount' => number_format($discount, 0, ',', '.'),
            'formatted_total' => number_format($newTotal, 0, ',', '.'),
            'discounted_items' => $discountedItems,
        ]);
    }
}
