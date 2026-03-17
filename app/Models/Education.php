<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Education extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'educations';

    protected $fillable = [
        'institution',       // { ar, en }
        'degree',            // { ar, en }
        'field_of_study',
        'institution_logo',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'gpa',
        'gpa_scale',
        'description',       // { ar, en }
        'courses_by_level',  // embedded array
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'institution'      => 'array',
        'degree'           => 'array',
        'description'      => 'array',
        'courses_by_level' => 'array',
        'is_current'       => 'boolean',
        'is_published'     => 'boolean',
        'sort_order'       => 'integer',
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