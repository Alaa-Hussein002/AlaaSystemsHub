<?php
// app/Exceptions/Handler.php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // ✅ معالجة أخطاء قاعدة البيانات
        $this->renderable(function (QueryException $e, $request) {
            if ($request->expectsJson()) {
                Log::error('Database Error: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في قاعدة البيانات',
                    'error' => app()->environment('local') ? $e->getMessage() : 'حدث خطأ غير متوقع',
                ], 500);
            }
        });
    }
}