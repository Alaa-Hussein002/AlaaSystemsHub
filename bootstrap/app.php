<?php
// bootstrap/app.php

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
        // ✅ Sanctum Middleware
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // ✅ تسجيل Middleware Aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminOnly::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'track.visitor' => \App\Http\Middleware\TrackVisitor::class,
        ]);

        // ✅ CSRF Exceptions
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();