<?php
// app/Models/PersonalProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PersonalProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'bio',
        'photo',
        'cv_file',
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
        'full_name' => 'array',
        'bio' => 'array',
        'contact' => 'array',
        'social_links' => 'array',
        'highlights' => 'array',
        'seo' => 'array',
        'rotating_roles' => 'array',
        'tech_display' => 'array',
        'tools' => 'array',
        'code_block_lines' => 'array',
        'available_for_hire' => 'boolean',
        'is_published' => 'boolean',
    ];

    public static function getProfile()
    {
        return static::first() ?? new static();
    }

    public function getContactInfo()
    {
        return $this->contact ?? [
            'email' => null,
            'phone' => null,
            'whatsapp' => null,
        ];
    }

    public function getEmail()
    {
        return $this->contact['email'] ?? null;
    }

    public function getPhone()
    {
        return $this->contact['phone'] ?? null;
    }

    public function getWhatsApp()
    {
        return $this->contact['whatsapp'] ?? null;
    }

        // ✅ Event للتحقق من التحديث
    // protected static function booted()
    // {
    //     static::updating(function ($profile) {
    //         Log::info('Model updating event:', [
    //             'id' => $profile->id,
    //             'full_name_old' => $profile->getOriginal('full_name'),
    //             'full_name_new' => $profile->full_name,
    //             'changes' => $profile->getDirty(),
    //         ]);
    //     });

    //     static::updated(function ($profile) {
    //         Log::info('Model updated event:', [
    //             'id' => $profile->id,
    //             'full_name' => $profile->full_name,
    //             'updated_at' => $profile->updated_at,
    //         ]);
    //     });
    // }
}