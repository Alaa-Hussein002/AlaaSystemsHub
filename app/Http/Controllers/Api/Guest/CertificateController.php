<?php
// app/Http/Controllers/Api/Guest/CertificateController.php

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
        $certificates = Certificate::where('is_published', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('issue_date', 'desc')
            ->get();

        return $this->success(CertificateResource::collection($certificates));
    }
}