<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class HealthCheckController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            DB::connection('mongodb')->command(['ping' => 1]);

            return $this->success([
                'database' => 'متصل',
                'app_name' => config('app.name'),
                'time'     => now()->toDateTimeString(),
            ], '✅ النظام يعمل بنجاح');

        } catch (\Exception $e) {
            return $this->error('❌ قاعدة البيانات غير متصلة', 500);
        }
    }
}