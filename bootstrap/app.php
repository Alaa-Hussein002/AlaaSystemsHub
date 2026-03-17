<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // تسجيل Middleware مخصصة
        $middleware->alias([
            'admin'      => \App\Http\Middleware\AdminOnly::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'track'      => \App\Http\Middleware\TrackVisitor::class,
        ]);

        // إعدادات CORS للـ API
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401, [], JSON_UNESCAPED_UNICODE);
            }
        });
    })->create();
