<?php

namespace App\Http\Controllers\Front\Member;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Customer;
use App\Services\Tickets\CreateTickets;   // ← tambahkan
use Illuminate\Http\Request;

class PrintMemberViewController extends Controller
{
    protected $ticketService;

    public function __construct(CreateTickets $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function printMember($customerID)
    {
        $customer = Customer::with(['tiketTerbaru.entries'])->findOrFail($customerID);

        $ticket = $customer->tiketTerbaru;

        if ($ticket) {

            // gunakan service, BUKAN $this->createLogPrintMember()
            $this->ticketService->createLogPrintMember($ticket, $customer);

            // reload entries
            $ticketEntries = $ticket->entries()->orderBy('created_at', 'asc')->get();

        } else {
            $ticketEntries = collect();
        }

        return view('front.print_ticket.member.print-ticket-member', compact('customer', 'ticket', 'ticketEntries'));
    }
}
