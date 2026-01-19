<?php

namespace App\Http\Controllers\Front\Admin;

use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\CashSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D;

class TransactionViewController extends Controller
{
    public function transactionIndex(Request $request)
    {
        $today = Carbon::today();
        $staff = Auth::guard('fo')->user();

        // Get cash session FIRST
        $cashSession = CashSession::where('staff_id', $staff->id)
            ->where('status', 1)
            ->latest()
            ->first();

        // Determine session start time
        $sessionStartTime = $cashSession?->waktu_buka;

        // Filter berdasarkan jenis pembayaran
        $filterPayments = $request->input('payment', []);

        // Build transaction query
        $query = Purchase::with(['purchaseDetails', 'customer']);

        // Filter by session waktu_buka if exists, otherwise fallback to today
        if ($sessionStartTime) {
            $query->where('created_at', '>=', $sessionStartTime);
        } else {
            $query->whereDate('created_at', $today);
        }

        if (!empty($filterPayments)) {
            $query->whereIn('payment', $filterPayments);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        // Summary
        $totalTransaksi = $transactions->count();
        $pendapatanHariIni = $transactions->sum('total');
        $pending = $transactions->where('status', 1)->count();

        // Opsi payment
        $paymentOptions = [
            1 => 'Cash',
            2 => 'QRIS BCA',
            3 => 'QRIS Mandiri',
            4 => 'Debit BCA',
            5 => 'Debit Mandiri',
            6 => 'Transfer',
            7 => 'QRIS BRI',
            8 => 'Debit BRI',
        ];

        $saldoAwal = $cashSession?->saldo_awal ?? 0;
        $pendapatanDenganSaldo = $pendapatanHariIni + $saldoAwal;

        // Build base query for payment summaries with session filter
        $baseQuery = function () use ($sessionStartTime, $today) {
            $q = Purchase::where('status', '2');
            if ($sessionStartTime) {
                $q->where('created_at', '>=', $sessionStartTime);
            } else {
                $q->whereDate('created_at', $today);
            }
            return $q;
        };

        $purchaseTunai = $baseQuery()->where('payment', '1')->sum('total');
        $purchaseQrisBca = $baseQuery()->where('payment', '2')->sum('total');
        $purchaseQrisMandiri = $baseQuery()->where('payment', '3')->sum('total');
        $purchaseDebitBca = $baseQuery()->where('payment', '4')->sum('total');
        $purchaseDebitMandiri = $baseQuery()->where('payment', '5')->sum('total');
        $purchaseQrisBri = $baseQuery()->where('payment', '7')->sum('total');
        $purchaseDebitBri = $baseQuery()->where('payment', '8')->sum('total');

        if (!$cashSession) {
            $cashSession = new CashSession([
                'saldo_awal' => 0,
                'waktu_buka' => null,
                'status' => 0,
            ]);
        }

        return view('front.admin.transaction', compact(
            'transactions',
            'totalTransaksi',
            'pendapatanHariIni',
            'pending',
            'saldoAwal',
            'pendapatanDenganSaldo',
            'staff',
            'paymentOptions',
            'filterPayments',
            'cashSession',
            'purchaseTunai',
            'purchaseQrisBca',
            'purchaseQrisMandiri',
            'purchaseDebitBca',
            'purchaseDebitMandiri',
            'purchaseQrisBri',
            'purchaseDebitBri'
        ));
    }

    public function export(Request $request)
    {
        $staff = Auth::guard('fo')->user();
        $today = Carbon::today();

        // 1. Get Cash Session logic (same as index)
        $cashSession = CashSession::where('staff_id', $staff->id)
            ->where('status', 1)
            ->latest()
            ->first();

        $sessionStartTime = $cashSession?->waktu_buka;

        // Ambil filter dari request
        $filterPayments = $request->input('payment', []);

        $query = Purchase::with(['purchaseDetails', 'customer']);

        // Filter by session waktu_buka if exists, otherwise fallback to today
        if ($sessionStartTime) {
            $query->where('created_at', '>=', $sessionStartTime);
        } else {
            $query->whereDate('created_at', $today);
        }

        // Apply filter jika ada
        if (!empty($filterPayments)) {
            $query->whereIn('payment', $filterPayments);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        return Excel::download(
            new TransactionsExport($transactions, $staff),
            'Report-Harian-' . $today->format('d-m-Y') . '.xlsx'
        );
    }
}
