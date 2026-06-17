<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('main.login');
    }

    public function doLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = Admin::where('username', $credentials['username'])->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Login gagal, periksa username atau password/PIN.']);
        }

        // 1. Validasi Password (cast ke string untuk mencegah TypeError di PHP 8+)
        $isPasswordValid = false;
        if (!empty($user->password)) {
            $isPasswordValid = Hash::check($credentials['password'], (string) $user->password);
        }
        
        // 2. Validasi PIN dengan pengaman null
        $isPinValid = false;
        if (!is_null($user->pin)) {
            // PENTING: Pilih salah satu metode di bawah ini sesuai database Anda!
            
            // OPSI A: Jika PIN di database disimpan secara PLAIN TEXT (teks/angka biasa)
            $isPinValid = ($credentials['password'] === (string) $user->pin);
            
            // OPSI B: Jika PIN di database di-HASH (Bcrypt, sama seperti password)
            // Silakan hapus komentar baris di bawah ini dan komentari OPSI A:
            // $isPinValid = Hash::check($credentials['password'], (string) $user->pin);
        }

        // 3. Jika keduanya salah
        if (!$isPasswordValid && !$isPinValid) {
            return response()->json(['success' => false, 'message' => 'Login gagal, periksa username atau password/PIN.']);
        }

        // 4. Pengecekan Role
        if (($user->is_admin == 0 && $user->is_root == 0) || $user->is_staff == 1) {
            Auth::guard('fo')->login($user);
            return response()->json(['success' => true, 'role' => 'fo']);
        }

        if ($user->is_admin == 1 || $user->is_root == 1) {
            Auth::guard('bo')->login($user);
            return response()->json(['success' => true, 'role' => 'bo']);
        }

        return response()->json(['success' => false, 'message' => 'Role user tidak valid.']);
    }

    public function logoutFo()
    {
        Auth::guard('fo')->logout();
        return redirect()->route('login');
    }

    public function logoutBo()
    {
        Auth::guard('bo')->logout();
        return redirect()->route('login');
    }
}