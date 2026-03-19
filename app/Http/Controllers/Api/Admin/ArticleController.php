<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\ActivityLog;
use App\Models\Article;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Article::orderBy('created_at', 'desc');

        if ($request->has('status')) $query->where('status', $request->status);
        if ($request->has('category')) $query->where('category', $request->category);
        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title.ar', 'like', "%{$s}%")
                  ->orWhere('title.en', 'like', "%{$s}%");
            });
        }

        $articles = $query->get();
        return $this->success(ArticleResource::collection($articles), 'المقالات');
    }

    public function store(Request $request)
{
    $request->validate([
        'title'  => 'required|string',
        'status' => 'required|in:draft,published,archived',
    ]);

    $data = $request->all();

    if (empty($data['slug'])) {
        $data['slug'] = \Illuminate\Support\Str::slug($data['title']) . '-' . \Illuminate\Support\Str::random(5);
    }

    $data['views_count'] = 0;
    $data['likes_count'] = 0;

    if ($data['status'] === 'published' && empty($data['published_at'])) {
        $data['published_at'] = now();
    }

    // حساب وقت القراءة
    $blocks = $data['blocks'] ?? [];
    $textContent = collect($blocks)
        ->filter(function ($block) {
            return ($block['type'] ?? '') === 'text';
        })
        ->pluck('content')
        ->implode(' ');
    $wordCount = str_word_count(strip_tags($textContent));
    $data['reading_time'] = max(1, ceil($wordCount / 200));

    $article = Article::create($data);

    ActivityLog::log('create', 'articles', "أضاف مقالة: {$data['title']}", 'article', $article->_id);

    return $this->created(new ArticleResource($article), 'تم إنشاء المقالة');
}

    public function show(string $id)
    {
        $article = Article::find($id);
        if (!$article) return $this->notFound('المقالة غير موجودة');
        return $this->success(new ArticleResource($article));
    }

    public function update(Request $request, string $id)
{
    $article = Article::find($id);
    if (!$article) return $this->notFound('المقالة غير موجودة');

    $data = $request->all();

    if (($data['status'] ?? '') === 'published' && !$article->published_at) {
        $data['published_at'] = now();
    }

    // حساب وقت القراءة
    $blocks = $data['blocks'] ?? $article->blocks ?? [];
    $textContent = collect($blocks)
        ->filter(function ($block) {
            return ($block['type'] ?? '') === 'text';
        })
        ->pluck('content')
        ->implode(' ');
    $wordCount = str_word_count(strip_tags($textContent));
    $data['reading_time'] = max(1, ceil($wordCount / 200));

    $article->update($data);

    $title = $data['title'] ?? $article->title ?? '';
    ActivityLog::log('update', 'articles', "عدّل مقالة: {$title}", 'article', $id);

    return $this->success(new ArticleResource($article), 'تم تحديث المقالة');
}

    public function destroy(string $id)
    {
        $article = Article::find($id);
        if (!$article) return $this->notFound('المقالة غير موجودة');

        $title = $article->title['ar'] ?? '';
        $article->delete();

        ActivityLog::log('delete', 'articles', "حذف مقالة: {$title}", 'article', $id);

        return $this->success(null, 'تم حذف المقالة');
    }
}