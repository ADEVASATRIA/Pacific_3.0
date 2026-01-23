<?php

namespace App\Http\Controllers\Back\ViewLog;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\LogQtyPacketTicket;
use App\Models\LogPrintSingles;
use App\Models\LogPrintMemberPelatih;
use App\Models\PackageComboRedeem;

class ViewLogTicketController extends Controller
{
    public function index(Request $request){
        $today = Carbon::today();
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $phone = $request->input('phone');
        
        $logQtyPacket = LogQtyPacketTicket::with(['log_redeem_packet_tickets', 'package_combo_redeem'])
            ->whereDate('created_at', $selectedDate)
            ->when($phone, function ($query) use ($phone) {
                $query->whereHas('log_redeem_packet_tickets', function ($q) use ($phone) {
                    $q->where('phone', 'like', "%{$phone}%");
                });
            })
            ->latest()
            ->get();

        $logPrintSingles = LogPrintSingles::whereDate('created_at', $selectedDate)
            ->when($phone, function ($query) use ($phone) {
                $query->where('phone', 'like', "%{$phone}%");
            })
            ->where(function ($query) {
                $query->where('ticket_code', 'not like', 'M%')
                    ->where('ticket_code', 'not like', 'TP%');
            })
            ->latest()
            ->get();

        $logPrintMember = LogPrintMemberPelatih::whereDate('created_at', $selectedDate)
            ->when($phone, function ($query) use ($phone) {
                $query->where('phone', 'like', "%{$phone}%");
            })
            ->where('ticket_code', 'like', 'M%')
            ->latest()
            ->get();

        $logPrintPelatih = LogPrintMemberPelatih::whereDate('created_at', $selectedDate)
            ->when($phone, function ($query) use ($phone) {
                $query->where('phone', 'like', "%{$phone}%");
            })
            ->where('ticket_code', 'like', 'TP%')
            ->latest()
            ->get();

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

        return view('back.viewLog.viewLogTicket', compact([
            'logQtyPacket',
            'logPrintSingles',
            'logPrintMember',
            'logPrintPelatih',
            'todaysSummary',
            'today',
            'selectedDate',
            'phone'
        ]));
    }

    public function viewActivePackageCustomer(Request $request){
        $phone = $request->input('phone');
        $query = PackageComboRedeem::with(['customer', 'details'])
            ->where('expired_date', '>=', now())
            ->whereNull('fully_redeemed_at')
            ->orderBy('expired_date', 'asc'); // Urutkan berdasarkan tanggal kadaluarsa terdekat

        // Filter berdasarkan nomor telepon jika ada input 'phone'
        if ($request->has('phone') && !empty($request->phone)) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->phone . '%');
            });
        }

        $activePackages = $query->get();

        return view('back.viewLog.viewActivePackageCustomer', compact([
            'activePackages',
            'phone'
        ]));
    }
}
