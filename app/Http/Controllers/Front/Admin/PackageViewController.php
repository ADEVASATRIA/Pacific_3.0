<?php

namespace App\Http\Controllers\Front\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\Customer;
use App\Models\PackageComboRedeem;
use App\Models\Purchase;

use App\Models\LogQtyPacketTicket;
use App\Models\LogRedeemPacketTicket;
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

    public function logHistoryRedeemCustomerPackage(Request $request)
    {
        $staff = Auth::guard('fo')->user();
        $customerPhone = $request->query('phone');
        $customer = null;
        $viewData = collect();

        $today = Carbon::today();

        // Ambil cash session aktif
        $cashSession = null;
        if ($staff) {
            $cashSession = CashSession::where('staff_id', $staff->id)
                ->whereDate('waktu_buka', $today->toDateString())
                ->where('status', 1)
                ->latest()
                ->first();
        }

        if (!$cashSession) {
            $cashSession = new CashSession([
                'saldo_awal' => 0,
                'waktu_buka' => null,
                'status' => 0,
            ]);
        }

        // Jika nomor telepon diisi
        if ($customerPhone) {
            $customer = Customer::where('phone', $customerPhone)
                ->whereNull('deleted_at')
                ->first();

            if ($customer) {
                $logQtyPacket = LogQtyPacketTicket::whereHas('log_redeem_packet_tickets', function ($query) use ($customer) {
                    $query->where('customer_id', $customer->id);
                })
                    ->orderBy('created_at', 'desc')
                    ->get();

                $viewData = $logQtyPacket->map(function ($ticket) {
                    return [
                        'purchase_date' => optional($ticket->package_combo_redeem)->created_at
                            ? Carbon::parse($ticket->package_combo_redeem->created_at)->format('d F Y')
                            : '-',
                        'package_name' => optional($ticket->package_combo_redeem)->name ?? '-',
                        'print_date' => $ticket->created_at
                            ? $ticket->created_at->format('d F Y H:i')
                            : '-',
                    ];
                });
            }
        }

        // Jika request AJAX
        if ($request->ajax()) {
            return response()->json([
                'customer' => $customer ? [
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                ] : null,
                'data' => $viewData,
            ]);
        }

        // Jika akses langsung non-AJAX
        return view('front.admin.package', compact('customerPhone', 'customer', 'cashSession', 'staff'));
    }

    public function logHistoryRedeemCustomerPackageDetail(Request $request)
    {
        $staff = Auth::guard('fo')->user();
        $customerPhone = $request->query('phone');
        $viewData = collect();
        $id = $request->query('id');

        $today = Carbon::today();
        // Ambil cash session aktif
        $cashSession = null;
        if ($staff) {
            $cashSession = CashSession::where('staff_id', $staff->id)
                ->whereDate('waktu_buka', $today->toDateString())
                ->where('status', 1)
                ->latest()
                ->first();
        }

        if (!$cashSession) {
            $cashSession = new CashSession([
                'saldo_awal' => 0,
                'waktu_buka' => null,
                'status' => 0,
            ]);
        }
        if ($id != null) {
            $logQtyPackage = LogQtyPacketTicket::where('package_combo_redeem_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($logQtyPackage->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data log redeem tidak ditemukan.'
                ], 404);
            }


            $viewData = $logQtyPackage->map(function ($ticket) {
                return [
                    'purchase_date' => optional($ticket->package_combo_redeem)->created_at
                        ? Carbon::parse($ticket->package_combo_redeem->created_at)->format('d F Y')
                        : '-',
                    'package_name' => optional($ticket->package_combo_redeem)->name ?? '-',
                    'print_date' => $ticket->created_at
                        ? $ticket->created_at->format('d F Y H:i')
                        : '-',
                ];
            });
        }

        if ($request->ajax()) {
            return response()->json([
                'data' => $viewData
            ]);
        }

        return view('front.admin.package', compact('cashSession', 'staff'));
    }
}
