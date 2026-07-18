<?php
// app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'category',
        'tech_stack',
        'features',
        'media',
        'links',
        'status',
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
        'title' => 'array',
        'description' => 'array',
        'tech_stack' => 'array',
        'features' => 'array',
        'media' => 'array',
        'links' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    // Scopes
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

    // Helpers
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}