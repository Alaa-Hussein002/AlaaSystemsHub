<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => (string) $this->_id,
            'title'             => $this->title,
            'issuer'            => $this->issuer,
            'issuer_logo'       => $this->issuer_logo,
            'credential_id'    => $this->credential_id,
            'credential_url'   => $this->credential_url,
            'certificate_image'=> $this->certificate_image,
            'issue_date'       => $this->issue_date,
            'expiry_date'      => $this->expiry_date,
            'skills_gained'    => $this->skills_gained,
            'is_published'     => $this->is_published,
        ];
    }
}