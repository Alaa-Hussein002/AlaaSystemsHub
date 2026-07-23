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
        // ✅ Log كل البيانات الواردة
        Log::info('Profile update request received', [
            'data' => $request->except(['photo', 'cv_file']),
            'has_photo' => $request->hasFile('photo'),
            'has_cv' => $request->hasFile('cv_file'),
        ]);

        try {
            // ✅ بدء Transaction
            DB::beginTransaction();

            $profile = PersonalProfile::first();

            if ($profile) {
                Log::info('Updating existing profile', ['id' => $profile->id]);
                
                // ✅ تحديث البيانات
                $updated = $profile->update($request->all());
                
                Log::info('Update result', [
                    'updated' => $updated,
                    'profile_id' => $profile->id,
                ]);
                
                // ✅ تحديث timestamp
                $profile->touch();
                
                // ✅ إعادة تحميل
                $profile->refresh();
                
            } else {
                Log::info('Creating new profile');
                $profile = PersonalProfile::create($request->all());
            }

            // ✅ مسح الـ cache
            Cache::forget('personal_profile');
            Cache::forget('public_profile');

            // ✅ تسجيل النشاط
            ActivityLog::log('update', 'profile', 'تم تحديث الملف الشخصي');

            // ✅ Commit Transaction
            DB::commit();

            Log::info('Profile updated successfully', [
                'id' => $profile->id,
                'full_name' => $profile->full_name,
            ]);

            return $this->success(
                new ProfileResource($profile), 
                'تم تحديث الملف الشخصي بنجاح'
            );
            
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            Log::error('Database query error in profile update', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
            ]);
            
            return $this->error('خطأ في قاعدة البيانات: ' . $e->getMessage(), 500);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Profile update failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->error('فشل تحديث الملف الشخصي: ' . $e->getMessage(), 500);
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