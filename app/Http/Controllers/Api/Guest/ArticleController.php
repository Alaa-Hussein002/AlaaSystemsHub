<?php
// app/Http/Controllers/Api/Guest/ArticleController.php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Article::where('is_published', true)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $articles = $query->get();

        return $this->success(ArticleResource::collection($articles));
    }

    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)
            ->where('is_published', true)
            ->where('status', 'published')
            ->first();

        if (!$article) {
            return $this->notFound('المقالة غير موجودة');
        }

        // Increment views
        $article->increment('views_count');

        // Get related articles
        $related = Article::where('is_published', true)
            ->where('status', 'published')
            ->where('id', '!=', $article->id)
            ->where(function ($q) use ($article) {
                // Same category or shared tags
                $q->where('category', $article->category);
                
                if ($article->tags && count($article->tags) > 0) {
                    foreach ($article->tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        return $this->success([
            'article' => new ArticleResource($article),
            'related' => ArticleResource::collection($related),
        ]);
    }

    public function categories()
    {
        $categories = Article::where('is_published', true)
            ->where('status', 'published')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return $this->success($categories);
    }
}