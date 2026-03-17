<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use App\Traits\ApiResponse;

class CertificateController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $certificates = Certificate::published()->ordered()->get();

        return $this->success(
            CertificateResource::collection($certificates),
            'الشهادات'
        );
    }
}