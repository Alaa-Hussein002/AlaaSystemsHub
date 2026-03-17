<?php

namespace App\Http\Controllers\Api\Guest;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use App\Traits\ApiResponse;

class ExperienceController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $experiences = Experience::published()->ordered()->get();

        return $this->success(
            ExperienceResource::collection($experiences),
            'الخبرات العملية'
        );
    }
}