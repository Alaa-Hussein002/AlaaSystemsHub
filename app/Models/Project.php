<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Project extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'projects';

    protected $fillable = [
        'title',             // { ar, en }
        'slug',
        'description',       // { ar, en }
        'short_description',
        'category',
        'tech_stack',        // array of { name, icon, color }
        'features',          // array of strings
        'media',             // { thumbnail, gallery[], demo_video, mockup_3d }
        'links',             // { live_demo, github, documentation }
        'status',            // completed | in_progress | planned
        'is_featured',
        'is_published',
        'sort_order',
        'start_date',
        'end_date',
        'client',
        'views_count',
        'likes_count',
        'tags',
    ];

    protected $casts = [
        'title'         => 'array',
        'description'   => 'array',
        'tech_stack'    => 'array',
        'features'      => 'array',
        'media'         => 'array',
        'links'         => 'array',
        'tags'          => 'array',
        'is_featured'   => 'boolean',
        'is_published'  => 'boolean',
        'sort_order'    => 'integer',
        'views_count'   => 'integer',
        'likes_count'   => 'integer',
    ];

    // ====================================
    // العلاقات
    // ====================================

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class, 'project_id');
    }

    // ====================================
    // Scopes
    // ====================================

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // ====================================
    // Helpers
    // ====================================

    public function incrementViews()
    {
        $this->increment('views_count');
    }
}