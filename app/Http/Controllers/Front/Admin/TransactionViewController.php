<?php

namespace App\Http\Controllers\Front\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Support\Carbon;

class TransactionViewController extends Controller
{
    public function transactionIndex()
    {
        // filter transaksi hanya hari ini
        $today = Carbon::today();

        $transactions = Purchase::with(['purchaseDetails', 'customer'])
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        // summary untuk cards
        $totalTransaksi = $transactions->count();
        $pendapatanHariIni = $transactions->sum('total');
        $pending = $transactions->where('status', 1)->count();

        return view('front.admin.transaction', compact(
            'transactions',
            'totalTransaksi',
            'pendapatanHariIni',
            'pending'
        ));
    }
}

