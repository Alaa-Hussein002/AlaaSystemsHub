<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Skill extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'skills';

    protected $fillable = [
        'category',       // {ar, en}
        'icon',
        'color',
        'sort_order',
        'is_published',   // ← هذا كان ناقص!
        'technologies',   // array of {name, proficiency, years_of_experience, is_featured}
    ];
    
    protected $casts = [
        'category'     => 'array',
        'technologies' => 'array',
        'sort_order'   => 'integer',
        'is_published' => 'boolean',
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