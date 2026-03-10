<?php

namespace App\Http\Controllers\Back\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethodType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PaymentMethodTypeController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->input('name');
        $query = PaymentMethodType::query();

        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        $paymentTypes = $query->get();

        return view('back.management_payment.index_types_payment', compact('paymentTypes'));
    }

    public function getPaymentMethodTypes($id)
    {
        $paymentTypes = PaymentMethodType::findOrFail($id);
        return response()->json($paymentTypes);
    }

    public function add(Request $request)
    {
        try {
            DB::beginTransaction();

            $name = $request->input('name');
            // buatkan alur generate slug
            $slug = Str::slug($name);
            // cek apakah slug sudah ada
            $existingPaymentMethodType = PaymentMethodType::where('slug', $slug)->first();
            if ($existingPaymentMethodType) {
                throw new \Exception('Payment Method Type name already exists');
            }
            // buat payment method type
            PaymentMethodType::create([
                'name' => $name,
                'slug' => $slug,
            ]);

            DB::commit();

            return redirect()->route('payment-types')->with([
                'success' => true,
                'action' => 'add',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('payment-types')->with([
                'success' => false,
                'action' => 'add',
            ]);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $request->validate(
                [
                    'name' => 'required|string|max:255',
                ],
                [
                    'name.required' => 'Payment Method Type name is required',
                    'name.string' => 'Payment Method Type name must be a string',
                    'name.max' => 'Payment Method Type name may not be greater than 255 characters',
                ]
            );

            $paymentMethodType = PaymentMethodType::findOrFail($id);
            $name = $request->input('name');
            $slug = Str::slug($name);
            // cek apakah slug sudah ada
            $existingPaymentMethodType = PaymentMethodType::where('slug', $slug)->first();
            if ($existingPaymentMethodType) {
                throw new \Exception('Payment Method Type name already exists');
            }
            
            $paymentMethodType->name = $name;
            $paymentMethodType->slug = $slug;
            if(!$paymentMethodType->save()){
                throw new \Exception('Payment Method Type name already exists');
            }
            // dd($paymentMethodType);
            
            DB::commit();

            return redirect()->route('payment-types')->with([
                'success' => true,
                'action' => 'edit',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('payment-types')->with([
                'success' => false,
                'action' => 'edit',
            ]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $paymentMethodType = PaymentMethodType::findOrFail($id);
            if (!$paymentMethodType->delete()) {
                throw new \Exception('Payment Method Type name already exists');
            }

            DB::commit();

            return redirect()->route('payment-types')->with([
                'success' => true,
                'action' => 'delete',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('payment-types')->with([
                'success' => false,
                'action' => 'delete',
            ]);
        }
    }
}
