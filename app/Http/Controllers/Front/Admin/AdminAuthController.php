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
            ->where('status', 1)
            ->latest();
        
        $cashSession = $cashSessionQuery->first();
        // dd($cashSession);


        if (!$cashSession) {
            $cashSession = new CashSession([
                'saldo_awal' => 0,
                'waktu_buka' => null,
                'status' => 0,
            ]);
        }
        
        return view('front.admin.index', compact(
            'staff', 
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

