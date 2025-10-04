<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class BackOfficeAuthenticate
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('bo')->check()) {
            return redirect()->route('login')->withErrors([
                'username' => 'Silakan login sebagai BO terlebih dahulu.'
            ]);
        }

        $user = Auth::guard('bo')->user();
        // Hanya boleh BO (is_admin / is_root == 1)
        if (!$user->is_admin && !$user->is_root) {
            Auth::guard('bo')->logout();
            return redirect()->route('login')->withErrors([
                'username' => 'Akun ini tidak memiliki akses ke BO.'
            ]);
        }

        return $next($request);
    }
}
