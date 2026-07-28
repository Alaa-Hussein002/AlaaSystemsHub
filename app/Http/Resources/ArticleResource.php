<?php
// app/Http/Resources/ArticleResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            
            // ✅ معالجة البلوكات
            'blocks' => $this->processBlocks($this->blocks ?? []),
            
            // ✅ صورة الغلاف
            'cover_image' => $this->cleanImageUrl($this->cover_image),
            
            'category' => $this->category,
            'tags' => $this->tags ?? [],
            'sources' => $this->sources ?? [],
            'language' => $this->language ?? 'ar',
            'status' => $this->status,
            'is_featured' => $this->is_featured ?? false,
            'is_published' => $this->is_published ?? false,
            
            'reading_time' => $this->reading_time ?? $this->calculateReadingTime(),
            
            'views_count' => $this->views_count ?? 0,
            'likes_count' => $this->likes_count ?? 0,
            'author' => $this->author ?? ['name' => 'علاء حسين'],
            'seo' => $this->seo ?? [],
            
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * ✅ معالجة البلوكات
     */
    private function processBlocks($blocks)
    {
        if (!is_array($blocks)) {
            return [];
        }

        return array_map(function ($block) {
            // معالجة صور البلوكات
            if (isset($block['type']) && $block['type'] === 'image' && !empty($block['content'])) {
                $block['content'] = $this->cleanImageUrl($block['content']);
            }

            return $block;
        }, $blocks);
    }

    /**
     * ✅ تنظيف رابط الصورة - النسخة الصحيحة
     */
    private function cleanImageUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        $url = trim($url);

        // ✅ 1. Cloudinary أو أي CDN خارجي
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        // ✅ 2. مسار محلي - نظفه
        // إزالة التكرار
        $url = preg_replace('#(/storage/)+#', '/storage/', $url);
        
        // استخراج المسار
        if (preg_match('#/storage/(.+)$#', $url, $matches)) {
            return asset('storage/' . $matches[1]);
        }

        // إزالة /storage/ من البداية
        $cleanPath = preg_replace('#^/?storage/#', '', $url);
        
        return asset('storage/' . $cleanPath);
    }

    /**
     * ✅ حساب وقت القراءة
     */
    private function calculateReadingTime()
    {
        $wordCount = 0;
        $blocks = $this->blocks ?? [];

        foreach ($blocks as $block) {
            if (isset($block['type']) && $block['type'] === 'text' && !empty($block['content'])) {
                $text = strip_tags($block['content']);
                $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
                $wordCount += count($words);
            }
        }

        $wordsPerMinute = $this->language === 'ar' ? 200 : 250;
        
        return max(1, ceil($wordCount / $wordsPerMinute));
    }
}