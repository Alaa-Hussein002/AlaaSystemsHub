<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => (string) $this->_id,
            'full_name'         => $this->full_name,
            'title'             => $this->title,
            'bio'               => $this->bio,
            'photo'             => $this->photo,
            'cover_image'       => $this->cover_image,
            'cv_file'           => $this->cv_file,
            'nationality'       => $this->nationality,
            'location'          => $this->location,
            'contact'           => $this->contact,
            'social_links'      => $this->social_links,
            'highlights'        => $this->highlights,
            'available_for_hire'=> $this->available_for_hire,
            'seo'               => $this->seo,
        ];
    }
}