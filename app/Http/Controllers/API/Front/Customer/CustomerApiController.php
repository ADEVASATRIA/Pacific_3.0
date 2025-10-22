<?php

namespace App\Http\Controllers\API\Front\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;


class CustomerApiController extends Controller
{
    /**
     * Get all customers untuk contact book
     */
    public function getAllCustomers()
    {
        try {
            $customers = Customer::select('id', 'phone', 'name')
                ->where('deleted_at', null)
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data customer'
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
