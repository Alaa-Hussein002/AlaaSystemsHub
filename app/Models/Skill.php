<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Skill extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'skills';

    protected $fillable = [
        'category',       // { ar, en }
        'icon',
        'color',
        'sort_order',
        'is_published',
        'technologies',   // embedded array
    ];

    protected $casts = [
        'category'     => 'array',
        'technologies' => 'array',
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}