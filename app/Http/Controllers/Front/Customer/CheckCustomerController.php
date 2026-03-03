<?php

namespace App\Http\Controllers\Front\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CheckCustomerController extends Controller
{
    public function checkCustomer(Request $request)
    {
        $validatedData = $request->validate([
            'phone' => 'required|regex:/^08[1-9][0-9]{6,10}$/',
            'name' => 'required|string|max:255',
        ]);

        $customer = Customer::where('phone', $validatedData['phone'])->first();

        if ($customer) {
            return redirect()->route('index_ticket', ['customer' => $customer->id]);
        }

        return redirect()
            ->back()
            ->withInput($validatedData)
            ->with('alert_type', 'error')
            ->with('alert_message', 'User masih belum ada , dan dilanjutkan ke halaman register')
            ->with('redirect_to_register', true);
    }
}
