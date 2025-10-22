<?php

namespace App\Http\Controllers\Front\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promo;
use Carbon\Carbon;

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

        $now = Carbon::now();
        if ($promo->start_date && $now->lt(Carbon::parse($promo->start_date))) {
            return response()->json(['success' => false, 'message' => 'Promo belum aktif.']);
        }

        if ($promo->expired_date && $now->gt(Carbon::parse($promo->expired_date))) {
            return response()->json(['success' => false, 'message' => 'Promo telah berakhir.']);
        }

        if ($promo->quota <= 0) {
            return response()->json(['success' => false, 'message' => 'Promo sudah habis (quota habis).']);
        }

        // Cek apakah semua tiket di items termasuk tipe yang boleh
        $validTicketIds = $promo->ticket_types ?? [];
        $items = collect($request->items);
        $eligible = $items->contains(function ($item) use ($validTicketIds) {
            return in_array($item['id'], $validTicketIds);
        });

        if (!$eligible) {
            return response()->json(['success' => false, 'message' => 'Kode promo tidak berlaku untuk tipe tiket ini.']);
        }

        $subTotal = $request->input('sub_total');
        if ($promo->min_purchase && $subTotal < $promo->min_purchase) {
            return response()->json(['success' => false, 'message' => 'Total pembelian belum mencapai minimum promo.']);
        }

        // Hitung diskon
        $discount = 0;
        if ($promo->type == 1) { // Persentase
            $discount = $subTotal * ($promo->value / 100);
        } elseif ($promo->type == 2) { // Nominal langsung
            $discount = $promo->value;
        }

        // Batasi maksimal diskon
        if ($promo->max_discount && $discount > $promo->max_discount) {
            $discount = $promo->max_discount;
        }

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
        ]);
    }
}
