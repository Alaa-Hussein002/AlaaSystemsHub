<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\ActivityLog;
use App\Models\PersonalProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

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
        $profile = PersonalProfile::first();

        if (!$profile) {
            $profile = PersonalProfile::create($request->all());
        } else {
            $profile->update($request->all());
        }

        ActivityLog::log('update', 'profile', 'تم تحديث الملف الشخصي');

        return $this->success(new ProfileResource($profile), 'تم تحديث الملف الشخصي');
    }
}