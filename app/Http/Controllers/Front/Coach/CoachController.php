<?php

namespace App\Http\Controllers\Front\Coach;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\Tickets\CreateTickets;

class CoachController extends Controller
{
    public function inputCoach(){
        return view('front.print_ticket.coach.input-coach');
    }

    public function checkCoach(Request $request){
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

        $customer = Customer::where('phone', $validatedData['phone'])->first();
    }
}
