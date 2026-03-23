<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => (string) $this->_id,
            'company'          => $this->company,
            'position'         => $this->position,
            'description'      => $this->description,
            'company_logo'     => $this->company_logo,
            'company_url'      => $this->company_url,
            'location'         => $this->location,
            'type'             => $this->type,
            'start_date'       => $this->start_date,
            'end_date'         => $this->end_date,
            'is_current'       => $this->is_current,
            'achievements'     => $this->achievements,
            'is_published'     => $this->is_published,
            'technologies_used'=> $this->technologies_used,
        ];
    }
}