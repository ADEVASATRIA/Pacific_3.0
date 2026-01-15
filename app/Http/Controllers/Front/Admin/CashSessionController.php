<?php

namespace App\Http\Controllers\Front\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\CashSession;
use App\Models\CashInOut;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailyReportExport;


class CashSessionController
{
    // Alur Cek Session yang  masih belum di tutup
    public function checkLastSession(Request $request)
    {
        $staff = Auth::guard('fo')->user();

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'User belum terautentikasi.']);
        }

        $lastSession = CashSession::where('staff_id', $staff->id)
            ->where('status', 1)
            ->latest()
            ->first();

        // dd($lastSession);

        if ($lastSession) {
            return response()->json(['success' => false, 'message' => 'Session kasir masih terbuka.']);
        } else {
            return response()->json(['success' => true, 'message' => 'Session kasir belum terbuka.']);
        }

    }
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
    public function exportReport(Request $request)
    {
        $staff = Auth::guard('fo')->user();
        $today = Carbon::today();
        $type = $request->query('type'); // 'pdf' or null (excel)

        // Get cash session data
        $cashSession = CashSession::where('staff_id', $staff->id)
            ->where('status', 1)
            ->latest()
            ->first();

        $transactions = Purchase::with(['purchaseDetails', 'customer'])
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        $export = new DailyReportExport($transactions, $staff, $cashSession);
        $filename = 'Report-Kasir-' . $today->format('d-m-Y');

        if ($type === 'pdf') {
            return Excel::download($export, $filename . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }

        return Excel::download($export, $filename . '.xlsx');
    }

    // ✅ Proses tutup kasir + logout
    public function processClose(Request $request)
    {
        $staff = Auth::guard('fo')->user();
        $today = Carbon::today();
        // dd($request->all());

        // Validasi input
        $validatedData = $request->validate([
            // 'saldo_awal' => 'required|numeric|min:0',
            'saldo_akhir' => 'required|numeric|min:0',
            'penjualan_fnb_kolam' => 'required|numeric|min:0',
            'penjualan_fnb_cafe' => 'required|numeric|min:0',

            // Array Request Cash in & Out
            'cash_in_out' => 'array',
            'cash_in_out.*.nominal_uang' => 'numeric|min:0',
            'cash_in_out.*.type' => 'integer|in:1,2',
            'cash_in_out.*.keterangan' => 'required|string|max:255',
        ]);


        $cashSession = CashSession::where('staff_id', $staff->id)
            ->whereDate('waktu_buka', $today)
            ->where('status', 1)
            ->latest()
            ->first();

        DB::beginTransaction();

        try {
            if ($cashSession) {
                // $cashSession->saldo_awal = $request->saldo_awal;
                $cashSession->saldo_akhir = $request->saldo_akhir;
                $cashSession->penjualan_fnb_kolam = $request->penjualan_fnb_kolam;
                $cashSession->penjualan_fnb_cafe = $request->penjualan_fnb_cafe;
                $cashSession->waktu_tutup = now();
                $cashSession->status = 0;
                $cashSession->save();

                $cashInOutItems = $validatedData['cash_in_out'] ?? [];
                if (is_array($cashInOutItems) && count($cashInOutItems) > 0) {
                    $cashSession->cashInOut()->createMany($cashInOutItems);
                }

            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menutup kasir.'], 500);
        }


        Auth::guard('fo')->logout();

        return response()->json(['success' => true, 'redirect' => route('login')]);
    }
}
