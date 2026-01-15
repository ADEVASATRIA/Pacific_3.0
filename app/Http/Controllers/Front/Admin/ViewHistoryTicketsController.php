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
            'purchaseTunai',
            'purchaseQrisBca',
            'purchaseQrisMandiri',
            'purchaseDebitBca',
            'purchaseDebitMandiri',
            'purchaseQrisBri',
            'purchaseDebitBri'
        ]));
    }
}
