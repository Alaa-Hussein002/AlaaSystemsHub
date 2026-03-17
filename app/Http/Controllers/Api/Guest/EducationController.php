<?php

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
        $educations = Education::published()->ordered()->get();

        return $this->success(
            EducationResource::collection($educations),
            'المؤهلات التعليمية'
        );
    }
}