<?php

namespace App\Services\Tickets;

use App\Models\Customer;
use App\Models\LogPrintMemberPelatih;
use App\Models\PackageCombo;
use App\Models\PackageComboDetail;
use App\Models\PackageComboRedeem;
use App\Models\TicketType;
use App\Models\Ticket;
use App\Models\PurchaseDetail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\TicketEntry;
use App\Helpers\TicketHelper;
use App\Models\MemberPass;
use App\Models\LogPrintSingles;

class CreateTickets
{
    // public function createTicketRegular(PurchaseDetail $purchaseDetail)
    // {
    //     $purchase = $purchaseDetail->purchase;
    //     $ticketType = TicketType::find($purchaseDetail->purchase_item_id);

    //     if (!$purchase || !$ticketType) {
    //         throw new \Exception("Purchase atau TicketType tidak ditemukan.");
    //     }

    //     $createdTickets = [];
    //     $qty = (int) $purchaseDetail->qty;

    //     for ($i = 0; $i < $qty; $i++) {
    //         $ticketkodeRef = $ticketType->ticket_kode_ref;

    //         $ticket = new Ticket();
    //         $ticket->purchase_detail_id = $purchaseDetail->id;
    //         $ticket->customer_id = $purchase->customer_id;
    //         $ticket->code = $ticketkodeRef . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    //         $ticket->ticket_kode_ref = $ticketType->ticket_kode_ref;
    //         $ticket->date_start = now();
    //         $ticket->date_end = now()->addDays($ticketType->duration);
    //         $ticket->is_active = true;
    //         $ticket->save();

    //         $entries = [];

    //         // ✅ Entry original
    //         $entries[] = $this->createEntry($ticket, 1);

    //         // ✅ Entry extra jika ada
    //         if ($ticketType->qty_extra > 0) {
    //             for ($j = 0; $j < $ticketType->qty_extra; $j++) {
    //                 $entries[] = $this->createEntry($ticket, 2);
    //             }
    //         }

    //         $ticket->entries = $entries;
    //         $createdTickets[] = $ticket;
    //     }
    //     // dd([
    //     //    'purchaseDetail' => $purchaseDetail->toArray(),
    //     //    'ticketType' => $ticketType->toArray(),
    //     //    'createdTickets' => $createdTickets,
    //     // ]);


    //     return $createdTickets;
    // }



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

            // ===== CREATE TICKET =====
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

            // ===== CREATE ENTRIES =====
            $entries = [];
            $entries[] = $this->createEntry($ticket, 1);

            if ($ticketType->qty_extra > 0) {
                for ($j = 0; $j < $ticketType->qty_extra; $j++) {
                    $entries[] = $this->createEntry($ticket, 2);
                }
            }

            $ticket->entries = $entries;



            // ============================================================
            // 🔥 ADDING LOGIC: LogPrintSingles (HISTORY CETAK TIKET)
            // ============================================================
            try {
                $customer = $purchase->customer; // customer langsung dari purchase

                $log = new LogPrintSingles();
                $log->customer_id = $customer?->id;
                $log->ticket_id = $ticket->id;
                $log->ticket_code = $ticket->code;
                $log->customer_name = $customer?->name;
                $log->phone = $customer?->phone;
                $log->status = 1;
                $log->name_tickets = $purchaseDetail->name ?? 'Single Ticket';
                $log->save();

            } catch (\Exception $e) {
                \Log::error('Error creating print single log: ' . $e->getMessage());
                throw $e;
            }
            // ============================================================



            $createdTickets[] = $ticket;
        }

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

    // Create Ticket Pelatih
    public function createTicketMember(PurchaseDetail $purchaseDetail)
    {
        $purchase = $purchaseDetail->purchase;
        $ticketType = TicketType::find($purchaseDetail->purchase_item_id);

        if (!$purchase || !$ticketType) {
            throw new \Exception("Purchase atau TicketType tidak ditemukan.");
        }

        $customer = $purchase->customer;

        $createdTickets = [];
        $qty = (int) $purchaseDetail->qty;

        for ($i = 0; $i < $qty; $i++) {
            $ticket = new Ticket();
            $ticket->purchase_detail_id = $purchaseDetail->id;
            $ticket->customer_id = $customer->id;
            $ticket->code = $ticketType->ticket_kode_ref . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $ticket->ticket_kode_ref = $ticketType->ticket_kode_ref;
            $ticket->date_start = now();
            $ticket->date_end = now()->addDays($ticketType->duration);
            $ticket->is_active = true;
            $ticket->save();

            $entries = [];
            $entries[] = $this->createEntry($ticket, 1);

            if ($ticketType->qty_extra > 0) {
                for ($j = 0; $j < $ticketType->qty_extra; $j++) {
                    $entries[] = $this->createEntry($ticket, 2);
                }
            }

            $ticket->entries = $entries;

            // --- buat log print member sesuai format ---
            $this->createLogPrintMember($ticket, $customer);

            $createdTickets[] = $ticket;
        }

        // Update membership period dan member_id seperti semula
        $customer->awal_masa_berlaku = now();
        $customer->akhir_masa_berlaku = now()->addDays($ticketType->duration);

        if (empty($customer->member_id)) {
            $customer->member_id = $customer->generateMemberId();
        }

        $customer->save();

        return $createdTickets;
    }


    public function createLogPrintMember(Ticket $ticket, Customer $customer)
    {
        $log = new LogPrintMemberPelatih();
        $log->customer_id = $customer->id;
        $log->ticket_id = $ticket->id;
        $log->ticket_code = $ticket->code;
        $log->customer_name = $customer->name;
        $log->phone = $customer->phone;
        $log->status = 1;
        $log->type = 1;  
        $log->save();

        return $log;
    }



    public function createTicketPelatih(PurchaseDetail $purchaseDetail)
    {
        $purchase = $purchaseDetail->purchase;
        $ticketType = TicketType::find($purchaseDetail->purchase_item_id);

        if (!$purchase || !$ticketType) {
            throw new \Exception("Purchase atau TicketType tidak ditemukan.");
        }

        $pelatih = Customer::find($purchase->customer_id);

        $createdTickets = [];
        $qty = (int) $purchaseDetail->qty;

        for ($i = 0; $i < $qty; $i++) {

            // ----- Generate Ticket -----
            $ticket = new Ticket();
            $ticket->purchase_detail_id = $purchaseDetail->id;
            $ticket->customer_id = $pelatih->id;
            $ticket->code = $ticketType->ticket_kode_ref . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $ticket->ticket_kode_ref = $ticketType->ticket_kode_ref;
            $ticket->date_start = now();
            $ticket->date_end = now()->addDays($ticketType->duration);
            $ticket->is_active = true;
            $ticket->save();

            // ----- Generate Ticket Entries -----
            $entries = [];
            $entries[] = $this->createEntry($ticket, 1); // Original

            if ($ticketType->qty_extra > 0) {
                for ($j = 0; $j < $ticketType->qty_extra; $j++) {
                    $entries[] = $this->createEntry($ticket, 2); // Extra entry
                }
            }

            $ticket->entries = $entries;

            // ----- Log Print Pelatih -----
            $this->createLogPrintPelatih($ticket, $pelatih);

            // Simpan ticket yg selesai dibuat
            $createdTickets[] = $ticket;
        }

        return $createdTickets;
    }




    private function createLogPrintPelatih(Ticket $ticket, Customer $pelatih)
    {
        $log = new LogPrintMemberPelatih();
        $log->customer_id = $pelatih->id;
        $log->ticket_id = $ticket->id;
        $log->ticket_code = $ticket->code;
        $log->customer_name = $pelatih->name;
        $log->phone = $pelatih->phone;
        $log->status = 1; // default success / printed
        $log->type = 2;   // tipe 2 sesuai logic lama
        $log->save();

        return $log;
    }



    public function createTicketPackage(PurchaseDetail $purchaseDetail)
    {
        $purchase = $purchaseDetail->purchase;
        $packageCombo = PackageCombo::find($purchaseDetail->purchase_item_id);

        if (!$purchase || !$packageCombo) {
            throw new \Exception("Purchase atau PackageCombo tidak ditemukan.");
        }

        // Buat PackageComboRedeem
        $newRedeem = $purchaseDetail->packageComboRedeem()->create([
            'package_combo_id' => $packageCombo->id,
            'customer_id' => $purchase->customer_id,
            'name' => $packageCombo->name,
            'price' => $packageCombo->price,
            'expired_date' => Carbon::now()->addDays($packageCombo->expired_duration),
            'fully_redeemed' => null,
        ]);

        $createdTickets = [];

        foreach ($packageCombo->details as $comboDetail) {

            $redeemDetail = $newRedeem->details()->create([
                'package_combo_detail_id' => $comboDetail->id,
                'name' => $comboDetail->type == 1
                    ? 'Tiket ' . $comboDetail->ticketType->name
                    : ($comboDetail->type == 2 ? $comboDetail->item->name : ''),
                'qty' => $purchaseDetail->qty * $comboDetail->qty,
                'qty_extra' => $purchaseDetail->qty_extra,
                'qty_redeemed' => $purchaseDetail->qty * $comboDetail->qty,
                'qty_printed' => 0,
            ]);

            // total ticket
            $totalTickets = $redeemDetail->qty;

            for ($i = 0; $i < $totalTickets; $i++) {

                if ($comboDetail->type != 1)
                    continue;

                $ticketCodeRef = $comboDetail->ticketType->ticket_kode_ref;

                $ticket = Ticket::create([
                    'purchase_detail_id' => $purchaseDetail->id,
                    'package_combo_redeem_detail_id' => $redeemDetail->id,
                    'customer_id' => $purchase->customer_id,
                    'code' => $ticketCodeRef . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT),
                    'ticket_kode_ref' => $ticketCodeRef,
                    'date_start' => now(),
                    'date_end' => now()->addDays($packageCombo->expired_duration),
                    'is_active' => true,
                ]);

                // 1️⃣ Entry original
                $this->createEntry($ticket, 1);

                // 2️⃣ Entry gratis sesuai qty_extra, selang-seling
                for ($j = 0; $j < $purchaseDetail->qty_extra; $j++) {
                    $this->createEntry($ticket, 2);
                }

                $createdTickets[] = $ticket;
            }


        }

        return $createdTickets;
    }
}