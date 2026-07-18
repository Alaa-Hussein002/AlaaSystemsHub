<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'successful',
        'failure_reason',
        'attempted_at',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    /**
     * تسجيل محاولة دخول
     */
    public static function logAttempt(
        string $email,
        string $ipAddress,
        bool $successful,
        string $failureReason = null,
        string $userAgent = null
    ): void {
        self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'successful' => $successful,
            'failure_reason' => $failureReason,
            'attempted_at' => Carbon::now(),
        ]);
    }

    /**
     * عدد المحاولات الفاشلة للـ IP
     */
    public static function failedAttemptsForIP(string $ipAddress, int $minutes = 15): int
    {
        return self::where('ip_address', $ipAddress)
            ->where('successful', false)
            ->where('attempted_at', '>=', Carbon::now()->subMinutes($minutes))
            ->count();
    }

    /**
     * عدد المحاولات الفاشلة للبريد
     */
    public static function failedAttemptsForEmail(string $email, int $minutes = 15): int
    {
        return self::where('email', $email)
            ->where('successful', false)
            ->where('attempted_at', '>=', Carbon::now()->subMinutes($minutes))
            ->count();
    }

    /**
     * تنظيف المحاولات القديمة
     */
    public static function cleanOldAttempts(int $days = 30): void
    {
        self::where('attempted_at', '<', Carbon::now()->subDays($days))->delete();
    }
}