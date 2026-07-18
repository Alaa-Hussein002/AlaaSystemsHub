<?php
// app/Http/Controllers/Api/Guest/ExperienceController.php

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
        $experiences = Experience::where('is_published', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('start_date', 'desc')
            ->get();

        return $this->success(ExperienceResource::collection($experiences));
    }
}