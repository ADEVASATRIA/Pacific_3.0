<?php

namespace App\Http\Controllers\Back\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $nama = $request->input('nama');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $paymentType = $request->input('payment_type');

        $query = Purchase::with('customer')
            ->where('status', 2)
            ->orderBy('created_at', 'desc');

        // Flags filter
        $hasNamaFilter = $nama !== null && $nama !== '';
        $hasDateFilter = !empty($startDate) || !empty($endDate);
        $hasPaymentFilter = $paymentType !== null && $paymentType !== '';

        // 🔍 Filter Nama Customer
        if ($hasNamaFilter) {
            $query->whereHas('customer', function ($q) use ($nama) {
                $q->where('name', 'LIKE', "%{$nama}%");
            });
        }

        // 📅 Filter Tanggal
        if ($hasDateFilter) {
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            } elseif ($startDate) {
                $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
            } elseif ($endDate) {
                $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
            }
        } else {
            if (!$hasPaymentFilter && !$hasNamaFilter) {
                // default hanya tampilkan transaksi hari ini
                $query->whereDate('created_at', Carbon::today());
            }
        }

        // 💳 Filter Jenis Pembayaran
        if ($hasPaymentFilter) {
            $query->where('payment', (int) $paymentType);
        }

        // 📄 Pagination
        $purchases = $query->paginate(10)->withQueryString();

        return view('back.transaction.index', compact('purchases', 'startDate', 'endDate', 'paymentType', 'nama'));
    }



    public function detail($id)
    {
        $purchase = Purchase::with([
            'customer',
            'purchaseDetails.ticketType',
            'purchaseDetails.packageCombo'
        ])->find($id);

        if (!$purchase) {
            return response('<p class="text-gray-400">Data transaksi tidak ditemukan.</p>', 404);
        }

        return view('back.partials.transaction.transaction_detail', compact('purchase'));
    }
}
