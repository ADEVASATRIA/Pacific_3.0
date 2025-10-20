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
            throw new \InvalidArgumentException('Silakan pilih tiket terlebih dahulu.');
        }

        $customerId = $request->input('customer_id');

        $member = Customer::where('id', $customerId)
            ->whereNotNull('member_id')
            ->whereNotNull('awal_masa_berlaku')
            ->whereNotNull('akhir_masa_berlaku')
            ->whereNull('deleted_at')
            ->first();

        // ✅ 1. Ambil semua ticket type dari item yang dipilih
        $ticketTypeIds = $items->pluck('id')->toArray();
        $ticketTypes = TicketType::whereIn('id', $ticketTypeIds)->get();

        // ✅ 2. Cek apakah user membeli lebih dari 1 jenis tiket member
        $memberTickets = $ticketTypes->where('tipe_khusus', 4);

        if ($memberTickets->count() > 1) {
            throw new \Exception(
                'Anda tidak diperkenankan membeli tiket member dengan jenis yang berbeda.'
            );
        }

        // ✅ 3. Jika user sudah punya tiket member aktif, larang pembelian ulang
        if ($member) {
            $ticketMemberExists = Ticket::where('customer_id', $member->id)
                ->where('is_active', 1)
                ->where('code', 'like', 'M%')
                ->where('date_start', '<=', now())
                ->where('date_end', '>=', now())
                ->exists();

            if ($ticketMemberExists) {
                $hasMemberTicket = $ticketTypes->contains(fn($t) => $t->tipe_khusus == 4);

                if ($hasMemberTicket) {
                    throw new \Exception(
                        'Anda masih memiliki tiket member aktif, silakan gunakan terlebih dahulu sebelum membeli kembali.'
                    );
                }
            } else {
                // Nonaktifkan tiket member lama yang sudah expired
                Ticket::where('customer_id', $member->id)
                    ->where('is_active', 1)
                    ->where('code', 'like', 'M%')
                    ->where('date_end', '<', now())
                    ->update(['is_active' => 0]);
            }
        }

        // ✅ 4. Hitung subtotal, pajak, dan total
        $subTotal = $items->sum(fn($i) => $i['qty'] * $i['price']);
        $tax = intval(round($subTotal * 0.1));
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
                $packageComboDetail = PackageComboDetail::where('package_combo_id', $id)->first();
                if (!$row) {
                    throw new \Exception("Package #{$id} tidak ditemukan.");
                }
                $price = intval($row->price);
                $name = $row->name;
                $qty_extra = $packageComboDetail->qty_extra ?? 0;
            }

            // dd(
            //     $type,
            //     $id,
            //     $name,
            //     $qty,
            //     $price,
            //     $type === 1 ? $id : null,
            //     $type === 3 ? $id : null,
            //     $type === 2 ? $id : null,
            //     $qty_extra,
            //     $ticket_kode_ref,
            // );

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

            // 🔢 Generate invoice berdasarkan tanggal + time (unik)
            do {
                $invoice = date('Ymd') . time();
            } while (Purchase::where('invoice_no', $invoice)->exists());

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
            $purchase->load('purchaseDetails.ticketType', 'purchaseDetails.packageCombo');

            foreach ($purchase->purchaseDetails as $pd) {
                if ($pd->type == 1 && $pd->ticketType) {
                    if ($pd->ticketType->tipe_khusus == 1) {
                        $ticketService->createTicketRegular($pd);
                    } elseif ($pd->ticketType->tipe_khusus == 4) {
                        $ticketService->createTicketMember($pd);
                    }
                } elseif ($pd->type == 3 && $pd->packageCombo && $pd->packageCombo->tipe_khusus == 1) {
                    $ticketService->createTicketPackage($pd);
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