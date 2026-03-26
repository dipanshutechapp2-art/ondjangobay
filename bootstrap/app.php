<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\Admin::class,
            'core' => \App\Http\Middleware\Core::class,
            'vendor' => \App\Http\Middleware\Vendor::class,
            'CorsMiddleware' => \App\Http\Middleware\CorsMiddleware::class,
            'logActivity' => \App\Http\Middleware\LogUserActivity::class,
            'logActivityApi' => \App\Http\Middleware\LogUserActivityApi::class,
            'partnerAuth' => \App\Http\Middleware\PartnerAuth::class,
            // 'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Sanctum Middleware
        ]);
        
        // Add Sanctum middleware for API
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();