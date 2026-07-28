<?php
// app/Http/Controllers/Api/Admin/ArticleController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\ActivityLog;
use App\Models\Article;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Article::orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $articles = $query->get();

        return $this->success(ArticleResource::collection($articles));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:articles,slug',
            'excerpt' => 'nullable|string',
            'blocks' => 'nullable|array',
            'cover_image' => 'nullable|string|max:1000',
            'category' => 'nullable|string',
            'tags' => 'nullable|array',
            'sources' => 'nullable|array',
            'language' => 'nullable|string|in:ar,en',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'author' => 'nullable|array',
        ]);

        // ✅ توليد slug تلقائي
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // التأكد من عدم التكرار
            $count = Article::where('slug', 'like', $validated['slug'] . '%')->count();
            if ($count > 0) {
                $validated['slug'] = $validated['slug'] . '-' . ($count + 1);
            }
        }

        // ✅ تنظيف صورة الغلاف
        if (isset($validated['cover_image']) && $validated['cover_image']) {
            $validated['cover_image'] = $this->cleanImagePath($validated['cover_image']);
        }

        // ✅ تنظيف الصور في البلوكات
        if (isset($validated['blocks']) && is_array($validated['blocks'])) {
            $validated['blocks'] = $this->cleanBlocks($validated['blocks']);
        }

        // ✅ تعيين published_at
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
            $validated['is_published'] = true;
        }

        // ✅ إنشاء المقالة
        $article = Article::create($validated);

        // ✅ حساب وقت القراءة
        $article->reading_time = $article->calculateReadingTime();
        $article->save();

        ActivityLog::log('create', 'articles', "أضاف مقالة: {$article->title}", 'article', $article->id);

        return $this->created(new ArticleResource($article), 'تم إنشاء المقالة بنجاح');
    }

    public function show(string $id)
    {
        $article = Article::find($id);
        
        if (!$article) {
            return $this->notFound('المقالة غير موجودة');
        }

        return $this->success(new ArticleResource($article));
    }

    public function update(Request $request, string $id)
    {
        $article = Article::find($id);
        
        if (!$article) {
            return $this->notFound('المقالة غير موجودة');
        }

        // ✅ Log البيانات الواردة للتحقق
        Log::info('Article update request', [
            'id' => $id,
            'data' => $request->all()
        ]);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|unique:articles,slug,' . $id,
            'excerpt' => 'nullable|string',
            'blocks' => 'nullable|array',
            'cover_image' => 'nullable|string|max:1000',
            'category' => 'nullable|string',
            'tags' => 'nullable|array',
            'sources' => 'nullable|array',
            'language' => 'nullable|string|in:ar,en',
            'status' => 'sometimes|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'author' => 'nullable|array',
        ]);

        // ✅ معالجة الصور فقط إذا كانت موجودة وليست فارغة
        if (array_key_exists('cover_image', $validated)) {
            if ($validated['cover_image']) {
                $validated['cover_image'] = $this->cleanImagePath($validated['cover_image']);
            } else {
                // إذا كانت فارغة، لا تحذف الصورة القديمة
                unset($validated['cover_image']);
            }
        }

        // ✅ معالجة البلوكات
        if (isset($validated['blocks']) && is_array($validated['blocks'])) {
            $validated['blocks'] = $this->cleanBlocks($validated['blocks']);
        }

        // ✅ تحديث حالة النشر
        if (isset($validated['status'])) {
            if ($validated['status'] === 'published' && $article->status !== 'published') {
                $validated['published_at'] = now();
                $validated['is_published'] = true;
            } elseif ($validated['status'] !== 'published') {
                $validated['is_published'] = false;
            }
        }

        // ✅ تحديث المقالة
        $article->update($validated);

        // ✅ إعادة حساب وقت القراءة
        $article->reading_time = $article->calculateReadingTime();
        $article->save();

        // ✅ Log بعد التحديث
        Log::info('Article updated successfully', [
            'id' => $id,
            'title' => $article->title
        ]);

        ActivityLog::log('update', 'articles', "عدّل مقالة: {$article->title}", 'article', $id);

        return $this->success(new ArticleResource($article), 'تم تحديث المقالة بنجاح');
    }

    public function destroy(string $id)
    {
        $article = Article::find($id);
        
        if (!$article) {
            return $this->notFound('المقالة غير موجودة');
        }

        $title = $article->title;
        $article->delete();

        ActivityLog::log('delete', 'articles', "حذف مقالة: {$title}", 'article', $id);

        return $this->success(null, 'تم حذف المقالة بنجاح');
    }

    /**
     * ✅ تنظيف مسار الصورة
     */
    private function cleanImagePath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $path = trim($path);
    
        // ✅ إذا كان رابط Cloudinary كامل
        if (Str::startsWith($path, 'https://res.cloudinary.com')) {
            return $path;
        }
    
        // ✅ إذا كان رابط كامل آخر
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // إذا كان رابط كامل، استخرج المسار النسبي فقط
        if (preg_match('#/storage/(.+)$#', $path, $matches)) {
            return $matches[1];
        }

        // إزالة /storage/ من البداية
        $cleanPath = preg_replace('#^/?storage/#', '', $path);

        return $cleanPath;
    }

    /**
     * ✅ تنظيف الصور في البلوكات
     */
private function cleanBlocks(array $blocks): array
{
    return array_map(function ($block) {
        // ✅ تنظيف صور البلوكات
        if (isset($block['type']) && $block['type'] === 'image' && !empty($block['content'])) {
            $block['content'] = $this->cleanImagePath($block['content']);
        }
        
        return $block;
    }, $blocks);
}
}