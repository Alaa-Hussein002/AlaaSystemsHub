<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => (string) $this->id,
            'full_name'           => $this->full_name,
            'bio'                 => $this->bio,
            'photo'               => $this->photo,
            'cv_file'             => $this->cv_file,
            'contact'             => $this->contact,
            'social_links'        => $this->social_links,
            'highlights'          => $this->highlights,
            'available_for_hire'  => $this->available_for_hire,
            'availability_status' => $this->availability_status ?? 'available',
            'seo'                 => $this->seo,
            'rotating_roles'      => $this->rotating_roles ?? [],
            'tech_display'        => $this->tech_display ?? [],
            'tools'               => $this->tools ?? [],
            'code_block_lines'    => $this->code_block_lines ?? [],
            'hero_greeting'       => $this->hero_greeting,
        ];
    }
}