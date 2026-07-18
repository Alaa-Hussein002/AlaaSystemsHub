<?php
// app/Http/Controllers/Api/Guest/StoreController.php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;

class StoreController extends Controller
{
    use ApiResponse;

    public function categories()
    {
        return $this->success([]);
    }

    public function products()
    {
        return $this->success([]);
    }

    public function productDetails($slug)
    {
        return $this->notFound('المنتج غير موجود');
    }

    public function paymentMethods()
    {
        return $this->success([]);
    }
}