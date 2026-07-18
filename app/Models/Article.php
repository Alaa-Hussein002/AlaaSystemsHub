<?php
// app/Models/Article.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'blocks',
        'cover_image',
        'category',
        'tags',
        'sources',
        'language',
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
        'blocks' => 'array',
        'tags' => 'array',
        'sources' => 'array',
        'author' => 'array',
        'seo' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'reading_time' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * ✅ Scopes
    */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByLanguage($query, $lang)
    {
        return $query->where('language', $lang);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * ✅ زيادة عدد المشاهدات
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * ✅ زيادة الإعجابات
     */
    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    /**
     * ✅ حساب وقت القراءة تلقائياً
     */
    public function calculateReadingTime()
    {
        $wordCount = 0;

        // حساب الكلمات من البلوكات النصية
        foreach ($this->blocks as $block) {
            if ($block['type'] === 'text' && isset($block['content'])) {
                $wordCount += str_word_count(strip_tags($block['content']));
            }
        }

        // متوسط القراءة: 200 كلمة في الدقيقة
        return max(1, ceil($wordCount / 200));
    }
}