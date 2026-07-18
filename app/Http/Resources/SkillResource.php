<?php

namespace App\Http\Resources;

use App\Helpers\IconHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'category' => $this->category ?? ['ar' => '', 'en' => ''],
            'icon' => IconHelper::format($this->icon),
            'color' => $this->color ?? '#3b82f6',
            'sort_order' => $this->sort_order ?? 0,
            'is_published' => $this->is_published ?? true,
            'technologies' => $this->technologies ?? [],
        ];
    }
}