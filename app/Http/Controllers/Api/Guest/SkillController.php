<?php
// app/Http/Controllers/Api/Guest/SkillController.php

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
        $skills = Skill::where('is_published', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        return $this->success(SkillResource::collection($skills));
    }
}