<?php

namespace App\Http\Controllers\Front\View;

use App\Http\Controllers\Controller;
use App\Models\Clubhouse;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use App\Models\Customer;
// use App\Models\Sponsor;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\DetailPayment;
use App\Models\Ticket;
use App\Models\TicketEntry;
use App\Models\TicketType;

class CheckoutViewController extends Controller
{
    public function checkoutView(Request $request)
    {
        $items = session("items");
        $customerId = session('customer_id');
        $token = session('checkout_token');

        if (empty($items) || empty($customerId) || empty($token)) {
            return redirect()->route("main")->with("error", "Akses tidak valid.");
        }

        $customer = Customer::find($customerId);
        $sponsor = Sponsor::where('status', 1)
            ->whereNull('deleted_at')
            ->get();

        $items = collect($items)->map(function ($item) {
            $ticketType = TicketType::find($item['id']);
            $item['is_coach_club_require'] = $ticketType?->is_coach_club_require ?? 0;
            return $item;
        });
        
        $clubhouses = Clubhouse::whereNull('deleted_at')
            ->get(['id', 'name']);

        $coaches = Customer::where('is_pelatih', 1)
            ->whereNull('deleted_at')
            ->get(['id', 'name']);

        return view("front.buy_ticket.checkout_view", [
            "items" => $items,
            "subTotal" => session("subTotal"),
            "tax" => session("tax"),
            "total" => session("total"),
            "customer" => $customer,
            "customerId" => $customerId,
            "checkoutToken" => $token,
            "sponsor" => $sponsor,
            "clubhouses" => $clubhouses,
            "coaches" => $coaches
        ]);
    }


    public function printTickets($purchaseId)
    {
        // Ambil data utama
        $purchase = Purchase::findOrFail($purchaseId);
        $purchaseDetails = PurchaseDetail::where('purchase_id', $purchaseId)->get();
        $customer = Customer::findOrFail($purchase->customer_id);

        // Filter hanya purchase detail dengan type == 1 (ticket_type)
        $allowedDetailIds = $purchaseDetails
            ->filter(fn($detail) => $detail->type == 1)
            ->pluck('id');

        // Ambil semua tiket dari detail yang diizinkan
        $tickets = Ticket::whereIn('purchase_detail_id', $allowedDetailIds)->get();

        // Ambil ticket entries untuk tiket tersebut
        $ticketEntries = TicketEntry::whereIn('ticket_id', $tickets->pluck('id'))->get();

        return view("front.print_ticket.print_ticket", [
            "purchase" => $purchase,
            "customer" => $customer,
            "purchaseDetails" => $purchaseDetails,
            "tickets" => $tickets,
            "ticketEntries" => $ticketEntries,
        ]);
    }


}
