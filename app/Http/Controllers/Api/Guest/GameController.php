<?php
// app/Http/Controllers/Api/Guest/GameController.php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;

class GameController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->success([]);
    }

    public function show($slug)
    {
        return $this->notFound('اللعبة غير موجودة');
    }

    public function play($slug)
    {
        return $this->error('هذه الميزة غير متاحة حالياً');
    }

    public function submitScore($slug)
    {
        return $this->error('هذه الميزة غير متاحة حالياً');
    }

    public function leaderboard($slug)
    {
        return $this->success([]);
    }
}