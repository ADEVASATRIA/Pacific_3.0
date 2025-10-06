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

class TransactionViewController extends Controller
{
    public function transactionIndex(Request $request)
    {
        $today = Carbon::today();
        $staff = Auth::guard('fo')->user();

        // Filter berdasarkan jenis pembayaran
        $filterPayments = $request->input('payment', []);

        $query = Purchase::with(['purchaseDetails', 'customer'])
            ->whereDate('created_at', $today);

        if (!empty($filterPayments)) {
            $query->whereIn('payment', $filterPayments);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        // Jika user klik tombol Export
        if ($request->has('export')) {
            return Excel::download(
                new TransactionsExport($transactions, $staff),
                'Transaksi-Hari-Ini-' . $today->format('d-m-Y') . '.xlsx'
            );
        }

        // Summary
        $totalTransaksi = $transactions->count();
        $pendapatanHariIni = $transactions->sum('total');
        $pending = $transactions->where('status', 1)->count();

        // Ambil saldo awal shift aktif
        $cashSession = CashSession::where('staff_id', $staff->id)
            ->whereDate('waktu_buka', $today)
            ->where('status', 1)
            ->latest()
            ->first();

        $saldoAwal = $cashSession?->saldo_awal ?? 0;
        $pendapatanDenganSaldo = $pendapatanHariIni + $saldoAwal;

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

        $cashSessionQuery = CashSession::where('staff_id', $staff->id)
            ->whereDate('waktu_buka', $today)
            ->where('status', 1)
            ->latest();

        $cashSession = $cashSessionQuery->first();

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
            'cashSession'
        ));
    }

    public function export()
    {
        $staff = Auth::guard('fo')->user();
        $today = Carbon::today();

        $transactions = Purchase::with(['purchaseDetails', 'customer'])
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        return Excel::download(
            new TransactionsExport($transactions, $staff),
            'Report-Transaksi-' . $today->format('d-m-Y') . '.xlsx'
        );
    }
}
