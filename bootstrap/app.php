<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // Pastikan route API juga ada (biasanya perlu ditambah manual di L11 jika belum)
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // --- TAMBAHKAN INI ---
        $middleware->alias([
            'activity.log' => \App\Http\Middleware\LogActivity::class,
        ]);
        // ---------------------
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();