<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use App\Traits\ApiResponse;

class SkillController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $skills = Skill::published()->ordered()->get();

        return $this->success(
            SkillResource::collection($skills),
            'قائمة المهارات'
        );
    }
}