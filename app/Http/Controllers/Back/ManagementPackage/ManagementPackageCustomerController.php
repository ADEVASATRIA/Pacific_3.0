<?php

namespace App\Http\Controllers\Back\ManagementPackage;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\PackageComboRedeem;
use App\Models\PackageComboRedeemDetail;
use App\Models\Purchase;

class ManagementPackageCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = PackageComboRedeem::with(['customer', 'details'])
            ->where('expired_date', '>=', Carbon::today())
            ->whereNull('fully_redeemed_at')
            ->orderBy('expired_date', 'asc');

        if ($request->has('phone') && !empty($request->phone)) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->phone . '%');
            });
        }

        $activePackages = $query->paginate(20);
        return view('back.management_package.viewActive', compact('activePackages'));
    }


    public function packageDetailCustomer(Request $request)
    {
        // Hanya validasi jika parameter 'phone' ada
        if ($request->has('phone')) {
            $validated = $request->validate([
                'phone' => 'required|regex:/^08[1-9][0-9]{6,10}$/',
            ]);
            $phone = $validated['phone'];
        } else {
            $phone = null;
        }

        $customer = null;
        $purchases = collect();
        $redeems = collect(); // Pastikan redeems terdefinisi
        $totalRedeemedTickets = 0;
        $totalQtyRedeemed = 0;
        $expiredDatesCount = 0;

        $today = \Carbon\Carbon::today(); // Pastikan Carbon dipanggil dengan namespace

        // Hanya jalankan query jika phone ada dan lolos validasi
        if ($phone) {
            $customer = \App\Models\Customer::where('phone', $phone)->first(); // Ganti dengan model Customer Anda
            if ($customer) {
                $purchases = \App\Models\Purchase::whereHas('customer', fn($q) => $q->where('phone', $phone)) // Ganti dengan model Purchase Anda
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

                // ... (Lanjutkan logika Anda untuk $validPurchaseDetailIds dan $redeems)
                $validPurchaseDetailIds = $purchases
                    ->flatMap(fn($p) => $p->purchaseDetails->pluck('id'))
                    ->unique()
                    ->values()
                    ->all();

                if (!empty($validPurchaseDetailIds)) {
                    $redeems = \App\Models\PackageComboRedeem::with('details') // Ganti dengan model Anda
                        ->where('customer_id', $customer->id)
                        ->whereIn('purchase_detail_id', $validPurchaseDetailIds)
                        ->latest()
                        ->get();
                }

                // Hitung totals berdasarkan redeems yang sudah difilter
                $totalRedeemedTickets = $redeems->sum(fn($redeem) => $redeem->details->sum('qty_printed'));
                $totalQtyRedeemed = $redeems->sum(fn($redeem) => $redeem->details->sum('qty_redeemed'));

                // Hitung jumlah package expired
                $expiredDatesCount = $redeems->filter(
                    fn($redeem) =>
                    $redeem->expired_date && Carbon::parse($redeem->expired_date)->lt($today)
                )->count();
            }
        }

        return view('back.management_package.viewDetailPackage', compact(
            'customer',
            'purchases',
            'redeems', // Pastikan variabel ini ada di compact
            'totalRedeemedTickets',
            'totalQtyRedeemed',
            'expiredDatesCount'
        ));
    }

}
