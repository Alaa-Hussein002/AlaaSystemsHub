<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'settings';

    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * الحصول على إعداد معين
     */
    public static function getValue(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * تحديث أو إنشاء إعداد
     */
    public static function setValue(string $group, string $key, $value): self
    {
        Cache::forget("setting_{$key}");

        return static::updateOrCreate(
            ['key' => $key],
            ['group' => $group, 'value' => $value]
        );
    }

    /**
     * الحصول على جميع إعدادات مجموعة
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
                     ->get()
                     ->pluck('value', 'key')
                     ->toArray();
    }
}