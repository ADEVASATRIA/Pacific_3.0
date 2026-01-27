<?php

namespace App\Http\Controllers\Back\Tickets;

use App\Http\Controllers\Controller;
use App\Models\PackageCombo;
use App\Models\PackageComboDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


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
            'weight' => 'integer',
            'price' => 'required|integer|min:1',
            'expired_duration' => 'nullable|integer|min:1',
            'is_active' => 'required|integer',
            'tempQty' => 'required|integer|min:1',
            'item_id' => 'required|integer|min:1',

            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Validasi Custom: Salah satu harus diisi (Duration atau Date Range)
        if (empty($request->expired_duration) && (empty($request->start_date) || empty($request->end_date))) {
            return redirect()->back()->withErrors(['expired_duration' => 'Harap isi Durasi atau Tanggal Mulai & Akhir'])->withInput();
        }

        // ===== ITEM LOGIC =====
        if ($request->item_id == 1) {
            $request->merge(['qty_extra' => 0]);
        } elseif ($request->item_id == 2) {
            $request->merge(['qty_extra' => 1]);
        }

        // ===== EXPIRED DURATION LOGIC =====
        $expiredDuration = $request->expired_duration;

        if (
            empty($expiredDuration) &&
            $request->filled('start_date') &&
            $request->filled('end_date')
        ) {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);

            // +1 jika mau inclusive (misal 1–7 = 7 hari)
            $expiredDuration = $start->diffInDays($end);
        }

        try {
            DB::beginTransaction();

            $packageCombo = new PackageCombo();
            $packageCombo->name = $request->name;
            $packageCombo->weight = $request->weight;
            $packageCombo->price = $request->price;
            $packageCombo->expired_duration = $expiredDuration;
            $packageCombo->start_date = $request->start_date;
            $packageCombo->end_date = $request->end_date;
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


    public function edit(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'integer',
            'price' => 'required|integer|min:1',
            'expired_duration' => 'nullable|integer|min:1',
            'is_active' => 'required|integer',
            'tempQty' => 'required|integer|min:1',
            'item_id' => 'required|integer|min:1',


            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Validasi Custom: Salah satu harus diisi (Duration atau Date Range)
        if (empty($request->expired_duration) && (empty($request->start_date) || empty($request->end_date))) {
            return redirect()->back()->withErrors(['expired_duration' => 'Harap isi Durasi atau Tanggal Mulai & Akhir'])->withInput();
        }

        if ($request->item_id == 1) {
            $request->merge(['qty_extra' => 0]);
        } else if ($request->item_id == 2) {
            $request->merge(['qty_extra' => 1]);
        }

        // ===== EXPIRED DURATION LOGIC =====
        $expiredDuration = $request->expired_duration;

        if (
            empty($expiredDuration) &&
            $request->filled('start_date') &&
            $request->filled('end_date')
        ) {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);

            // +1 jika mau inclusive (misal 1–7 = 7 hari)
            $expiredDuration = $start->diffInDays($end);
        }

        try {
            DB::beginTransaction();

            $packageCombo = PackageCombo::findOrFail($id);
            $packageCombo->name = $request->name;
            $packageCombo->weight = $request->weight;
            $packageCombo->price = $request->price;
            $packageCombo->expired_duration = $expiredDuration;
            $packageCombo->start_date = $request->start_date;
            $packageCombo->end_date = $request->end_date;
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
