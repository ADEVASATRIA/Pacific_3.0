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

        // Get cash session FIRST
        $cashSession = CashSession::where('staff_id', $staff->id)
            ->where('status', 1)
            ->latest()
            ->first();

        // Determine session start time
        $sessionStartTime = $cashSession?->waktu_buka;

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

