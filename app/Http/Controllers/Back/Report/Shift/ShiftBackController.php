<?php

namespace App\Http\Controllers\Back\Report\Shift;

use App\Http\Controllers\Controller;
use App\Models\CashInOut;
use App\Models\CashSession;
use Illuminate\Http\Request;

use App\Models\Purchase;
use Carbon\Carbon;

class ShiftBackController extends Controller
{
    public function index(Request $request){
        
        $staffName = $request->query('staff_name');
        $waktuBuka = $request->query('waktu_buka');
        $waktuTutup = $request->query('waktu_tutup');
        $status = $request->query('status');

        $query = CashSession::with(['staff', 'cashInOut']);

        if ($staffName) {
            $query->whereHas('staff', function ($q) use ($staffName) {
                $q->where('name', 'like', "%$staffName%");
            });
        }

        if ($waktuBuka) {
            $query->whereDate('waktu_buka', $waktuBuka);
        }

        if ($waktuTutup) {
            $query->whereDate('waktu_tutup', $waktuTutup);
        }

        if ($status !== null) {
            $query->where('status', $status);
        }
        
        $CashSession = $query->orderBy('created_at', 'desc')->paginate(10);

        // Untuk setiap cash session, ambil data transaksi (purchase) yang terjadi di antara waktu buka dan tutup
        foreach ($CashSession as $session) {
            $purchaseQuery = Purchase::with(['purchaseDetails', 'customer', 'paymentMethod'])
                ->where('staff_id', $session->staff_id)
                ->where('status', Purchase::STATUS_PAID);

            if ($session->waktu_tutup) {
                $purchaseQuery->whereBetween('created_at', [$session->waktu_buka, $session->waktu_tutup]);
            } else {
                $purchaseQuery->where('created_at', '>=', $session->waktu_buka);
            }

            $session->purchases = $purchaseQuery->get();
        }

        return view('back.report.shift_report.index', compact('CashSession'));

    }

    public function showCashInOut($id){
        // ID diambil dari cash session id
        $session = CashSession::with(['staff', 'cashInOut'])->findOrFail($id);
        
        $purchaseQuery = Purchase::with(['purchaseDetails', 'customer', 'paymentMethod'])
            ->where('staff_id', $session->staff_id)
            ->where('status', Purchase::STATUS_PAID);

        if ($session->waktu_tutup) {
            $purchaseQuery->whereBetween('created_at', [$session->waktu_buka, $session->waktu_tutup]);
        } else {
            $purchaseQuery->where('created_at', '>=', $session->waktu_buka);
        }

        $purchases = $purchaseQuery->get();

        return view('back.report.shift_report.detail_cash_in_out', compact('session', 'purchases'));
    }
}
