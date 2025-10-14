<?php

namespace App\Http\Controllers\Back\Tickets;

use App\Http\Controllers\Controller;
use App\Models\PackageCombo;
use App\Models\PackageComboDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageComboController extends Controller
{
    public function index(Request $request)
    {
        $isActive = $request->input('is_active');

        $query = PackageCombo::query();

        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', (bool) $isActive);
        }

        $packageCombos = $query->where('deleted_at', null)->orderBy('created_at', 'asc')->paginate(10);
        return view('back.tickets.index_package', compact('packageCombos'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:1',
            'expired_duration' => 'required|integer|min:1',
            'is_active' => 'required|integer',
            'tempQty' => 'required|integer|min:1',
            'item_id' => 'required|integer|min:1',
        ]);

        if ($request->item_id == 1) {
            $request->merge(['qty_extra' => 0]);
        } else if ($request->item_id == 2) {
            $request->merge(['qty_extra' => 1]);
        }
        try {
            DB::beginTransaction();

            $packageCombo = new PackageCombo();
            $packageCombo->name = $request->name;
            $packageCombo->price = $request->price;
            $packageCombo->expired_duration = $request->expired_duration;
            $packageCombo->is_active = $request->is_active;
            $packageCombo->save();

            $comboDetail = new PackageComboDetail();
            $comboDetail->package_combo_id = $packageCombo->id;
            $comboDetail->type = 1;
            $comboDetail->item_id = $request->item_id;
            $comboDetail->qty = $request->tempQty;
            $comboDetail->qty_extra = $request->qty_extra;
            $comboDetail->save();

            DB::commit();

            return redirect()->route('package-combo')->with([
                'success' => true,
                'action' => 'add'
            ]);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('package-combo')->with([
                'success' => false,
                'action' => 'add'
            ]);
        }
    }

    public function getPackageCombo($id)
    {
        $packageCombo = PackageCombo::with('details')->findOrFail($id);
        return response()->json($packageCombo);
    }


    public function edit(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:1',
            'expired_duration' => 'required|integer|min:1',
            'is_active' => 'required|integer',
            'tempQty' => 'required|integer|min:1',
            'item_id' => 'required|integer|min:1',
        ]);

        if ($request->item_id == 1) {
            $request->merge(['qty_extra' => 0]);
        } else if ($request->item_id == 2) {
            $request->merge(['qty_extra' => 1]);
        }

        try {
            DB::beginTransaction();

            $packageCombo = PackageCombo::findOrFail($id);
            $packageCombo->name = $request->name;
            $packageCombo->price = $request->price;
            $packageCombo->expired_duration = $request->expired_duration;
            $packageCombo->is_active = $request->is_active;
            $packageCombo->save();

            $comboDetail = PackageComboDetail::where('package_combo_id', $id)->first();
            $comboDetail->type = 1;
            $comboDetail->item_id = $request->item_id;
            $comboDetail->qty = $request->tempQty;
            $comboDetail->qty_extra = $request->qty_extra;
            $comboDetail->save();

            DB::commit();

            return redirect()->route('package-combo')->with([
                'success' => true,
                'action' => 'edit'
            ]);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('package-combo')->with([
                'success' => false,
                'action' => 'edit'
            ]);
        }
    }

    public function delete($id)
    {
        $packageCombo = PackageCombo::findOrFail($id);
        $packageCombo->delete();
        return redirect()->route('package-combo')->with([
            'success' => true,
            'action' => 'delete'
        ]);
    }
}
