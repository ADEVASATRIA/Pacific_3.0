<?php

namespace App\Http\Controllers\Front\Admin;

use Illuminate\Http\Request;
use App\Models\CashSession;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;

class CashSessionController
{
    public function store(Request $request)
    {
        $staff = Auth::guard('fo')->user();

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'User belum terautentikasi.']);
        }

        CashSession::create([
            'staff_id' => $staff->id,
            'saldo_awal' => $request->saldo_awal,
            'status' => 1,
            'waktu_buka' => now(),
        ]);

        return response()->json(['success' => true, 'redirect' => route('main')]);
    }

    // ✅ Export laporan harian kasir
    public function exportReport()
    {
        $staff = Auth::guard('fo')->user();
        $today = Carbon::today();

        $transactions = Purchase::with(['purchaseDetails', 'customer'])
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        return Excel::download(
            new TransactionsExport($transactions, $staff),
            'Report-Kasir-' . $today->format('d-m-Y') . '.xlsx'
        );
    }

    // ✅ Proses tutup kasir + logout
    public function processClose(Request $request)
    {
        $staff = Auth::guard('fo')->user();
        $today = Carbon::today();

        // dd($request->all());
        // Validasi input
        $request->validate([
            'saldo_akhir' => 'required|numeric|min:0',
            'fnb_balance' => 'required|numeric|min:0',
            'minus_balance' => 'required|numeric|min:0',
        ]);

        $cashSession = CashSession::where('staff_id', $staff->id)
            ->whereDate('waktu_buka', $today)
            ->where('status', 1)
            ->latest()
            ->first();

        if ($cashSession) {
            $cashSession->saldo_akhir = $request->saldo_akhir;
            $cashSession->fnb_balance = $request->fnb_balance;
            $cashSession->minus_balance = $request->minus_balance;
            $cashSession->status = 0;
            $cashSession->waktu_tutup = now();
            $cashSession->save();
        }
        
        Auth::guard('fo')->logout();

        return response()->json(['success' => true, 'redirect' => route('login')]);
    }
}
