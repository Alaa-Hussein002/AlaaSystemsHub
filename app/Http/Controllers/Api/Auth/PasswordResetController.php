<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Mail\OtpMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class PasswordResetController extends Controller
{
    use ApiResponse;

    /**
     * إرسال OTP للبريد الإلكتروني
     * POST /api/auth/password/forgot
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $email = $request->email;
        $ipAddress = $request->ip();

        // Rate Limiting: 3 محاولات كل ساعة
        $key = 'password-reset:' . $ipAddress;
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return $this->error(
                'تم تجاوز الحد المسموح. حاول مرة أخرى بعد ' . ceil($seconds / 60) . ' دقيقة',
                429
            );
        }

        RateLimiter::hit($key, 3600); // ساعة واحدة

        // البحث عن المستخدم
        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->error('البريد الإلكتروني غير مسجل لدينا', 404);
        }

        // التحقق من حالة الحساب
        if ($user->status !== 'active') {
            return $this->error('حسابك معطل. تواصل مع الإدارة', 403);
        }

        try {
            // إنشاء OTP جديد
            $resetToken = PasswordResetToken::createForEmail($email, $ipAddress);

            // إرسال البريد الإلكتروني
            Mail::to($email)->send(new OtpMail(
                $resetToken->otp,
                $user->name,
                5 // 5 دقائق
            ));

            return $this->success([
                'email' => $email,
                'expires_in_minutes' => 5,
            ], 'تم إرسال رمز التحقق إلى بريدك الإلكتروني');

        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء إرسال البريد: ' . $e->getMessage(), 500);
        }
    }

    /**
     * التحقق من OTP
     * POST /api/auth/password/verify-otp
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $email = $request->email;
        $otp = $request->otp;

        // البحث عن OTP صالح
        $resetToken = PasswordResetToken::getValidOTP($email, $otp);

        if (!$resetToken) {
            return $this->error('رمز التحقق غير صحيح أو منتهي الصلاحية', 422);
        }

        return $this->success([
            'email' => $email,
            'otp_verified' => true,
            'message' => 'تم التحقق بنجاح. يمكنك الآن إدخال كلمة المرور الجديدة',
        ], 'رمز التحقق صحيح');
    }

    /**
     * إعادة تعيين كلمة المرور
     * POST /api/auth/password/reset
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $email = $request->email;
        $otp = $request->otp;
        $newPassword = $request->password;

        // التحقق من OTP مرة أخرى
        $resetToken = PasswordResetToken::getValidOTP($email, $otp);

        if (!$resetToken) {
            return $this->error('رمز التحقق غير صحيح أو منتهي الصلاحية', 422);
        }

        // البحث عن المستخدم
        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->error('المستخدم غير موجود', 404);
        }

        try {
            // تحديث كلمة المرور
            $user->update([
                'password' => Hash::make($newPassword),
            ]);

            // تعليم OTP كمستخدم
            $resetToken->markAsUsed();

            // حذف جميع Tokens القديمة (تسجيل خروج من جميع الأجهزة)
            $user->tokens()->delete();

            return $this->success(null, 'تم تغيير كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن');

        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء تحديث كلمة المرور: ' . $e->getMessage(), 500);
        }
    }

    /**
     * إعادة إرسال OTP
     * POST /api/auth/password/resend-otp
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;
        $ipAddress = $request->ip();

        // Rate Limiting: محاولة واحدة كل دقيقتين
        $key = 'resend-otp:' . $email;
        
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return $this->error(
                'يرجى الانتظار ' . ceil($seconds / 60) . ' دقيقة قبل إعادة الإرسال',
                429
            );
        }

        RateLimiter::hit($key, 120); // دقيقتين

        $user = User::where('email', $email)->first();

        try {
            // إنشاء OTP جديد
            $resetToken = PasswordResetToken::createForEmail($email, $ipAddress);

            // إرسال البريد
            Mail::to($email)->send(new OtpMail(
                $resetToken->otp,
                $user->name,
                5
            ));

            return $this->success([
                'email' => $email,
                'expires_in_minutes' => 5,
            ], 'تم إعادة إرسال رمز التحقق');

        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء إرسال البريد: ' . $e->getMessage(), 500);
        }
    }
}