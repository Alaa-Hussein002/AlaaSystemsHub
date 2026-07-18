<?php
// app/Helpers/IconHelper.php

namespace App\Helpers;

class IconHelper
{
    /**
     * تحديد نوع الأيقونة
     */
    public static function isEmoji(?string $icon): bool
    {
        if (empty($icon)) {
            return false;
        }

        // تحقق من رموز emoji
        return preg_match('/^[\x{1F000}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F300}-\x{1F5FF}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}]{1,4}$/u', $icon) === 1;
    }

    /**
     * تحديد إذا كان رابط صورة
     */
    public static function isImageUrl(?string $icon): bool
    {
        if (empty($icon)) {
            return false;
        }

        return str_starts_with($icon, 'http://') 
            || str_starts_with($icon, 'https://')
            || str_starts_with($icon, '/')
            || preg_match('/\.(jpg|jpeg|png|gif|svg|webp|ico)$/i', $icon);
    }

    /**
     * معالجة وإرجاع الأيقونة الصحيحة
     */
    public static function format(?string $icon): ?string
    {
        if (empty($icon)) {
            return null;
        }

        // ✅ emoji - أرجعه كما هو
        if (self::isEmoji($icon)) {
            return $icon;
        }

        // ✅ رابط كامل - أرجعه كما هو
        if (str_starts_with($icon, 'http://') || str_starts_with($icon, 'https://')) {
            return $icon;
        }

        // ✅ مسار نسبي - أضف domain
        if (str_starts_with($icon, 'media/')) {
            return asset('storage/' . $icon);
        }

        if (str_starts_with($icon, 'storage/')) {
            return asset($icon);
        }

        // ✅ افتراضي - افترض أنه في storage
        return asset('storage/' . $icon);
    }
}