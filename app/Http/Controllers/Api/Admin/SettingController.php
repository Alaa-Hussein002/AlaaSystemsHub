<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return $this->success($settings);
    }

    public function show(string $key)
    {
        $value = Setting::getValue($key);
        if (!$value) return $this->notFound('الإعداد غير موجود');
        return $this->success([$key => $value]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'group' => 'required|string',
            'key'   => 'required|string',
            'value' => 'required',
        ]);

        Setting::setValue($request->group, $request->key, $request->value);
        ActivityLog::log('update', 'settings', "حدّث الإعداد: {$request->key}");
        return $this->success(null, 'تم تحديث الإعداد');
    }
}