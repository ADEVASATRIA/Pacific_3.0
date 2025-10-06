<?php

namespace App\Services\Member;



use Illuminate\Http\Request;

use App\Models\TicketType;
use App\Models\Customer;

use App\Models\Ticket;


class ExtendsService
{
    public function prepareCheckoutData(Request $request)
    {
        // dd('masuk extends service');
        $tickets = collect($request->input('tickets', []))
            ->map(fn($t) => [
                'id' => intval($t['id'] ?? 0),
                'name' => $t['name'] ?? '',
                'price' => intval($t['price'] ?? 0),
                'qty' => intval($t['qty'] ?? 0),
                'type_purchase' => intval($t['type_purchase'] ?? 1), // default regular
            ])
            ->filter(fn($t) => $t['qty'] > 0);

        $items = $tickets->values();

        if ($items->isEmpty()) {
            // jangan return redirect di sini — lempar exception
            throw new \InvalidArgumentException('Silakan pilih tiket terlebih dahulu.');
        }
        $customerId = $request->input('customer_id');

        $member = Customer::where('id', $customerId)
            ->whereNotNull('member_id')
            ->whereNotNull('awal_masa_berlaku')
            ->whereNotNull('akhir_masa_berlaku')
            ->whereNull('deleted_at')
            ->first();

        if ($member) {
            $ticketMemberExists = Ticket::where('customer_id', $member->id)
                ->where('is_active', 1)
                ->where('code', 'like', 'M%')
                ->where('date_start', '<=', now())
                ->where('date_end', '>=', now())
                ->get();
            foreach($ticketMemberExists as $tm){
                $tm->is_active = 0;
                $tm->save();
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
}