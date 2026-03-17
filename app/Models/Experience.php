<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Experience extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'experiences';

    protected $fillable = [
        'company',           // { ar, en }
        'position',          // { ar, en }
        'description',       // { ar, en }
        'company_logo',
        'company_url',
        'location',
        'type',              // full_time | part_time | freelance | internship
        'start_date',
        'end_date',
        'is_current',
        'achievements',
        'technologies_used',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'company'           => 'array',
        'position'          => 'array',
        'description'       => 'array',
        'achievements'      => 'array',
        'technologies_used' => 'array',
        'is_current'        => 'boolean',
        'is_published'      => 'boolean',
        'sort_order'        => 'integer',
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