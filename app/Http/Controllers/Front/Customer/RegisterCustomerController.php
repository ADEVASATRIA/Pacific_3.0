<?php

namespace App\Http\Controllers\Front\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class RegisterCustomerController extends Controller
{
    public function registerDataCustomer(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|regex:/^08[1-9][0-9]{6,10}$/',
            'dob' => 'required|date',
            'type_customer' => 'required|in:laki-laki,wanita,anak-anak',
            'category_customer' => 'required|in:biasa,private,coach',
            'clubhouse_id' => 'nullable|exists:clubhouses,id',
            'catatan' => 'nullable|string|max:500',
        ]);

        $customer = Customer::create($validatedData);

        return redirect()->route('index_ticket', ['customer' => $customer->id])
            ->with('success', 'Data customer berhasil disimpan.');

    }
}
