<?php

namespace App\Http\Controllers\Back\Report\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CustomerReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Purchase::with([
                'details' => function ($query) use ($request) {
                    $query->whereIn('type', [
                        PurchaseDetail::TYPE_TICKET,
                        PurchaseDetail::TYPE_PACKAGE_COMBO
                    ]);

                    if ($request->filled('ticket_type')) {
                        $query->where('name', 'like', '%' . $request->ticket_type . '%');
                    }
                },
                'customer'
            ])->where('status', Purchase::STATUS_PAID);

            // Filter Status Customer
            if ($request->filled('is_active')) {
                $query->whereHas('customer', function ($q) use ($request) {
                    $q->where('is_active', $request->is_active);
                });
            }

            // Wajib pilih bulan agar tidak terlalu berat
            if (!$request->filled('start_month') || !$request->filled('end_month')) {
                return redirect()->back()->with('error', 'Silakan pilih rentang bulan terlebih dahulu.');
            }

            // Filter berdasarkan bulan
            try {
                $startMonth = Carbon::parse($request->start_month)->startOfMonth();
                $endMonth = Carbon::parse($request->end_month)->endOfMonth();
                $query->whereBetween('created_at', [$startMonth, $endMonth]);
            } catch (\Exception $e) {
                Log::error('Error parsing months: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Format bulan tidak valid.');
            }

            // Ambil data
            $purchases = $query->limit(1000)->get(); // batasi sementara agar tidak overload

            $summary = [];

            foreach ($purchases as $purchase) {
                foreach ($purchase->details as $detail) {
                    $key = $purchase->created_at->format('Y-m') . '-' . $detail->name;

                    if (!isset($summary[$key])) {
                        $summary[$key] = [
                            'month' => $purchase->created_at->format('F Y'),
                            'ticket_name' => $detail->name,
                            'visitor_ids' => [],
                        ];
                    }

                    $customerId = $purchase->customer?->id;
                    if ($customerId && !in_array($customerId, $summary[$key]['visitor_ids'])) {
                        $summary[$key]['visitor_ids'][] = $customerId;
                    }
                }
            }

            // Hitung jumlah visitor unik
            foreach ($summary as &$item) {
                $item['visitor_count'] = count(array_unique($item['visitor_ids']));
                unset($item['visitor_ids']);
            }

            // Urutkan berdasarkan bulan
            $summary = collect($summary)->sortBy('month')->values();

            return view('back.report.customer_report.index', [
                'summary' => $summary,
                'request' => $request
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing monthly visitor data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan dalam memproses data.');
        }
    }
}
