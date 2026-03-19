<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\AnalyticsEvent;
use App\Models\Article;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Article::published()->orderBy('published_at', 'desc');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title.ar', 'like', "%{$search}%")
                  ->orWhere('title.en', 'like', "%{$search}%")
                  ->orWhere('tags', $search);
            });
        }

        if ($request->has('tag')) {
            $query->where('tags', $request->tag);
        }

        $articles = $query->get();

        return $this->success(
            ArticleResource::collection($articles),
            'المقالات'
        );
    }

    public function show(string $slug)
    {
        $article = Article::published()->where('slug', $slug)->first();

        if (!$article) {
            return $this->notFound('المقالة غير موجودة');
        }

        $article->incrementViews();

        AnalyticsEvent::create([
            'event_type'     => 'page_view',
            'event_category' => 'blog',
            'target_type'    => 'article',
            'target_id'      => (string) $article->_id,
            'target_title'   => $article->title['ar'] ?? $article->title,
            'visitor'        => [
                'ip_hash'    => md5(request()->ip()),
                'session_id' => session()->getId(),
            ],
            'page_url' => request()->fullUrl(),
        ]);

        $related = Article::published()
            ->where('_id', '!=', $article->_id)
            ->where('category', $article->category)
            ->limit(3)
            ->get();

        return $this->success([
            'article' => new ArticleResource($article),
            'related' => ArticleResource::collection($related),
        ], 'تفاصيل المقالة');
    }

    public function categories()
    {
        $categories = Article::published()
            ->get()
            ->pluck('category')
            ->filter()
            ->unique()
            ->values();

        return $this->success($categories, 'تصنيفات المقالات');
    }
}