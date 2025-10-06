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

    // public function close()
    // {
    //     $staff = Auth::guard('fo')->user();
    //     $today = Carbon::today();

    //     // ✅ Ambil cash session aktif hari ini untuk staff login
    //     $cashSession = CashSession::where('staff_id', $staff->id)
    //         ->whereDate('waktu_buka', $today)
    //         ->where('status', 1)
    //         ->latest()
    //         ->first();

    //     // Kalau belum ada, isi default agar view tidak error
    //     if (!$cashSession) {
    //         $cashSession = new CashSession([
    //             'saldo_awal' => 0,
    //             'waktu_buka' => null,
    //             'status' => 0,
    //         ]);
    //     }

    //     return view('front.admin.close', compact('staff', 'cashSession'));
    // }

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

        $cashSession = CashSession::where('staff_id', $staff->id)
            ->whereDate('waktu_buka', $today)
            ->where('status', 1)
            ->latest()
            ->first();

        if ($cashSession) {
            $cashSession->update([
                'saldo_akhir' => $request->saldo_akhir,
                'status' => 0,
                'waktu_tutup' => now(),
            ]);
        }

        // ✅ Logout setelah tutup kasir
        Auth::guard('fo')->logout();

        return response()->json(['success' => true, 'redirect' => route('login')]);
    }
}
