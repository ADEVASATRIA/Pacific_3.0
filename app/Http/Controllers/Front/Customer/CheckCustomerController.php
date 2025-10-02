<?php

namespace App\Http\Controllers\Front\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Clubhouse;

class CheckCustomerController extends Controller
{
    public function checkCustomer(Request $request)
        {
            $validatedData = $request->validate([
                'phone' => 'required|regex:/^08[1-9][0-9]{6,10}$/',
                'name' => 'nullable|string|max:255',
            ]);
    
            // dd($validatedData);
    
            $customer = Customer::where('phone', $validatedData['phone'])->first();
            $clubhouses = Clubhouse::all();
    
            if ($customer) {
                return redirect()->route('index_ticket', ['customer' => $customer->id]);
            } else {
                return redirect()->route('registrasi_new_customer', ['clubhouses' => $clubhouses]);
            }
        }
}
