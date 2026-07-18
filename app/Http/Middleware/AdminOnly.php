<?php
// app/Http/Middleware/AdminOnly.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح',
            ], 401);
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        if (! $user || ! $user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية الوصول',
            ], 403);
        }

        return $next($request);
    }
}