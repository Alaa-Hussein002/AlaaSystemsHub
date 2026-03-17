<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->type !== 'admin') {
            return response()->json([
                'status'  => false,
                'message' => 'هذه الصفحة للمدراء فقط',
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'status'  => false,
                'message' => 'حسابك معطل',
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        return $next($request);
    }
}