<?php

namespace App\Http\Controllers\Front\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promo;
use App\Models\Voucher;
use App\Models\VoucherLog;
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
        $subTotal = $request->input('sub_total');
        $now = now();

        // 1️⃣ CEK SEBAGAI PROMO DAHULU
        $promo = Promo::where('code', $code)->where('is_active', 1)->first();

        if ($promo) {
            if ($promo->start_date && $now->lt(Carbon::parse($promo->start_date))) {
                return response()->json(['success' => false, 'message' => 'Promo belum aktif.']);
            }
            if ($promo->expired_date && $now->gt(Carbon::parse($promo->expired_date)->endOfDay())) {
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

        // 🔹 Hitung total baru
        $newTotal = max($subTotal - $discount, 0);

        return response()->json([
            'success' => true,
            'message' => "Promo {$promo->code} berhasil diterapkan! Diskon sebesar Rp " . number_format($discount, 0, ',', '.'),
            'type' => 'promo',
            'promo_id' => $promo->id,
            'discount' => $discount,
            'new_total' => $newTotal,
            'formatted_subtotal' => number_format($subTotal, 0, ',', '.'),
            'formatted_discount' => number_format($discount, 0, ',', '.'),
            'formatted_total' => number_format($newTotal, 0, ',', '.'),
            'discounted_items' => $discountedItems,
        ]);
    }

        // 2️⃣ JIKA BUKAN PROMO, CEK SEBAGAI VOUCHER (DI VOUCHER LOG)
        $voucherLog = VoucherLog::where('code', $code)
            ->where('is_active', 1)
            ->with('voucher')
            ->first();

        if ($voucherLog && $voucherLog->voucher) {
            $voucher = $voucherLog->voucher;

            // Validasi tanggal Voucher Utama (opsional jika voucher log sudah mewakili)
            if ($voucher->start_date && $now->lt(Carbon::parse($voucher->start_date))) {
                return response()->json(['success' => false, 'message' => 'Voucher belum aktif.']);
            }
            if ($voucher->end_date && $now->gt(Carbon::parse($voucher->end_date)->endOfDay())) {
                return response()->json(['success' => false, 'message' => 'Voucher telah berakhir.']);
            }

            // Validasi minimal pembelian
            if ($voucher->min_purchase && $subTotal < $voucher->min_purchase) {
                return response()->json(['success' => false, 'message' => 'Total pembelian belum mencapai minimum untuk voucher ini.']);
            }

            // Hitung diskon voucher (mengurangi subtotal langsung)
            $discount = 0;
            if ($voucher->type_voucher === 'percent') {
                $discount = $subTotal * ($voucher->value / 100);
            } else {
                // fixed
                $discount = $voucher->value;
            }

            // Batasi maksimal diskon
            if ($voucher->max_discount && $discount > $voucher->max_discount) {
                $discount = $voucher->max_discount;
            }

            $newTotal = max($subTotal - $discount, 0);

            return response()->json([
                'success' => true,
                'message' => "Voucher {$voucher->name} berhasil diterapkan! Diskon sebesar Rp " . number_format($discount, 0, ',', '.'),
                'type' => 'voucher',
                'voucher_id' => $voucher->id,
                'voucher_log_id' => $voucherLog->id,
                'discount' => $discount,
                'new_total' => $newTotal,
                'formatted_subtotal' => number_format($subTotal, 0, ',', '.'),
                'formatted_discount' => number_format($discount, 0, ',', '.'),
                'formatted_total' => number_format($newTotal, 0, ',', '.'),
                'discounted_items' => [], // Voucher tidak mengubah harga per item
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Kode promo atau voucher tidak ditemukan, tidak aktif, atau sudah digunakan.']);
    }
}
