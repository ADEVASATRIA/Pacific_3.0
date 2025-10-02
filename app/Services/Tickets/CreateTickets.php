<?php

namespace App\Services\Tickets;

use App\Models\TicketType;
use App\Models\Ticket;
use App\Models\PurchaseDetail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\TicketEntry;
use App\Helpers\TicketHelper;
use App\Models\MemberPass;

class CreateTickets
{
    public function createTicketRegular(PurchaseDetail $purchaseDetail)
    {
        $purchase = $purchaseDetail->purchase;
        $ticketType = TicketType::find($purchaseDetail->purchase_item_id);
    
        if (!$purchase || !$ticketType) {
            throw new \Exception("Purchase atau TicketType tidak ditemukan.");
        }
    
        $createdTickets = [];
        $qty = (int) $purchaseDetail->qty;
    
        for ($i = 0; $i < $qty; $i++) {
            $ticketkodeRef = $ticketType->ticket_kode_ref;
    
            $ticket = new Ticket();
            $ticket->purchase_detail_id = $purchaseDetail->id;
            $ticket->customer_id = $purchase->customer_id;
            $ticket->code = $ticketkodeRef . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $ticket->ticket_kode_ref = $ticketType->ticket_kode_ref;
            $ticket->date_start = now();
            $ticket->date_end = now()->addDays($ticketType->duration);
            $ticket->is_active = true;
            $ticket->save();
    
            $entries = [];
    
            // ✅ Entry original
            $entries[] = $this->createEntry($ticket, 1);
    
            // ✅ Entry extra jika ada
            if ($ticketType->qty_extra > 0) {
                for ($j = 0; $j < $ticketType->qty_extra; $j++) {
                    $entries[] = $this->createEntry($ticket, 2);
                }
            }
    
            $ticket->entries = $entries;
            $createdTickets[] = $ticket;
        }
        // dd([
        //    'purchaseDetail' => $purchaseDetail->toArray(),
        //    'ticketType' => $ticketType->toArray(),
        //    'createdTickets' => $createdTickets,
        // ]);

    
        return $createdTickets;
    }
    
    private function createEntry(Ticket $ticket, int $type): TicketEntry
    {
        $entry = new TicketEntry();
        $entry->ticket_id = $ticket->id;
        $entry->date_valid = now();
        $entry->code = TicketHelper::generateQrCode($ticket->id, $ticket->code . '-' . $type . '-' . uniqid());
        $entry->status = 0;
        $entry->type = $type;
        $entry->created_at = now();
        $entry->save();
    
        return $entry;
    }
}