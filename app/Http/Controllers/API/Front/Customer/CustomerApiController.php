<?php

namespace App\Http\Controllers\API\Front\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;


class CustomerApiController extends Controller
{
    /**
     * Get customers untuk contact book modal.
     *
     * Mendukung parameter ?search= untuk server-side filtering nama/telepon.
     * Hasil dibatasi 100 baris untuk menjaga performa render di browser.
     * Filter deleted_at ditangani otomatis oleh SoftDeletes pada model Customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCustomers(Request $request)
    {
        try {
            $search = trim($request->query('search', ''));

            $customers = Customer::select('id', 'phone', 'name')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->orderBy('name', 'asc')
                ->limit(100)
                ->get();

            return response()->json([
                'success'   => true,
                'customers' => $customers,
                'count'     => $customers->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data customer',
            ], 500);
        }
    }

    /**
     * Search customer by phone number untuk auto-fill
     */
    public function searchByPhone(Request $request)
    {
        try {
            $phone = $request->query('phone');

            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor telephone tidak boleh kosong'
                ], 400);
            }

            $customer = Customer::where('phone', $phone)
                ->select('id', 'name', 'phone')
                ->first();

            if ($customer) {
                return response()->json([
                    'success' => true,
                    'customer' => $customer
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer tidak ditemukan'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari customer'
            ], 500);
        }
    }
}
