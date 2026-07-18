<?php
// app/Models/GameScore.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameScore extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'game_id',
        'user_id',
        'player_name',
        'score',
        'level_reached',
        'time_played_seconds',
        'game_data',
        'device_info',
        'ip_hash',
        'is_verified',
    ];

    protected $casts = [
        'score' => 'integer',
        'level_reached' => 'integer',
        'time_played_seconds' => 'integer',
        'game_data' => 'array',
        'device_info' => 'array',
        'is_verified' => 'boolean',
    ];

    public function game()
    {
        return $this->belongsTo(ArcadeGame::class, 'game_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}