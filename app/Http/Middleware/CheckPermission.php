<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $module, string $action)
    {
        $user = $request->user();

        if (!$user || !$user->hasPermission($module, $action)) {
            return response()->json([
                'status'  => false,
                'message' => "ليس لديك صلاحية: {$module}.{$action}",
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        return $next($request);
    }
}