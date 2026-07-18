<?php
// app/Http/Controllers/Api/HealthCheckController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HealthCheckController extends Controller
{
    public function index()
    {
        try {
            DB::connection()->getPdo();
            $dbStatus = 'connected';
        } catch (\Exception $e) {
            $dbStatus = 'disconnected';
        }

        return response()->json([
            'status' => 'ok',
            'database' => $dbStatus,
            'timestamp' => now()->toISOString(),
        ]);
    }
}