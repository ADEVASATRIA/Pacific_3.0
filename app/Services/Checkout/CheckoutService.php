<?php

namespace App\Services\Checkout;

use App\Services\Tickets\CreateTickets;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\TicketType;
use App\Models\PackageCombo;
use App\Models\PackageComboDetail;
use App\Models\PackageComboRedeem;
use App\Models\PackageComboRedeemDetail;
use App\Models\Customer;

use App\Models\Admin;
use App\Models\Ticket;


class CheckoutService
{
    public function prepareCheckoutData(Request $request)
    {
        $tickets = collect($request->input('tickets', []))
            ->map(fn($t) => [
                'id' => intval($t['id'] ?? 0),
                'name' => $t['name'] ?? '',
                'price' => intval($t['price'] ?? 0),
                'qty' => intval($t['qty'] ?? 0),
                'type_purchase' => intval($t['type_purchase'] ?? 1), // default regular
            ])
            ->filter(fn($t) => $t['qty'] > 0);

        $packages = collect($request->input('packages', []))
            ->map(fn($p) => [
                'id' => intval($p['id'] ?? 0),
                'name' => $p['name'] ?? '',
                'price' => intval($p['price'] ?? 0),
                'qty' => intval($p['qty'] ?? 0),
                'type_purchase' => intval($p['type_purchase'] ?? 3), // default package
            ])
            ->filter(fn($p) => $p['qty'] > 0);

        $items = $tickets->concat($packages)->values();

        if ($items->isEmpty()) {
            // jangan return redirect di sini — lempar exception
            throw new \InvalidArgumentException('Silakan pilih tiket terlebih dahulu.');
        }
        $customerId = $request->input('customer_id');


        $member = Customer::where('id', $customerId)
            ->whereNotNull('member_id')
            ->whereNotNull('awal_masa_berlaku')
            ->whereNotNull('akhir_masa_berlaku')
            ->whereNull('deleted_at') // cek yang masih aktif
            ->first();

        if ($member) {
            // 🔹 Cek apakah ada tiket member yang masih aktif dan berlaku
            $ticketMemberExists = Ticket::where('customer_id', $member->id)
                ->where('is_active', 1)
                ->where('code', 'like', 'M%')
                ->where('date_start', '<=', now())
                ->where('date_end', '>=', now())
                ->exists();

            if ($ticketMemberExists) {
                // Jika masih ada tiket aktif → cek apakah mau beli tiket member lagi
                $ticketTypeIds = $items->pluck('id')->toArray();
                $ticketTypes   = TicketType::whereIn('id', $ticketTypeIds)->get();

                $hasMemberTicket = $ticketTypes->contains(fn($t) => $t->tipe_khusus == 4);

                if ($hasMemberTicket) {
                    throw new \Exception(
                        'Anda masih memiliki tiket member aktif, silakan gunakan terlebih dahulu sebelum membeli kembali.'
                    );
                }
            } else {
                // 🔹 Nonaktifkan tiket lama yang sudah expired tapi statusnya masih active
                Ticket::where('customer_id', $member->id)
                    ->where('is_active', 1)
                    ->where('code', 'like', 'M%')
                    ->where('date_end', '<', now()) // hanya expired yg di-nonaktifkan
                    ->update(['is_active' => 0]);
            }
        }


        $subTotal = $items->sum(fn($i) => $i['qty'] * $i['price']);

        // simpan pajak (10% dari subtotal) untuk keperluan laporan
        $tax = intval(round($subTotal * 0.1));

        // total = subtotal (tidak ditambah pajak lagi)
        $total = $subTotal;

        return [
            'items' => $items,
            'subTotal' => $subTotal,
            'tax' => $tax,
            'total' => $total,
            'customer_id' => $customerId,
        ];
    }

    public function processCheckout(Request $request)
    {
        $items = collect($request->input('items', []));
        if ($items->isEmpty()) {
            throw new \Exception('Tidak ada item untuk disimpan.');
        }

        // 🔹 Mapping Items
        $preparedItems = $items->map(function ($it) {
            $type = intval($it['type_purchase'] ?? 1);
            $id = intval($it['id'] ?? 0);
            $qty = intval($it['qty'] ?? 0);

            $row = null;
            $price = intval($it['price'] ?? 0);
            $name = $it['name'] ?? '';

            $qty_extra = 0;
            $ticket_kode_ref = null;

            if ($type === 1) {
                $row = TicketType::find($id);
                if (!$row) {
                    throw new \Exception("Ticket type #{$id} tidak ditemukan.");
                }
                $price = intval($row->price);
                $name = $row->name;
                $qty_extra = $row->qty_extra ?? 0;
                $ticket_kode_ref = $row->ticket_kode_ref ?? null;
            } elseif ($type === 3) {
                $row = PackageCombo::find($id);
                if (!$row) {
                    throw new \Exception("Package #{$id} tidak ditemukan.");
                }
                $price = intval($row->price);
                $name = $row->name;
            }

            return [
                'type_purchase' => $type,
                'id' => $id,
                'name' => $name,
                'qty' => $qty,
                'price' => $price,
                'ticket_type_id' => $type === 1 ? $id : null,
                'package_id' => $type === 3 ? $id : null,
                'item_id' => $type === 2 ? $id : null,
                'qty_extra' => $qty_extra,
                'ticket_kode_ref' => $ticket_kode_ref,
            ];
        });

        DB::beginTransaction();
        try {
            // 🔑 Cari staff dari PIN
            $staff = Admin::where('pin', $request->input('staff_pin'))->first();
            if (!$staff) {
                throw new \Exception('PIN Staff salah.');
            }

            // 🔢 Generate invoice
            $invoice = 'INV-' . strtoupper(Str::random(6)) . '-' . time();

            // 🛒 Buat Purchase
            $purchase = Purchase::create([
                'customer_id' => $request->input('customer_id'),
                'promo_id' => $request->input('promo_id') ?? null,
                'staff_id' => $staff->id,
                'invoice_no' => $invoice,
                'sub_total' => $request->input('sub_total'),
                'tax' => $request->input('tax'),
                'discount' => $request->input('discount') ?? 0,
                'total' => $request->input('total'),
                'kembalian' => $request->input('kembalian') ?? 0,
                'uangDiterima' => $request->input('uangDiterima') ?? 0,
                'payment' => $request->input('payment'),
                'payment_info' => $request->input('payment_info'),
                'approval_code' => $request->input('approval_code'),
                'status' => Purchase::STATUS_PAID, // default → langsung paid
            ]);
            // dd($purchase);

            // 📝 Buat Purchase Detail
            $purchaseDetails = [];
            foreach ($preparedItems as $pi) {
                $purchaseDetail = PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'type' => $pi['type_purchase'],
                    'purchase_item_id' => $pi['ticket_type_id'] ?: ($pi['package_id'] ?: $pi['item_id']),
                    'name' => $pi['name'],
                    'qty' => $pi['qty'],
                    'qty_extra' => $pi['qty_extra'],
                    'price' => $pi['price'],
                    'ticket_kode_ref' => $pi['ticket_kode_ref'],
                ]);
                $purchaseDetails[] = $purchaseDetail;
            }

            // // 🎟️ Generate ticket (kalau diaktifkan)
            $ticketService = new CreateTickets();
            $purchase->load('purchaseDetails.ticketType');
            foreach ($purchase->purchaseDetails as $pd) {
                if ($pd->type == 1 && $pd->ticketType && $pd->ticketType->tipe_khusus == 1) {
                    // dd('sini');
                    $ticketService->createTicketRegular($pd);
                } elseif ($pd->type == 1 && $pd->ticketType && $pd->ticketType->tipe_khusus == 4) {
                    // dd('masuk sini');
                    $ticketService->createTicketMember($pd);
                }
            }


            DB::commit();
            return $purchase;

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

}