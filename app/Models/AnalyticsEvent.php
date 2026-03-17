<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AnalyticsEvent extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'analytics_events';

    const UPDATED_AT = null;

    protected $fillable = [
        'event_type',      // page_view | product_view | product_purchase | game_play | download
        'event_category',  // portfolio | store | games | general
        'target_type',     // project | product | game | page
        'target_id',
        'target_title',
        'visitor',         // embedded { user_id, session_id, ip_hash, country, city }
        'device',          // embedded { type, browser, os, screen_resolution }
        'referrer',        // embedded { url, source, medium }
        'page_url',
        'duration_seconds',
        'metadata',
    ];

    protected $casts = [
        'visitor'          => 'array',
        'device'           => 'array',
        'referrer'         => 'array',
        'metadata'         => 'array',
        'duration_seconds' => 'integer',
    ];

    // ====================================
    // تسجيل حدث بسهولة
    // ====================================

    public static function track(array $data): self
    {
        return static::create(array_merge($data, [
            'visitor' => array_merge($data['visitor'] ?? [], [
                'session_id' => session()->getId(),
                'ip_hash'    => md5(request()->ip()),
            ]),
            'device' => [
                'type'    => self::detectDeviceType(),
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