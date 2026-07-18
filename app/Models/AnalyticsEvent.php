<?php
// app/Models/AnalyticsEvent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'event_type',
        'event_category',
        'target_type',
        'target_id',
        'target_title',
        'visitor',
        'device',
        'referrer',
        'page_url',
        'duration_seconds',
        'metadata',
    ];

    protected $casts = [
        'visitor' => 'array',
        'device' => 'array',
        'referrer' => 'array',
        'metadata' => 'array',
        'duration_seconds' => 'integer',
    ];

    public static function track(array $data): self
    {
        return static::create(array_merge($data, [
            'visitor' => array_merge($data['visitor'] ?? [], [
                'session_id' => session()->getId(),
                'ip_hash' => md5(request()->ip()),
            ]),
            'device' => [
                'type' => self::detectDeviceType(),
                'browser' => request()->header('User-Agent'),
            ],
            'referrer' => [
                'url' => request()->header('referer'),
            ],
            'page_url' => request()->fullUrl(),
        ]));
    }

    private static function detectDeviceType(): string
    {
        $agent = strtolower(request()->header('User-Agent', ''));
        if (str_contains($agent, 'mobile')) return 'mobile';
        if (str_contains($agent, 'tablet')) return 'tablet';
        return 'desktop';
    }
}