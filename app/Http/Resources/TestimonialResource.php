<?php
// app/Http/Resources/TestimonialResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'client_name' => $this->client_name,
            'client_title' => $this->client_title,
            'client_company' => $this->client_company,
            'client_avatar' => $this->client_avatar ? asset('storage/' . $this->client_avatar) : null,
            'content' => $this->content ?? ['ar' => '', 'en' => ''],
            'rating' => $this->rating ?? 5,
            'project_id' => $this->project_id,
            'is_featured' => $this->is_featured ?? false,
            'is_published' => $this->is_published ?? true,
            'sort_order' => $this->sort_order ?? 0,
        ];
    }
}