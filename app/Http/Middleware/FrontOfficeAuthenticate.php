<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class FrontOfficeAuthenticate
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('fo')->check()) {
            return redirect()->route('login')->withErrors([
                'username' => 'Silakan login sebagai FO terlebih dahulu.'
            ]);
        }

        $user = Auth::guard('fo')->user();
        // Hanya boleh FO (is_admin & is_root == 0)
        if ($user->is_admin || $user->is_root) {
            Auth::guard('fo')->logout();
            return redirect()->route('login')->withErrors([
                'username' => 'Akun ini tidak memiliki akses ke FO.'
            ]);
        }

        return $next($request);
    }
}
