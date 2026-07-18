<?php
// app/Http/Controllers/Api/Public/ArticleController.php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use ApiResponse;

    /**
     * عرض المقالات المنشورة فقط
     */
    public function index(Request $request)
    {
        $query = Article::published()
                       ->orderBy('published_at', 'desc');

        // فلترة حسب التصنيف
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // فلترة حسب اللغة
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // المقالات المميزة فقط
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $perPage = $request->input('per_page', 12);
        $articles = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($articles->items()),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    /**
     * ✅ عرض مقالة واحدة بالـ slug
     */
    public function show(string $slug)
    {
        // ✅ البحث بالـ slug وليس id
        $article = Article::where('slug', $slug)
                         ->where('is_published', true)
                         ->where('status', 'published')
                         ->first();

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'المقالة غير موجودة أو غير منشورة',
            ], 404);
        }

        // ✅ زيادة عدد المشاهدات
        $article->incrementViews();

        return response()->json([
            'success' => true,
            'data' => new ArticleResource($article),
        ]);
    }

    /**
     * الحصول على التصنيفات
     */
    public function getCategories()
    {
        $categories = Article::where('is_published', true)
                            ->where('status', 'published')
                            ->whereNotNull('category')
                            ->distinct()
                            ->pluck('category')
                            ->filter()
                            ->values();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * المقالات المميزة
     */
    public function featured()
    {
        $articles = Article::where('is_published', true)
                          ->where('status', 'published')
                          ->where('is_featured', true)
                          ->orderBy('published_at', 'desc')
                          ->limit(6)
                          ->get();

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($articles),
        ]);
    }

    /**
     * ✅ المقالات ذات الصلة (نفس التصنيف)
     */
    public function related(string $slug)
    {
        $article = Article::where('slug', $slug)
                         ->where('is_published', true)
                         ->first();

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'المقالة غير موجودة',
            ], 404);
        }

        $related = Article::where('is_published', true)
                         ->where('status', 'published')
                         ->where('category', $article->category)
                         ->where('id', '!=', $article->id)
                         ->orderBy('published_at', 'desc')
                         ->limit(3)
                         ->get();

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($related),
        ]);
    }
}