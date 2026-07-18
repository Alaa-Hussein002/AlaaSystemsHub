<?php
// app/Helpers/MediaHelper.php

namespace App\Helpers;

class MediaHelper
{
    /**
     * تنظيف وتوليد رابط الملف الصحيح
     */
    public static function getFileUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // ✅ إذا كان رابط كامل بالفعل، أرجعه كما هو
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // ✅ إذا كان يبدأ بـ /storage، احذف البادئة
        $path = ltrim($path, '/');
        $path = preg_replace('#^storage/#', '', $path);

        // ✅ إذا كان يبدأ بـ media/، أضف storage/
        if (str_starts_with($path, 'media/')) {
            return url('storage/' . $path);
        }

        // ✅ أي حالة أخرى
        return url('storage/media/' . $path);
    }

    /**
     * تنظيف رابط موجود من التكرار
     */
    public static function cleanUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        // إذا كان رابط كامل صحيح، أرجعه
        if (preg_match('#^https?://[^/]+/storage/media/#', $url)) {
            return $url;
        }

        // إزالة جميع التكرارات
        $url = preg_replace('#(https?://[^/]+/storage/)+#', '$1', $url);
        $url = preg_replace('#/storage/storage/#', '/storage/', $url);
        
        return $url;
    }
}