<?php
// app/Http/Controllers/Api/Guest/EducationController.php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\EducationResource;
use App\Models\Education;
use App\Traits\ApiResponse;

class EducationController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $educations = Education::where('is_published', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('start_date', 'desc')
            ->get();

        return $this->success(EducationResource::collection($educations));
    }
}