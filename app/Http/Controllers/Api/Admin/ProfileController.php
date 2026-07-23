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

class ProfileController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $profile = PersonalProfile::first();
        if (!$profile) {
            return $this->notFound('لا يوجد ملف شخصي');
        }
        return $this->success(new ProfileResource($profile));
    }

        public function update(Request $request)
    {
        try {
            $profile = PersonalProfile::first();

            // ✅ Log قبل التحديث
            if ($profile) {
                Log::info('Before update:', [
                    'id' => $profile->id,
                    'full_name' => $profile->full_name,
                    'bio' => $profile->bio,
                    'updated_at' => $profile->updated_at,
                    'request_data' => $request->except(['photo', 'cv_file']), // لعدم إغراق اللوج بالملفات
                ]);
            }

            // ✅ إنشاء أو تحديث
            if (!$profile) {
                Log::info('Creating new profile');
                $profile = PersonalProfile::create($request->all());
                Log::info('Profile created:', [
                    'id' => $profile->id,
                    'full_name' => $profile->full_name,
                ]);
            } else {
                Log::info('Updating existing profile');
                $profile->update($request->all());
                
                // ✅ Log بعد التحديث مباشرة
                Log::info('After update (before refresh):', [
                    'id' => $profile->id,
                    'full_name' => $profile->full_name,
                    'updated_at' => $profile->updated_at,
                ]);
                
                // ✅ تحديث الـ timestamp يدوياً (للتأكيد)
                $profile->touch();
                
                // ✅ إعادة تحميل من DB
                $profile->refresh();
                
                Log::info('After refresh:', [
                    'id' => $profile->id,
                    'full_name' => $profile->full_name,
                    'updated_at' => $profile->updated_at,
                ]);
            }

            // ✅ مسح الـ cache
            Cache::forget('personal_profile');
            Cache::forget('public_profile');
            Cache::tags(['profile'])->flush(); // مسح كل cache مرتبط بالـ profile
            
            Log::info('Cache cleared for profile');

            // ✅ تسجيل النشاط
            ActivityLog::log('update', 'profile', 'تم تحديث الملف الشخصي');

            // ✅ التحقق من البيانات المُرجعة
            Log::info('Returning profile:', [
                'id' => $profile->id,
                'full_name' => $profile->full_name,
            ]);

            return $this->success(
                new ProfileResource($profile), 
                'تم تحديث الملف الشخصي بنجاح'
            );
            
        } catch (\Exception $e) {
            Log::error('Profile update error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->error('فشل تحديث الملف الشخصي: ' . $e->getMessage(), 500);
        }
    }

    // ✅ دالة جديدة للتحقق من البيانات
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