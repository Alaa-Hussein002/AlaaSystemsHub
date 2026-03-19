<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => (string) $this->_id,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'excerpt'       => $this->excerpt,
            'blocks'        => $this->blocks,
            'cover_image'   => $this->cover_image,
            'category'      => $this->category,
            'tags'          => $this->tags,
            'sources'       => $this->sources,
            'language'      => $this->language ?? 'ar',
            'status'        => $this->status,
            'is_featured'   => $this->is_featured,
            'reading_time'  => $this->reading_time,
            'views_count'   => $this->views_count ?? 0,
            'likes_count'   => $this->likes_count ?? 0,
            'author'        => $this->author,
            'published_at'  => $this->published_at?->toDateTimeString(),
            'created_at'    => $this->created_at?->toDateTimeString(),
        ];
    }
}