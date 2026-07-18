<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => (string) $this->_id,
            'title'             => $this->title,
            'slug'              => $this->slug,
            'description'       => $this->description,
            'short_description' => $this->short_description,
            'category'          => $this->category,
            'tech_stack'        => $this->tech_stack,
            'features'          => $this->features,
            'media'             => $this->media,
            'links'             => $this->links,
            'status'            => $this->status,
            'is_featured'       => $this->is_featured,
            'start_date'        => $this->start_date,
            'end_date'          => $this->end_date,
            'client'            => $this->client,
            'views_count'       => $this->views_count ?? 0,
            'likes_count'       => $this->likes_count ?? 0,
            'tags'              => $this->tags,
            'testimonials'      => TestimonialResource::collection($this->whenLoaded('testimonials')),
            'created_at'        => $this->created_at?->toDateTimeString(),
            'is_published'      => $this->is_published,
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}