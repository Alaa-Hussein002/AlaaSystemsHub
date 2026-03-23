<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EducationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => (string) $this->_id,
            'institution'      => $this->institution,
            'degree'           => $this->degree,
            'field_of_study'   => $this->field_of_study,
            'institution_logo' => $this->institution_logo,
            'location'         => $this->location,
            'start_date'       => $this->start_date,
            'end_date'         => $this->end_date,
            'is_current'       => $this->is_current,
            'gpa'              => $this->gpa,
            'gpa_scale'        => $this->gpa_scale,
            'description'      => $this->description,
            'courses_by_level' => $this->courses_by_level,
            'is_published'     => $this->is_published,
        ];
    }
}