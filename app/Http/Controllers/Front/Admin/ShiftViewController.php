<?php

namespace App\Http\Controllers\Front\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exports\ShiftExport;
use Maatwebsite\Excel\Facades\Excel;


class ShiftViewController extends Controller
{
    public function index(Request $request)
    {
        $staff = Auth::guard('fo')->user();
        $today = now()->startOfDay();

        // Ambil kas sesi aktif hari ini
        $cashSession = CashSession::where('staff_id', $staff->id)
            ->whereDate('waktu_buka', $today)
            ->where('status', 1)
            ->latest()
            ->first();

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
