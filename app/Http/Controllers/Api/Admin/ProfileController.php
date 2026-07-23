<?php
// app/Http/Controllers/Api/Admin/ProfileController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\ActivityLog;
use App\Models\PersonalProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    use ApiResponse;

    // public function show()
    // {
    //     $profile = PersonalProfile::first();
    //     if (!$profile) {
    //         return $this->notFound('لا يوجد ملف شخصي');
    //     }
    //     return $this->success(new ProfileResource($profile));
    // }

    public function update(Request $request)
{
    try {
        $profile = PersonalProfile::first();

        if (!$profile) {
            $profile = new PersonalProfile();
        }

        // ✅ تنظيف البيانات قبل الحفظ
        $data = $request->all();
        
        // تنظيف highlights
        if (isset($data['highlights'])) {
            $data['highlights'] = collect($data['highlights'])->map(function($item) {
                if (isset($item['icon']) && str_contains($item['icon'], 'localhost')) {
                    $item['icon'] = null;
                }
                return $item;
            })->filter(function($item) {
                return !empty($item['icon']);
            })->values()->toArray();
        }
        
        // تنظيف tools
        if (isset($data['tools'])) {
            $data['tools'] = collect($data['tools'])->map(function($item) {
                if (isset($item['icon']) && str_contains($item['icon'], 'localhost')) {
                    $item['icon'] = null;
                }
                return $item;
            })->filter(function($item) {
                return !empty($item['icon']);
            })->values()->toArray();
        }
        
        // تنظيف seo
        if (isset($data['seo']['og_image']) && str_contains($data['seo']['og_image'], 'localhost')) {
            $data['seo']['og_image'] = null;
        }
        
        // تنظيف photo
        if (isset($data['photo']) && str_contains($data['photo'], 'localhost')) {
            unset($data['photo']);
        }
        
        // تنظيف cv_file
        if (isset($data['cv_file']) && str_contains($data['cv_file'], 'localhost')) {
            unset($data['cv_file']);
        }

        $profile->fill($data);
        $profile->save();

        Cache::forget('personal_profile');
        Cache::forget('public_profile');

        ActivityLog::log('update', 'profile', 'تم تحديث الملف الشخصي');

        return $this->success(
            new ProfileResource($profile), 
            'تم تحديث الملف الشخصي بنجاح'
        );
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ], 500);
    }
}

    public function show()
    {
        try {
            $profile = PersonalProfile::first();
            
            if (!$profile) {
                return $this->error('الملف الشخصي غير موجود', 404);
            }

            return $this->success(new ProfileResource($profile));
            
        } catch (\Exception $e) {
            Log::error('Profile show error: ' . $e->getMessage());
            return $this->error('فشل جلب الملف الشخصي', 500);
        }
    }

    public function debug()
    {
        try {
            $profile = PersonalProfile::first();
            
            return response()->json([
                'success' => true,
                'profile' => $profile,
                'updated_at' => $profile?->updated_at?->toDateTimeString(),
                'cache' => Cache::get('public_profile'),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}