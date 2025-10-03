<?php

namespace App\Http\Controllers\Front\Member;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Customer;
use Illuminate\Http\Request;

class PrintMemberViewController extends Controller
{
    public function printMember($customerID)
    {
        $customer = Customer::with([
            'tiketTerbaru.entries'
        ])->findOrFail($customerID);

        $ticket = $customer->tiketTerbaru;
        $ticketEntries = $ticket ? $ticket->entries : collect();
        // dd($ticket, $ticketEntries);

        return view('front.print_ticket.member.print-ticket-member', compact('customer', 'ticket', 'ticketEntries'));
    }

}
