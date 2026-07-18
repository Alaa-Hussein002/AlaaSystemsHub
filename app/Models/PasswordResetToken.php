<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'is_used',
        'ip_address',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * توليد OTP عشوائي
     */
    public static function generateOTP(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * إنشاء OTP جديد للمستخدم
     */
    public static function createForEmail(string $email, string $ipAddress = null): self
    {
        // حذف OTP القديمة غير المستخدمة
        self::where('email', $email)
            ->where('is_used', false)
            ->delete();

        return self::create([
            'email' => $email,
            'otp' => self::generateOTP(),
            'expires_at' => Carbon::now()->addMinutes(5),
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * التحقق من OTP
     */
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    /**
     * تعليم OTP كمستخدم
     */
    public function markAsUsed(): void
    {
        $this->update(['is_used' => true]);
    }

    /**
     * الحصول على OTP صالح
     */
    public static function getValidOTP(string $email, string $otp): ?self
    {
        return self::where('email', $email)
            ->where('otp', $otp)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }
}