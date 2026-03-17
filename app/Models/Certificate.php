<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Certificate extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'certificates';

    protected $fillable = [
        'title',
        'issuer',
        'issuer_logo',
        'credential_id',
        'credential_url',
        'certificate_image',
        'issue_date',
        'expiry_date',
        'has_expiry',
        'skills_gained',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'skills_gained' => 'array',
        'has_expiry'    => 'boolean',
        'is_published'  => 'boolean',
        'sort_order'    => 'integer',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}