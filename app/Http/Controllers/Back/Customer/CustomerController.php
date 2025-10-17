<?php

namespace App\Http\Controllers\Back\Customer;

use App\Http\Controllers\Controller;
use App\Models\Clubhouse;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('clubhouse');


        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        $customers = $query->where('deleted_at', null)->orderBy('created_at', 'asc')->paginate(10);
        $clubhouses = Clubhouse::all();

        return view('back.customer.index', compact('customers', 'clubhouses'));
    }

    public function add(Request $request)
    {
        $validatedFields = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:customers,phone',
            'dob' => 'nullable|date',
            'tipe_customer' => 'required|in:1,2,3',
            'kategory_customer' => 'required|in:1,2,3',
            'id_club_renang' => 'required|exists:clubhouses,id',
            'catatan' => 'nullable|string|max:255',
            'awal_masa_berlaku' => 'nullable|date|required_if:kategory_customer,2',
            'akhir_masa_berlaku' => 'nullable|date|required_if:kategory_customer,2',
        ], [
            'phone.unique' => 'Nomor telepon ini sudah terdaftar!',
        ]);

        try {
            DB::beginTransaction();

            $customer = new Customer();

            if ($request->kategory_customer == 2) {
                $customer->is_pelatih = 1;
                $customer->awal_masa_berlaku = $validatedFields['awal_masa_berlaku'];
                $customer->akhir_masa_berlaku = $validatedFields['akhir_masa_berlaku'];
            } else {
                $customer->is_pelatih = 0;
            }

            $customer->name = $validatedFields['name'];
            $customer->phone = $validatedFields['phone'];
            $customer->dob = $validatedFields['dob'];
            $customer->tipe_customer = $validatedFields['tipe_customer'];
            $customer->kategory_customer = $validatedFields['kategory_customer'];
            $customer->id_club_renang = $validatedFields['id_club_renang'];
            $customer->clubhouse_id = $validatedFields['id_club_renang'];
            $customer->catatan = $validatedFields['catatan'];
            $customer->save();

            DB::commit();

            return redirect()->route('customer')->with([
                'success' => true,
                'action' => 'add'
            ]);


        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('customer')->with([
                'success' => false,
                'action' => 'add'
            ]);
        }
    }


    public function getCustomer($id)
    {
        $customer = Customer::find($id);
        $clubhouses = Clubhouse::all();

        return response()->json([
            'customer' => $customer,
            'clubhouses' => $clubhouses
        ]);
    }

    public function edit(Request $request, $id)
    {
        $validatedFields = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:customers,phone',
            'dob' => 'nullable|date',
            'tipe_customer' => 'required|in:1,2,3',
            'kategory_customer' => 'required|in:1,2,3',
            'id_club_renang' => 'required|exists:clubhouses,id',
            'catatan' => 'nullable|string|max:255',
            'awal_masa_berlaku' => 'nullable|date|required_if:kategory_customer,2',
            'akhir_masa_berlaku' => 'nullable|date|required_if:kategory_customer,2',
        ], [
            'phone.unique' => 'Nomor telepon ini sudah terdaftar!',
        ]);

        try {
            DB::beginTransaction();

            $customer = Customer::find($id);

            if ($request->kategory_customer == 2) {
                $customer->is_pelatih = 1;
                $customer->awal_masa_berlaku = $validatedFields['awal_masa_berlaku'];
                $customer->akhir_masa_berlaku = $validatedFields['akhir_masa_berlaku'];
            } else {
                $customer->is_pelatih = 0;
            }

            $customer->name = $validatedFields['name'];
            $customer->phone = $validatedFields['phone'];
            $customer->dob = $validatedFields['dob'];
            $customer->tipe_customer = $validatedFields['tipe_customer'];
            $customer->kategory_customer = $validatedFields['kategory_customer'];
            $customer->id_club_renang = $validatedFields['id_club_renang'];
            $customer->catatan = $validatedFields['catatan'];
            $customer->save();

            DB::commit();

            return redirect()->route('customer')->with([
                'success' => true,
                'action' => 'edit'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('customer')->with([
                'success' => false,
                'action' => 'edit'
            ]);
        }
    }

    public function delete($id)
    {
        $customer = Customer::find($id);
        $customer->delete();

        return redirect()->route('customer')->with([
            'success' => true,
            'action' => 'delete'
        ]);
    }
}
