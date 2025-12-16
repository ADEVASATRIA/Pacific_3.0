<?php

namespace App\Http\Controllers\Front\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\LogQtyPacketTicket;
use App\Models\LogPrintSingles;
use App\Models\LogPrintMemberPelatih;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use App\Models\CashSession;

class ViewHistoryTicketsController extends Controller
{
    public function viewHistoryTickets(Request $request) {
		$staff = Auth::guard('fo')->user();
        $today = Carbon::today();
        $phone = $request->input('phone');

		// Alur purchase today
		 $purchaseToday = Purchase::whereDate('created_at','=', $today)
            ->where('status', '2')
            ->where('payment', '1')
            ->sum('total');

		// Alur Cash Session 
		$cashSession = null;
        if ($staff) {
            $cashSession = CashSession::where('staff_id', $staff->id)
                ->whereDate('waktu_buka', $today->toDateString())
                ->where('status', 1)
                ->latest()
                ->first();
        }

		// jika tidak ada session aktif, bikin object default agar view tidak error saat mengakses properti
        if (!$cashSession) {
            $cashSession = new CashSession([
                'saldo_awal' => 0,
                'waktu_buka' => null,
                'status' => 0,
            ]);
        }

        // Query untuk package tickets
        // Query for package tickets
		$logQtyPacket = LogQtyPacketTicket::with(['log_redeem_packet_tickets', 'package_combo_redeem'])
			->whereDate('created_at', $today)
			->when($phone, function ($query) use ($phone) {
				$query->whereHas('log_redeem_packet_tickets', function ($q) use ($phone) {
					$q->where('phone', 'like', "%{$phone}%");
				});
			})
			->latest()
			->get();

		// Query for single tickets
		$logPrintSingles = LogPrintSingles::whereDate('created_at', $today)
			->when($phone, function ($query) use ($phone) {
				$query->where('phone', 'like', "%{$phone}%");
			})
			->where(function ($query) {
				$query->where('ticket_code', 'not like', 'M%')
					->where('ticket_code', 'not like', 'TP%');
			})
			->latest()
			->get();

		// Query for member/trainer tickets
		$logPrintMember = LogPrintMemberPelatih::whereDate('created_at', $today)
			->when($phone, function ($query) use ($phone) {
				$query->where('phone', 'like', "%{$phone}%");
			})
			->where('ticket_code', 'like', 'M%')
			->latest()
			->get();

		$logPrintPelatih = LogPrintMemberPelatih::whereDate('created_at', $today)
			->when($phone, function ($query) use ($phone) {
				$query->where('phone', 'like', "%{$phone}%");
			})
			->where('ticket_code', 'like', 'TP%')
			->latest()
			->get();

		// Calculate summary
		$todaysSummary = [
			'total_package_tickets' => $logQtyPacket->count(),
			'total_single_tickets' => $logPrintSingles->count(),
			'total_member_tickets' => $logPrintMember->count(),
			'total_trainer_tickets' => $logPrintPelatih->count(),
			'unique_customers' => collect([
				$logQtyPacket->pluck('log_redeem_packet_tickets.phone'),
				$logPrintSingles->pluck('phone'),
				$logPrintMember->pluck('phone'),
				$logPrintPelatih->pluck('phone')
			])->flatten()->unique()->count()
		];


        return view('front.admin.viewHistoryTickets', compact([
            'logQtyPacket',
			'logPrintSingles',
			'logPrintMember',
			'logPrintPelatih',
			'todaysSummary',
			'today',
			'cashSession',
            'staff',
            'purchaseToday'
        ]));
    }
}
