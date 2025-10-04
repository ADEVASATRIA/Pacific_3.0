<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class SetGuardSessionLifetime
{
    public function handle($request, Closure $next)
    {
        // cek guard aktif
        $guards = ['fo', 'bo'];
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $lifetimes = Config::get('session.session_lifetimes', []);
                if (isset($lifetimes[$guard])) {
                    Config::set('session.lifetime', $lifetimes[$guard]);
                }
                break;
            }
        }

        return $next($request);
    }
}
