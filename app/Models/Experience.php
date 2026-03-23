<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Experience extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'experiences';

protected $fillable = [
    'company',          // {ar, en}
    'position',         // {ar, en}
    'description',      // {ar, en}
    'company_logo',
    'location',
    'type',
    'start_date',
    'end_date',
    'is_current',
    'achievements',
    'technologies_used',
    'sort_order',
    'is_published',     // ← هذا كان ناقص!
];

    protected $casts = [
        'company'            => 'array',
        'position'           => 'array',
        'description'        => 'array',
        'start_date'         => 'date',
        'end_date'           => 'date',
        'is_current'         => 'boolean',
        'is_published'       => 'boolean',
        'achievements'       => 'array',
        'technologies_used'  => 'array',
        'sort_order'         => 'integer',
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