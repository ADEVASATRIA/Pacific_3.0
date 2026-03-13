<?php

namespace App\Http\Controllers\Back\Voucher;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Back\Voucher\VoucherLogController;

class VoucherController extends Controller
{
    public function index(Request $request){
        $name = $request->input('name');
        $isActive = $request->input('is_active');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = Voucher::query();

        if($name){
            $query->where('name', 'like', '%' . $name . '%');
        }

        // Filter status aktif / tidak aktif
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', (bool) $isActive);
        }

        // Filter tanggal mulai
        if ($startDate) {
            $query->where('start_date', '>=', $startDate);
        }

        // Filter tanggal selesai
        if ($endDate) {
            $query->where('end_date', '<=', $endDate);
        }

        $vouchers = $query->get();

        return view('back.management_voucher.index', compact('vouchers'));
    }

    public function getVoucher($id){
        $voucher = Voucher::find($id);
        return response()->json($voucher);
    }

    public function add(Request $request){
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => 'required|string|max:255',
                'requirements' => 'required|string|max:255',
                'type_voucher' => 'required|in:fixed,percent',
                'value' => 'required|numeric|min:0',
                'quota' => 'required|integer|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'min_purchase' => 'required|numeric|min:0',
                'max_discount' => 'required|numeric|min:0',
                'is_active' => 'required|boolean',
            ],
            [
                'name.required' => 'Nama voucher harus diisi.',
                'requirements.required' => 'Persyaratan voucher harus diisi.',
                'type_voucher.required' => 'Tipe voucher harus diisi.',
                'value.required' => 'Nilai voucher harus diisi.',
                'quota.required' => 'Kuota voucher harus diisi.',
                'start_date.required' => 'Tanggal mulai voucher harus diisi.',
                'end_date.required' => 'Tanggal selesai voucher harus diisi.',
                'min_purchase.required' => 'Pembelian minimum voucher harus diisi.',
                'max_discount.required' => 'Diskon maksimum voucher harus diisi.',
                'is_active.required' => 'Status voucher harus diisi.',
            ]);

            // Alur Simpan Data
            $voucher = new Voucher();
            $voucher->name = $request->input('name');
            $voucher->requirements = $request->input('requirements');
            $voucher->type_voucher = $request->input('type_voucher');
            $voucher->value = $request->input('value');
            $voucher->quota = $request->input('quota');
            $voucher->start_date = $request->input('start_date');
            $voucher->end_date = $request->input('end_date');
            $voucher->min_purchase = $request->input('min_purchase');
            $voucher->max_discount = $request->input('max_discount');
            $voucher->is_active = $request->input('is_active');
            
            if(!$voucher->save()){
                throw new \Exception('Gagal menyimpan data voucher.');
            }

            // Generate log voucher
            VoucherLogController::generateLogs($voucher);

            DB::commit();
            return redirect()->route('voucher')->with([
                'success' => true,
                'action' => 'add',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('voucher')->with([
                'success' => false,
                'action' => 'add',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function edit(Request $request, $id){
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => 'required|string|max:255',
                'requirements' => 'required|string|max:255',
                'type_voucher' => 'required|in:fixed,percent',
                'value' => 'required|numeric|min:0',
                'quota' => 'required|integer|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'min_purchase' => 'required|numeric|min:0',
                'max_discount' => 'required|numeric|min:0',
                'is_active' => 'required|boolean',
            ],
            [
                'name.required' => 'Nama voucher harus diisi.',
                'requirements.required' => 'Persyaratan voucher harus diisi.',
                'type_voucher.required' => 'Tipe voucher harus diisi.',
                'value.required' => 'Nilai voucher harus diisi.',
                'quota.required' => 'Kuota voucher harus diisi.',
                'start_date.required' => 'Tanggal mulai voucher harus diisi.',
                'end_date.required' => 'Tanggal selesai voucher harus diisi.',
                'min_purchase.required' => 'Pembelian minimum voucher harus diisi.',
                'max_discount.required' => 'Diskon maksimum voucher harus diisi.',
                'is_active.required' => 'Status voucher harus diisi.',
            ]);

            // Alur Edit Data
            $voucher = Voucher::find($id);
            if(!$voucher){
                throw new \Exception('Voucher tidak ditemukan.');
            }
            $voucher->name = $request->input('name');
            $voucher->requirements = $request->input('requirements');
            $voucher->type_voucher = $request->input('type_voucher');
            $voucher->value = $request->input('value');
            $voucher->quota = $request->input('quota');
            $voucher->start_date = $request->input('start_date');
            $voucher->end_date = $request->input('end_date');
            $voucher->min_purchase = $request->input('min_purchase');
            $voucher->max_discount = $request->input('max_discount');
            $voucher->is_active = $request->input('is_active');

            if(!$voucher->save()){
                throw new \Exception('Gagal menyimpan data voucher.');
            }
            
            VoucherLogController::generateLogs($voucher);
            
            DB::commit();
            return redirect()->route('voucher')->with([
                'success' => true,
                'action' => 'edit',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('voucher')->with([
                'success' => false,
                'action' => 'edit',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete($id){
        try {
            DB::beginTransaction();
            $voucher = Voucher::find($id);
            if(!$voucher){
                throw new \Exception('Voucher tidak ditemukan.');
            }
            if(!$voucher->delete()){
                throw new \Exception('Gagal menghapus data voucher.');
            }
            DB::commit();
            return redirect()->route('voucher')->with([
                'success' => true,
                'action' => 'delete',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('voucher')->with([
                'success' => false,
                'action' => 'delete',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
