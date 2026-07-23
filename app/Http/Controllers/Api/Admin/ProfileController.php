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
            DB::beginTransaction();
            
            $profile = PersonalProfile::first();

            if (!$profile) {
                $profile = new PersonalProfile();
            }

            $profile->fill($request->all());
            $profile->save();

            Cache::forget('personal_profile');
            Cache::forget('public_profile');

            ActivityLog::log('update', 'profile', 'تم تحديث الملف الشخصي');

            DB::commit();

            return $this->success(
                new ProfileResource($profile), 
                'تم تحديث الملف الشخصي بنجاح'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            
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
}