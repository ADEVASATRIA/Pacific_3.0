<?php

namespace App\Http\Controllers\Front\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;

use App\Exports\ShiftExport;
use Maatwebsite\Excel\Facades\Excel;


class ShiftViewController extends Controller
{
    public function index(Request $request)
    {
        $staff = Auth::guard('fo')->user();
        $today = now()->startOfDay();

        $purchaseToday = Purchase::whereDate('created_at','=', $today)
            ->where('status', '2')
            ->where('payment', '1')
            ->sum('total');

        // Ambil kas sesi aktif hari ini
        $purchaseTunai = Purchase::whereDate('created_at', $today)->where('status', '2')->where('payment', '1')->sum('total');
        $purchaseQrisBca = Purchase::whereDate('created_at', $today)->where('status', '2')->where('payment', '2')->sum('total');
        $purchaseQrisMandiri = Purchase::whereDate('created_at', $today)->where('status', '2')->where('payment', '3')->sum('total');
        $purchaseDebitBca = Purchase::whereDate('created_at', $today)->where('status', '2')->where('payment', '4')->sum('total');
        $purchaseDebitMandiri = Purchase::whereDate('created_at', $today)->where('status', '2')->where('payment', '5')->sum('total');
        // $purchaseTransfer = Purchase::whereDate('created_at', $today)->where('status', '2')->where('payment', '6')->sum('total'); // Transfer usually not in cashier closing? But listed in request.
        $purchaseQrisBri = Purchase::whereDate('created_at', $today)->where('status', '2')->where('payment', '7')->sum('total');
        $purchaseDebitBri = Purchase::whereDate('created_at', $today)->where('status', '2')->where('payment', '8')->sum('total');
        
        // $purchaseToday used for "Penjualan Tunai Tiket" display in modal, which usually refers to Cash (1). 
        // If $purchaseToday in original code meant all sales, I should check. 
        // Original: ->where('payment', '1')->sum('total'); -> It was already just filtering payment 1 (Cash).
        
        // dd($purchaseToday);

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

        // Ambil filter dari request
        $statusFilter = $request->input('status');
        $staffName = $request->input('staff_name');

        // Query dasar
        $query = CashSession::with('staff')
            ->whereDate('waktu_buka', now());

        // Filter status
        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        // Filter nama staff
        if (!empty($staffName)) {
            $query->whereHas('staff', function ($q) use ($staffName) {
                $q->where('name', 'like', '%' . $staffName . '%');
            });
        }

        $shift = $query->paginate(10)->appends($request->query());

        return view('front.admin.shift_index', [
            'cashSession' => $cashSession,
            'shift' => $shift,
            'staff' => $staff,
            'purchaseTunai' => $purchaseTunai,
            'purchaseQrisBca' => $purchaseQrisBca,
            'purchaseQrisMandiri' => $purchaseQrisMandiri,
            'purchaseDebitBca' => $purchaseDebitBca,
            'purchaseDebitMandiri' => $purchaseDebitMandiri,
            'purchaseQrisBri' => $purchaseQrisBri,
            'purchaseDebitBri' => $purchaseDebitBri
        ]);
    }

    public function export(Request $request)
    {
        $staff = Auth::guard('fo')->user();

        $statusFilter = $request->input('status');
        $staffName = $request->input('staff_name');

        $query = CashSession::with('staff')
            ->whereDate('waktu_buka', now());

        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if (!empty($staffName)) {
            $query->whereHas('staff', function ($q) use ($staffName) {
                $q->where('name', 'like', '%' . $staffName . '%');
            });
        }

        $shift = $query->get();

        $filename = 'Laporan_Shift_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new ShiftExport($shift, $staff), $filename);
    }
}
