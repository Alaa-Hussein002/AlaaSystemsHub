<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use App\Traits\ApiResponse;

class TestimonialController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $testimonials = Testimonial::published()
                                    ->ordered()
                                    ->get();

        return $this->success(
            TestimonialResource::collection($testimonials),
            'التوصيات'
        );
    }
}