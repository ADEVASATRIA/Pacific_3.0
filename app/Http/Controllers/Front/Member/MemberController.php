<?php

namespace App\Http\Controllers\Front\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
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
            ->whereHas('tiketTerbaru', function ($q) use ($today) {
                $q->whereDate('date_end', '>=', $today)
                    ->where('code', 'LIKE', 'M%');
            })
            ->with([
                'tiketTerbaru',
                'tickets' => fn($q) => $q->where('code', 'LIKE', 'M%')->orderBy('created_at', 'desc'),
            ])
            ->first();

        if ($customer) {
            return redirect()->route('member.print_member', ['customerID' => $customer->id]);
        } else {
            return redirect()->back()->with('error', 'Data Member tidak ditemukan.');
        }
    }

}
