<?php

namespace App\Http\Controllers\Front\View;

use App\Http\Controllers\Controller;
use App\Models\PackageCombo;
use Illuminate\Http\Request;
use App\Models\TicketType;
use App\Models\Customer;

class TiketController extends Controller
{
    public function indexViewTicket(Request $request)
    {
        $customerId = $request->query('customer', null);
        $filterType = $request->query('filter_type', null);
        
        // Ticket Regular
        $ticketRegular = $filterType == 1 || $filterType == null
            ? TicketType::where('tipe_khusus', 1)->where('is_active', 1)->where('deleted_at', null)->get() : collect();
            
        // Ticket Pengantar
        $ticketPengantar = $filterType == 2 || $filterType == null
            ? TicketType::where('tipe_khusus', 2)->where('is_active', 1)->where('deleted_at', null)->get() : collect();
        
        // Ticket Pelatih
        $ticketPelatih = $filterType == 3 || $filterType == null
            ? TicketType::where('tipe_khusus', 3)->where('is_active', 1)->where('deleted_at', null)->get() : collect();
            
        // Ticket Member
        $ticketMember = $filterType == 4 || $filterType == null
            ? TicketType::where('tipe_khusus', 4)->where('is_active', 1)->where('deleted_at', null)->get() : collect();

        // Ticket Pelatih
        $ticketBiayaPelatih = $filterType == 5 || $filterType == null
            ? TicketType::where('tipe_khusus', 5)->where('is_active', 1)->where('deleted_at', null)->get() : collect();
        
        // Ticket Package 
        $ticketPackage = $filterType == 6 || $filterType == null
            ? PackageCombo::where('is_active', 1)->where('deleted_at', null)->get() : collect();
        
        $customer = $customerId ? Customer::find($customerId) : null;

        return view('front.buy_ticket.ticket_view', compact([
            'ticketRegular',
            'ticketPengantar',
            'ticketPelatih',
            'ticketMember',
            'ticketBiayaPelatih',
            'ticketPackage',
            'customer',
            'customerId',
            'filterType'
        ]));
    }
}
