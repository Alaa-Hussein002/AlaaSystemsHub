<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Article extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'articles';

    protected $fillable = [
        'title',           // string (ليس array)
        'slug',
        'excerpt',         // string
        'blocks',          // array of blocks [{type, content, ...}]
        'cover_image',
        'category',
        'tags',
        'sources',
        'language',        // ar | en
        'status',
        'is_featured',
        'is_published',
        'reading_time',
        'views_count',
        'likes_count',
        'author',
        'seo',
        'published_at',
    ];
    
    protected $casts = [
        'blocks'       => 'array',
        'tags'         => 'array',
        'sources'      => 'array',
        'author'       => 'array',
        'seo'          => 'array',
        'is_featured'  => 'boolean',
        'is_published' => 'boolean',
        'views_count'  => 'integer',
        'likes_count'  => 'integer',
        'reading_time' => 'integer',
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }
}