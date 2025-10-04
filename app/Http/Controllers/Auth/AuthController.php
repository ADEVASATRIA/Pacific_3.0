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

        // Cari user berdasarkan username
        $user = Admin::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['username' => 'Login gagal, periksa username atau password.']);
        }

        // 🔹 Jika user adalah FO (is_admin=0, is_root=0)
        if ($user->is_admin == 0 && $user->is_root == 0) {
            Auth::guard('fo')->login($user);
            return redirect()->route('main'); // dashboard FO
        }

        // 🔹 Jika user adalah BO (is_admin=1 atau is_root=1)
        if ($user->is_admin == 1 || $user->is_root == 1) {
            Auth::guard('bo')->login($user);
            return redirect()->route('dashboard'); // dashboard BO
        }

        return back()->withErrors(['username' => 'Role user tidak valid.']);
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
