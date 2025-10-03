<?php

namespace App\Http\Controllers\Front\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;

class AdminAuthController extends Controller
{
    public function index(){
        return view('front.admin.index');
    }

    public function checkPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string'
        ]);

        // Contoh: PIN diambil dari .env (lebih aman)
        $admin = Admin::where('pin', $request->pin)->first();

        if ($admin != null) {
            // Simpan session login admin (opsional)
            session(['is_admin_logged_in' => true]);

            return response()->json([
                'success' => true,
                'redirect' => route('admin.index') // ganti dengan route admin Anda
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'PIN salah. Coba lagi.'
        ], 401);
    }
}
