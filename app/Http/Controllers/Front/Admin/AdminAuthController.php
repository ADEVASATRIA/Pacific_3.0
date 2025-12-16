<?php

namespace App\Http\Controllers\Front\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\CashSession;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\Purchase;

use Illuminate\Support\Facades\Log;

class AdminAuthController
{
    public function index()
    {
        $staff = Auth::guard('fo')->user();

        if (!$staff) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $today = Carbon::today();
        // Kalkulasi total penjualan tiket tunai 
        $purchaseToday = Purchase::whereDate('created_at','=', $today)
            ->where('status', '2')
            ->where('payment', '1')
            ->sum('total');
        
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

        return view('front.admin.index', compact('staff', 'cashSession', 'purchaseToday'));
    }


    public function checkPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string'
        ]);

        $admin = Admin::where('pin', $request->pin)->first();

        if ($admin != null) {
            session(['is_admin_logged_in' => true]);
            return response()->json([
                'success' => true,
                'redirect' => route('admin.index')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'PIN salah. Coba lagi.'
        ], 401);
    }
}

