<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PersonalProfile extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'personal_profiles';

    protected $fillable = [
        'full_name',        // { ar, en }
        'title',            // { ar, en }
        'bio',              // { ar, en }
        'photo',
        'cover_image',
        'cv_file',
        'date_of_birth',
        'nationality',
        'location',         // { city, country, coordinates }
        'contact',          // { email, phone, whatsapp }
        'social_links',     // array of { platform, url, icon }
        'highlights',       // array of { icon, label, value }
        'available_for_hire',
        'is_published',
        'seo',              // { meta_title, meta_description, meta_keywords }
    ];

    protected $casts = [
        'full_name'         => 'array',
        'title'             => 'array',
        'bio'               => 'array',
        'location'          => 'array',
        'contact'           => 'array',
        'social_links'      => 'array',
        'highlights'        => 'array',
        'seo'               => 'array',
        'available_for_hire'=> 'boolean',
        'is_published'      => 'boolean',
    ];

    /**
     * الحصول على الملف الشخصي (مستند واحد فقط)
     */
    public static function getProfile()
    {
        return static::first() ?? new static();
    }
}