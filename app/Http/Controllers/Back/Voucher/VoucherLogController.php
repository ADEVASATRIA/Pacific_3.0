<?php

namespace App\Http\Controllers\Back\Voucher;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VoucherLogController extends Controller
{
    public static function generateLogs(Voucher $voucher)
    {
        // Cek apakah sudah ada log untuk voucher ini
        $hasLogs = VoucherLog::where('voucher_id', $voucher->id)->exists();

        if (!$hasLogs) {
            $data = [];
            $now = Carbon::now();

            for ($i = 0; $i < $voucher->quota; $i++) {
                $data[] = [
                    'voucher_id'  => $voucher->id,
                    'customer_id' => null,
                    'code'        => strtoupper(Str::random(17)), // Generate 17 karakter random
                    'start_at'    => $voucher->start_date,
                    'end_at'      => $voucher->end_date,
                    'is_active'   => $voucher->is_active,
                    'created_at'  => $now, // Bulk insert butuh manual timestamp
                    'updated_at'  => $now,
                ];
            }

            // Menggunakan insert() untuk efisiensi tinggi (Bulk Insert)
            if (!empty($data)) {
                VoucherLog::insert($data);
            }
        } else {
            // Logika UPDATE: Jika master diubah, log yang sudah ada ikut terupdate
            VoucherLog::where('voucher_id', $voucher->id)->update([
                'start_at'  => $voucher->start_date,
                'end_at'    => $voucher->end_date,
                'is_active' => $voucher->is_active,
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    // Function Index Voucher Log page
    public function indexLog(Request $request, $id)
    {
        // Menggunakan query builder agar bisa menambah filter secara dinamis
        $query = VoucherLog::with(['customer', 'voucher']) // Eager loading agar tidak N+1 query
            ->where('voucher_id', $id);

        // Filter berdasarkan Nama Customer (melalui relasi)
        if ($request->filled('name')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        // Filter berdasarkan Code Voucher Log
        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }

        // Filter berdasarkan Status (is_active)
        // Menggunakan request()->has() karena nilai '0' sering dianggap empty/false
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Eksekusi query dengan pagination atau get
        $voucherLog = $query->latest()->get();

        return view('back.management_voucher.indexVoucherLog', compact('voucherLog', 'id'));
    }
}
