<?php
// app/Http/Controllers/Api/Guest/ProfileController.php

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
        $profile = PersonalProfile::first();
        
        if (!$profile) {
            // Return empty structure if no profile exists yet
            return $this->success([
                'full_name' => ['ar' => '', 'en' => ''],
                'title' => ['ar' => '', 'en' => ''],
                'bio' => ['ar' => '', 'en' => ''],
                'photo' => null,
                'cv_file' => null,
                'rotating_roles' => [],
                'availability_status' => 'available',
                'tech_display' => [],
                'tools' => [],
                'code_block_lines' => [],
                'highlights' => [],
            ]);
        }

        return $this->success(new ProfileResource($profile));
    }
}