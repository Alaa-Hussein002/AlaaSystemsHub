<?php
// app/Models/Certificate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory;

    // ✅ تحديد اسم الجدول بشكل صريح
    protected $table = 'certificates';

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
        'has_expiry' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
                    ->orderBy('issue_date', 'desc');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}