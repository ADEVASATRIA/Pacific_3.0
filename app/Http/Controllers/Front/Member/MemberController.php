<?php

namespace App\Http\Controllers\Front\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\TicketType;
use Carbon\Carbon;

class MemberController extends Controller
{
    public function inputMember()
    {
        return view('front.print_ticket.member.input-member');
    }
    public function checkMember(Request $request)
    {
        $validatedData = $request->validate([
            'phone' => [
                'required',
                'regex:/^08[1-9][0-9]{6,10}$/'
            ],
        ], [
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Format nomor telepon tidak valid. Harus diawali 08 dan panjang 9–12 digit.',
        ]);

        $today = now()->startOfDay();

        $customer = Customer::where('phone', $validatedData['phone'])
            ->whereNotNull('member_id')
            ->with([
                'tiketTerbaru' => fn($q) => $q->where('code', 'LIKE', 'M%')->latest('created_at'),
                'tickets' => fn($q) => $q->where('code', 'LIKE', 'M%')->orderBy('created_at', 'desc'),
            ])
            ->first();

        if ($customer && $customer->tiketTerbaru) {
            $ticket = $customer->tiketTerbaru;
            $dateEnd = \Carbon\Carbon::parse($ticket->date_end)->startOfDay();

            if ($dateEnd->gte($today)) {
                $diffDays = $today->diffInDays($dateEnd, false);

                // Jika H-7 → tampilkan modal renewal, tapi tetap redirect ke print member
                // Jika H-7 → tampilkan modal renewal, tapi tetap di halaman form
                if ($diffDays <= 7) {
                    return redirect()
                        ->back()
                        ->with('renewal', "Membership Anda akan habis dalam {$diffDays} hari, silakan perpanjang.")
                        ->with('customer_id', $customer->id);
                }


                // masih aktif dan lebih dari 7 hari → langsung ke print member
                return redirect()->route('member.print_member', ['customerID' => $customer->id]);
            }
        }

        // expired → modal expired
        $expired = Customer::where('phone', $validatedData['phone'])
            ->whereNotNull('member_id')
            ->first();

        if ($expired) {
            return redirect()->back()
                ->with('expired', 'Ticket Anda sudah expired')
                ->with('customer_id', $expired->id);
        }

        return redirect()->back()->with('error', 'Data Member tidak ditemukan.');
    }



    // Alur perpanjang member
    public function memberExtend(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ], [
            'customer_id.required' => 'Customer ID wajib diisi.',
            'customer_id.exists' => 'Customer tidak ditemukan.',
        ]);

        $customer = Customer::find($validatedData['customer_id']);

        // Disini kamu bisa langsung proses perpanjangan membership
        // Misal generate tiket baru atau update masa berlaku
        if ($customer) {
            return redirect()->route('member.list-ticket-member', ['customer' => $customer->id]);
        } else {
            return redirect()->route('main')->with('error', 'Gagal memperpanjang membership. Silakan coba lagi.');
        }
    }

    public function indexExtendMember(Request $request)
    {
        $customerId = $request->query('customer', null);
        $filterType = $request->query('filter_type', null);

        // Ticket Member
        $ticketMember = $filterType == 4 || $filterType == null
            ? TicketType::where('tipe_khusus', 4)->where('is_active', 1)->where('deleted_at', null)->get() : collect();

        $customer = $customerId ? Customer::find($customerId) : null;

        return view('front.print_ticket.member.ticket_view_perpanjang', compact([
            'ticketMember',
            'customer',
            'customerId',
            'filterType'
        ]));
    }


}
