<?php

namespace App\Http\Controllers\API\Bug;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\TicketType;
use Illuminate\Http\Request;
use App\Helpers\TicketHelper;

use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\TicketEntry;
use App\Models\PurchaseDetail;
use App\Services\Tickets\CreateTickets;

class MemberController extends Controller
{
    protected $ticketService;

    public function __construct(CreateTickets $ticketService)
    {
        $this->ticketService = $ticketService;
    }
    public function getDataMember()
    {
        $member = Customer::whereNotNull('member_id')
            ->whereDate('awal_masa_berlaku', '<=', now())
            ->whereDate('akhir_masa_berlaku', '>=', now())
            ->with([
                'tickets' => function ($q) {
                    $q->where('code', 'LIKE', 'M%')
                        ->orderBy('created_at', 'desc');
                }
            ])
            ->get();

        $totalActiveMembers = $member->count();

        return response()->json([
            'status' => 'success',
            'total_active_members' => $totalActiveMembers,
            'data' => $member,
        ]);
    }


    public function nonActiveMemberStatusTicket()
    {
        $nomer_phone = '082318941895';

        $customer = Customer::where('phone', $nomer_phone)->first();

        $ticket_member = Ticket::where('customer_id', $customer->id)
            ->where('code', 'LIKE', 'M%')
            ->get();

        // dd($customer, $ticket_member);

        // Alur Non-Aktifkan tiket 
        foreach ($ticket_member as $ticket) {
            $ticket->is_active = 0;
            $ticket->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Tickets have been deactivated successfully.',
            'data' => $ticket_member,
        ]);
    }

    public function repairActiveMember()
    {
        $nomer_phone = '082318941895';
        $customerMember = Customer::where('phone', $nomer_phone)
            ->where('awal_masa_berlaku', '<=', now())
            ->whereBetween('akhir_masa_berlaku', [now()->startOfDay(), now()->endOfDay()])
            ->whereNotNull('member_id')
            ->first();

        // jika sudah dapet data member nya nah siapkan data untuk membuat ticket nya :
        /* 
            1. Cari PurchaseDetail nya yang dia beli tiket tersebut dari tiket data date_end nya paling lama
        */

        $ticketToRepair = Ticket::where('customer_id', $customerMember->id)
            ->where('code', 'LIKE', 'M%')
            ->where('is_active', 0)
            ->orderBy('date_end', 'desc')
            ->first();
        $purchaseDetail = PurchaseDetail::where('id', $ticketToRepair->purchase_detail_id)
            ->first();

        // Cari tiket type nya 
        $ticketType = TicketType::find($purchaseDetail->purchase_item_id);

        DB::beginTransaction();
        try {
            if ($ticketToRepair) {
                $ticketToRepair->is_active = 1;
                $ticketToRepair->save();
            }

            // Cek tiket Entry Lamanya 
            $ticketEnries = TicketEntry::where('ticket_id', $ticketToRepair->id)->get();
            if ($ticketEnries) {
                foreach ($ticketEnries as $entry) {
                    $entry->delete();
                }
            }

            $entries = [];

            // ✅ Entry original
            $entries[] = $this->createEntry($ticketToRepair, 1);
            if ($ticketType->qty_extra > 0) {
                for ($j = 0; $j < $ticketType->qty_extra; $j++) {
                    $entries[] = $this->createEntry($ticketToRepair, 2);
                }
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Member ticket repaired successfully.',
                'ticket' => $ticketToRepair,
                'entries' => $entries,
            ]);

        } catch (\Throwable $th) {

            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to repair member ticket: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function repairAllActiveMembers()
    {
        // 🔹 Ambil semua customer aktif
        $activeMembers = Customer::whereNotNull('member_id')
            ->whereDate('awal_masa_berlaku', '<=', now())
            ->whereDate('akhir_masa_berlaku', '>=', now())
            ->get();

        if ($activeMembers->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Tidak ada member aktif untuk diperbaiki.',
                'data' => [],
            ]);
        }

        $results = [];
        $errors = [];

        foreach ($activeMembers as $member) {
            DB::beginTransaction();

            try {
                // 🔹 Nonaktifkan semua tiket aktif
                $tickets = Ticket::where('customer_id', $member->id)
                    ->where('code', 'LIKE', 'M%')
                    ->get();

                foreach ($tickets as $ticket) {
                    $ticket->is_active = 0;
                    $ticket->save();
                }

                // 🔹 Cari tiket terakhir yang akan diperbaiki
                $ticketToRepair = Ticket::where('customer_id', $member->id)
                    ->where('code', 'LIKE', 'M%')
                    ->where('is_active', 0)
                    ->orderBy('date_end', 'desc')
                    ->first();

                if (!$ticketToRepair) {
                    DB::rollBack();
                    $errors[] = [
                        'member_id' => $member->id,
                        'reason' => 'Tidak ditemukan tiket M%',
                    ];
                    continue;
                }

                // 🔹 Ambil PurchaseDetail & TicketType
                $purchaseDetail = PurchaseDetail::find($ticketToRepair->purchase_detail_id);
                if (!$purchaseDetail) {
                    DB::rollBack();
                    $errors[] = [
                        'member_id' => $member->id,
                        'reason' => 'PurchaseDetail tidak ditemukan',
                    ];
                    continue;
                }

                $ticketType = TicketType::find($purchaseDetail->purchase_item_id);
                if (!$ticketType) {
                    DB::rollBack();
                    $errors[] = [
                        'member_id' => $member->id,
                        'reason' => 'TicketType tidak ditemukan',
                    ];
                    continue;
                }

                // 🔹 Aktifkan kembali tiket yang dipilih
                $ticketToRepair->is_active = 1;
                $ticketToRepair->save();

                // 🔹 Hapus semua entries lama
                TicketEntry::where('ticket_id', $ticketToRepair->id)->delete();

                // 🔹 Buat ulang entries baru
                $entries = [];
                $entries[] = $this->createEntry($ticketToRepair, 1);

                if ($ticketType->qty_extra > 0) {
                    for ($j = 0; $j < $ticketType->qty_extra; $j++) {
                        $entries[] = $this->createEntry($ticketToRepair, 2);
                    }
                }

                DB::commit();

                $results[] = [
                    'member_id' => $member->id,
                    'member_name' => $member->name,
                    'ticket_id' => $ticketToRepair->id,
                    'entries_created' => count($entries),
                ];

            } catch (\Throwable $e) {
                DB::rollBack();
                $errors[] = [
                    'member_id' => $member->id,
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Repair selesai dijalankan untuk semua member aktif.',
            'total_repaired' => count($results),
            'total_failed' => count($errors),
            'results' => $results,
            'errors' => $errors,
        ]);
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



