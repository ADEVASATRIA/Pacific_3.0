<?php

namespace App\Http\Controllers\Front\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\Customer;
use App\Models\PackageComboRedeem;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageViewController extends Controller
{
    public function index(Request $request)
    {
        $staff = Auth::guard('fo')->user();

        $customerPhone = $request->query('phone');
        $customer = null;
        $purchases = collect();
        $totalRedeemedTickets = 0;
        $totalQtyRedeemed = 0;
        $expiredDatesCount = 0;

        // hari ini (dipakai untuk cek expired & cash session)
        $today = Carbon::today();

        // --- Inisialisasi cashSession (selalu didefinisikan untuk view) ---
        $cashSession = null;
        if ($staff) {
            $cashSession = CashSession::where('staff_id', $staff->id)
                ->whereDate('waktu_buka', $today->toDateString())
                ->where('status', 1)
                ->latest()
                ->first();
        }

        // jika tidak ada session aktif, bikin object default agar view tidak error saat mengakses properti
        if (!$cashSession) {
            $cashSession = new CashSession([
                'saldo_awal' => 0,
                'waktu_buka' => null,
                'status' => 0,
            ]);
        }

        // --- Logika pencarian paket berdasarkan nomor telepon customer ---
        if ($customerPhone) {
            $customer = Customer::where('phone', $customerPhone)->first();

            if ($customer) {
                // Ambil purchases yang valid (type=3, payment ada/tidak kosong, status=2)
                $purchases = Purchase::whereHas('customer', fn($q) => $q->where('phone', $customerPhone))
                    ->whereHas('purchaseDetails', fn($q) => $q->where('type', 3))
                    ->whereNotNull('payment')
                    ->where('payment', '!=', '')
                    ->where('status', 2)
                    ->with([
                        'customer',
                        'purchaseDetails' => fn($q) => $q->where('type', 3),
                        'purchaseDetails.packageComboRedeem',
                        'purchaseDetails.packageComboRedeem.details',
                    ])
                    ->latest()
                    ->get();

                // Kumpulkan semua purchase_detail.id dari purchases yang valid
                $validPurchaseDetailIds = $purchases
                    ->flatMap(fn($p) => $p->purchaseDetails->pluck('id'))
                    ->unique()
                    ->values()
                    ->all();

                // Ambil PackageComboRedeem yang terkait purchase_detail_id tersebut
                if (!empty($validPurchaseDetailIds)) {
                    $redeems = PackageComboRedeem::with('details')
                        ->where('customer_id', $customer->id)
                        ->whereIn('purchase_detail_id', $validPurchaseDetailIds)
                        ->latest()
                        ->get();
                } else {
                    $redeems = collect();
                }

                // Hitung totals berdasarkan redeems yang sudah difilter (hanya dari purchase valid)
                $totalRedeemedTickets = $redeems->sum(fn($redeem) => $redeem->details->sum('qty_printed'));
                $totalQtyRedeemed = $redeems->sum(fn($redeem) => $redeem->details->sum('qty_redeemed'));

                // Hitung jumlah package expired (berdasarkan expired_date di PackageComboRedeem)
                $expiredDatesCount = $redeems->filter(
                    fn($redeem) =>
                    $redeem->expired_date && Carbon::parse($redeem->expired_date)->lt($today)
                )->count();
            }
        }

        return view('front.admin.viewPackage', compact(
            'customerPhone',
            'purchases',
            'customer',
            'totalRedeemedTickets',
            'totalQtyRedeemed',
            'expiredDatesCount',
            'cashSession',
            'staff'
        ));
    }
}
