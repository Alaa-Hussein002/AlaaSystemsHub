<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => (string) $this->_id,
            'category'     => $this->category,
            'icon'         => $this->icon,
            'color'        => $this->color,
            'technologies' => $this->technologies,
            'is_published' => $this->is_published,
            'sort_order'   => $this->sort_order,
        ];
    }
}