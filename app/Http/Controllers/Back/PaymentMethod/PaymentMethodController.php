<?php

namespace App\Http\Controllers\Back\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    // Alur management Payment Method
    public function index(Request $request)
    {
        // Filter nama payment method
        $name = $request->input('name');
        $query = PaymentMethod::query();

        // Query all type
        $paymentMethodTypes = PaymentMethodType::all();

        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        $paymentMethods = $query->get();

        return view('back.management_payment.index', compact('paymentMethods', 'paymentMethodTypes'));
    }

    public function getPaymentMethod($id){
        $paymentMethod = PaymentMethod::findOrFail($id);
        return response()->json($paymentMethod);
    }

    public function add(Request $request){
        try {
            DB::beginTransaction();

            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'payment_method_type_id' => 'required|string|max:255',
                    'provider' => 'required|string|max:255',
                    'is_active' => 'required|boolean',
                    'is_approval_code_required' => 'required|boolean',
                    'is_number_card_required' => 'required|boolean'
                ],
                [
                    'name.required' => 'Nama payment method harus diisi!',
                    'image.required' => 'Gambar payment method harus diisi!',
                    'payment_method_type_id.required' => 'Tipe payment method harus diisi!',
                    'provider.required' => 'Provider payment method harus diisi!',
                    'is_active.required' => 'Status payment method harus diisi!',
                    'is_approval_code_required.required' => 'Req Approval Code harus diisi!',
                    'is_number_card_required.required' => 'Req Number Card harus diisi!'
                ]
            );

            $imagePath = null;
            if ($request->hasFile('image')) {
                // Menggunakan Storage::disk('public')->putFile untuk kepastian path
                // Path akan disimpan sebagai 'payment_methods/namafile.jpg' di dalam disk 'public'
                $imagePath = $request->file('image')->store('payment_methods', 'public');
            }

            $paymentMethod = new PaymentMethod();
            $paymentMethod->name = $request->name;
            $paymentMethod->img_thumbnail = $imagePath;
            $paymentMethod->payment_method_type_id = $request->payment_method_type_id;
            $paymentMethod->provider = $request->provider;
            $paymentMethod->is_active = $request->is_active;
            $paymentMethod->is_approval_code_required = $request->is_approval_code_required;
            $paymentMethod->is_number_card_required = $request->is_number_card_required;

            if (!$paymentMethod->save()) {
                throw new \Exception("Gagal menyimpan data Payment Method.");
            }

            DB::commit();
            return redirect()->route('payment-method')->with([
                'success' => true,
                'action' => 'add'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());    
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            // dd($request->all());

            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'type' => 'required|string|max:255',
                    'provider' => 'required|string|max:255',
                    'is_active' => 'required|boolean',
                    'is_approval_code_required' => 'required|boolean',
                    'is_number_card_required' => 'required|boolean'
                ],
                [
                    'name.required' => 'Nama payment method harus diisi!',
                    'image.required' => 'Gambar payment method harus diisi!',
                    'type.required' => 'Tipe payment method harus diisi!',
                    'provider.required' => 'Provider payment method harus diisi!',
                    'is_active.required' => 'Status payment method harus diisi!',
                    'is_approval_code_required.required' => 'Req Approval Code harus diisi!',
                    'is_number_card_required.required' => 'Req Number Card harus diisi!'
                ]
            );

            $imagePath = null;
            if ($request->hasFile('image')) {
                // Menggunakan Storage::disk('public')->putFile untuk kepastian path
                // Path akan disimpan sebagai 'payment_methods/namafile.jpg' di dalam disk 'public'
                $imagePath = $request->file('image')->store('payment_methods', 'public');
            }

            $paymentMethod = PaymentMethod::find($id);
            $paymentMethod->name = $request->name;
            if ($request->hasFile('image')) {
                $paymentMethod->img_thumbnail = $imagePath;
            }
            $paymentMethod->payment_method_type_id = $request->type;
            $paymentMethod->provider = $request->provider;
            $paymentMethod->is_active = $request->is_active;
            $paymentMethod->is_approval_code_required = $request->is_approval_code_required;
            $paymentMethod->is_number_card_required = $request->is_number_card_required;

            if (!$paymentMethod->save()) {
                throw new \Exception("Gagal menyimpan data Payment Method.");
            }

            DB::commit();
            return redirect()->route('payment-method')->with([
                'success' => true,
                'action' => 'edit'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());    
        }
    }

    public function delete($id){
        try {
            DB::beginTransaction();

            $paymentMethod = PaymentMethod::find($id);

            if (!$paymentMethod->delete()) {
                throw new \Exception("Gagal menghapus data Payment Method.");
            }

            DB::commit();
            return redirect()->route('payment-method')->with([
                'success' => true,
                'action' => 'delete'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());    
        }
    }
}
