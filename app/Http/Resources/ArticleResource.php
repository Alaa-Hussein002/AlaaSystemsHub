<?php
// app/Http/Resources/ArticleResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            
            // ✅ معالجة البلوكات (تنظيف الصور)
            'blocks' => $this->processBlocks($this->blocks ?? []),
            
            // ✅ صورة الغلاف - رابط نظيف
            'cover_image' => $this->cleanImageUrl($this->cover_image),
            
            'category' => $this->category,
            'tags' => $this->tags ?? [],
            'sources' => $this->sources ?? [],
            'language' => $this->language ?? 'ar',
            'status' => $this->status,
            'is_featured' => $this->is_featured ?? false,
            'is_published' => $this->is_published ?? false,
            
            // ✅ حساب وقت القراءة تلقائياً إذا لم يكن موجوداً
            'reading_time' => $this->reading_time ?? $this->calculateReadingTime(),
            
            'views_count' => $this->views_count ?? 0,
            'likes_count' => $this->likes_count ?? 0,
            'author' => $this->author ?? ['name' => 'علاء حسين'],
            'seo' => $this->seo ?? [],
            
            // ✅ استخدام toIso8601String بدلاً من toISOString
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * ✅ معالجة البلوكات وتنظيف روابط الصور
     */
    private function processBlocks($blocks)
    {
        if (!is_array($blocks)) {
            return [];
        }

        return array_map(function ($block) {
            // تنظيف صور البلوكات من نوع image
            if (isset($block['type']) && $block['type'] === 'image' && isset($block['content'])) {
                $block['content'] = $this->cleanImageUrl($block['content']);
            }

            return $block;
        }, $blocks);
    }

    /**
     * ✅ تنظيف رابط الصورة
     */
    private function cleanImageUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        // إذا كان رابط كامل بالفعل ولا يحتوي على تكرار
        if (preg_match('#^https?://[^/]+/storage/[^/]#', $url) && 
            !preg_match('#/storage/.*?/storage/#', $url)) {
            return $url;
        }

        // إزالة /storage/ المكرر
        $cleanPath = preg_replace('#(/storage/)+#', '/storage/', $url);
        
        // إزالة http://localhost:8000 إن وجد
        $cleanPath = preg_replace('#^https?://[^/]+/storage/#', '', $cleanPath);
        
        // إزالة /storage/ من البداية
        $cleanPath = preg_replace('#^/?storage/#', '', $cleanPath);
        
        // إرجاع رابط كامل
        return url('storage/' . $cleanPath);
    }

    /**
     * ✅ حساب وقت القراءة من البلوكات
     */
    private function calculateReadingTime()
    {
        $wordCount = 0;
        $blocks = $this->blocks ?? [];

        // حساب الكلمات من البلوكات النصية فقط
        foreach ($blocks as $block) {
            if (isset($block['type']) && $block['type'] === 'text' && isset($block['content'])) {
                // إزالة HTML tags وحساب الكلمات
                $text = strip_tags($block['content']);
                $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
                $wordCount += count($words);
            }
        }

        // متوسط القراءة: 200 كلمة في الدقيقة للعربي
        // 250 كلمة للإنجليزي
        $wordsPerMinute = $this->language === 'ar' ? 200 : 250;
        
        return max(1, ceil($wordCount / $wordsPerMinute));
    }
}