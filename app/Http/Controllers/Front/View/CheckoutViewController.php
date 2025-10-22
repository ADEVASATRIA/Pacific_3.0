<?php

namespace App\Http\Controllers\Front\View;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use App\Models\Customer;
// use App\Models\Sponsor;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\DetailPayment;
use App\Models\Ticket;
use App\Models\TicketEntry;

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
        $sponsor = Sponsor::where('status', 1)->whereNull('deleted_at')->where('status', 1)->get();

        return view("front.buy_ticket.checkout_view", [
            "items" => $items,
            "subTotal" => session("subTotal"),
            "tax" => session("tax"),
            "total" => session("total"),
            "customer" => $customer,
            "customerId" => $customerId,
            "checkoutToken" => $token,
            "sponsor" => $sponsor,
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
