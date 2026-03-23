<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PersonalProfile extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'personal_profiles';

    protected $fillable = [
        'full_name',
        'title',
        'bio',
        'photo',
        'cover_image',
        'cv_file',
        'date_of_birth',
        'nationality',
        'location',
        'contact',
        'social_links',
        'highlights',
        'available_for_hire',
        'availability_status',
        'is_published',
        'seo',
        'rotating_roles',
        'tech_display',
        'tools',
        'code_block_lines',
        'hero_greeting',
    ];
    
    protected $casts = [
        'full_name'          => 'array',
        'title'              => 'array',
        'bio'                => 'array',
        'location'           => 'array',
        'contact'            => 'array',
        'social_links'       => 'array',
        'highlights'         => 'array',
        'seo'                => 'array',
        'rotating_roles'     => 'array',
        'tech_display'       => 'array',
        'tools'              => 'array',
        'code_block_lines'   => 'array',
        'available_for_hire' => 'boolean',
        'is_published'       => 'boolean',
    ];

    /**
     * الحصول على الملف الشخصي (مستند واحد فقط)
     */
    public static function getProfile()
    {
        return static::first() ?? new static();
    }
}