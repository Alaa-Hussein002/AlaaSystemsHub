<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ArcadeGame extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'arcade_games';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'game_type',
        'difficulty',
        'cover_image',
        'screenshots',
        'game_config',
        'controls',
        'stats',
        'rewards',
        'is_featured',
        'is_active',
        'sort_order',
        'tags',
    ];

    protected $casts = [
        'name'        => 'array',
        'description' => 'array',
        'controls'    => 'array',
        'screenshots' => 'array',
        'game_config' => 'array',
        'stats'       => 'array',
        'rewards'     => 'array',
        'tags'        => 'array',
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
    ];

    public function scores()
    {
        return $this->hasMany(GameScore::class, 'game_id');
    }

    public function topScores(int $limit = 10)
    {
        return $this->scores()
                    ->orderBy('score', 'desc')
                    ->limit($limit)
                    ->get();
    }

    public function incrementPlayCount()
    {
        $this->increment('stats.play_count');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}