<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => (string) $this->_id,
            'client_name'    => $this->client_name,
            'client_title'   => $this->client_title,
            'client_company' => $this->client_company,
            'client_avatar'  => $this->client_avatar,
            'content'        => $this->content,
            'rating'         => $this->rating,
            'is_featured'    => $this->is_featured,
        ];
    }
}