<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SetGuardSessionLifetime::class);
        $middleware->use([
            // App\Http\Middleware\TrustHosts::class,
            // ...
        ]);
        $middleware->group('web', [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // Middleware web lainnya...
        ]);

        $middleware->group('api', [
            // Middleware api lainnya...
        ]);

        // === Tambahkan ini ===
        $middleware->alias([
            'fo.auth' => \App\Http\Middleware\FrontOfficeAuthenticate::class,
            'bo.auth' => \App\Http\Middleware\BackOfficeAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
