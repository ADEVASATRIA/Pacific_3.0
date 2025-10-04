<?php

namespace App\Http\Controllers\Front\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class RegisterCustomerController extends Controller
{
    public function registerDataCustomer(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|regex:/^08[1-9][0-9]{6,10}$/',
                'dob' => 'required|date',
                'type_customer' => 'required|in:1,2,3',
                'category_customer' => 'required|in:1,2,3',
                'clubhouse_id' => 'nullable|exists:clubhouses,id',
                'catatan' => 'nullable|string|max:500',
            ]);

            if (Customer::where('phone', $validatedData['phone'])->exists()) {
                throw new \Exception("Nomor telepon sudah digunakan.");
            }

            $customer = Customer::create($validatedData);

            return redirect()->route('index_ticket', ['customer' => $customer->id])
                ->with('success', 'Data customer berhasil disimpan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
