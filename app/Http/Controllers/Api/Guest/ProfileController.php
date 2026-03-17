<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\PersonalProfile;
use App\Traits\ApiResponse;

class ProfileController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $profile = PersonalProfile::where('is_published', true)->first();

        if (!$profile) {
            return $this->notFound('الملف الشخصي غير متاح');
        }

        return $this->success(
            new ProfileResource($profile),
            'الملف الشخصي'
        );
    }
}