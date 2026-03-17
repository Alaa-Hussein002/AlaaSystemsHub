<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Testimonial extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'testimonials';

    protected $fillable = [
        'client_name',
        'client_title',
        'client_company',
        'client_avatar',
        'content',         // { ar, en }
        'rating',
        'project_id',
        'is_featured',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'content'      => 'array',
        'rating'       => 'integer',
        'is_featured'  => 'boolean',
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}