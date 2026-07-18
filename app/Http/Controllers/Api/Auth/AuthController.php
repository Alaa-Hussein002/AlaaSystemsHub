<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\LoginAttempt;
use App\Models\Role;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * تسجيل الدخول مع حماية متقدمة
     * POST /api/auth/login
     */
    public function login(LoginRequest $request)
    {
        $email = $request->email;
        $password = $request->password;
        $ipAddress = $request->ip();
        $userAgent = $request->header('User-Agent');

        // Rate Limiting: 5 محاولات كل 15 دقيقة
        $rateLimitKey = 'login:' . $ipAddress;
        
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);
            
            LoginAttempt::logAttempt(
                $email,
                $ipAddress,
                false,
                'تجاوز الحد المسموح من المحاولات',
                $userAgent
            );

            return $this->error(
                "تم تجاوز الحد المسموح من محاولات تسجيل الدخول. حاول مرة أخرى بعد {$minutes} دقيقة",
                429
            );
        }

        // البحث عن المستخدم
        $user = User::where('email', $email)->first();

        // التحقق من وجود المستخدم وكلمة المرور
        if (!$user || !Hash::check($password, $user->password)) {
            RateLimiter::hit($rateLimitKey, 900); // 15 دقيقة

            LoginAttempt::logAttempt(
                $email,
                $ipAddress,
                false,
                'بيانات دخول غير صحيحة',
                $userAgent
            );

            return $this->error('البريد الإلكتروني أو كلمة المرور غير صحيحة', 401);
        }

        // التحقق من حالة الحساب
        if ($user->status !== 'active') {
            LoginAttempt::logAttempt(
                $email,
                $ipAddress,
                false,
                'الحساب معطل',
                $userAgent
            );

            return $this->error('حسابك معطل. تواصل مع الإدارة للمزيد من المعلومات', 403);
        }

        // مسح Rate Limiting عند النجاح
        RateLimiter::clear($rateLimitKey);

        // تحديث بيانات آخر دخول
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
        ]);

        // تسجيل محاولة ناجحة
        LoginAttempt::logAttempt(
            $email,
            $ipAddress,
            true,
            null,
            $userAgent
        );

        // تسجيل في Activity Log
        ActivityLog::create([
            'user_id' => $user->id ?? $user->_id, 
            'user_name' => $user->name,
            'action' => 'login',
            'module' => 'auth',
            'description' => 'تسجيل دخول ناجح',
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        // إنشاء Token
        try {
            $tokenName = $user->type === 'admin' ? 'admin-token' : 'customer-token';
            $token = $user->createToken($tokenName, [], now()->addDays(30))->plainTextToken;
        } catch (\Exception $e) {
            return $this->error('خطأ في إنشاء جلسة الدخول: ' . $e->getMessage(), 500);
        }

        $user->load('role');

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in_days' => 30,
        ], 'تم تسجيل الدخول بنجاح');
    }

    /**
     * تسجيل عميل جديد (معطل مؤقتاً)
     * POST /api/auth/register
     */
    public function register(RegisterRequest $request)
    {
        // CUSTOMER_FEATURE: Disabled temporarily
        return $this->error('التسجيل غير متاح حالياً. سيتم تفعيله قريباً', 503);

        /*
        // ===== الكود الأصلي - سيتم تفعيله لاحقاً =====
        
        $customerRole = Role::where('name', 'customer')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'type' => 'customer',
            'role_id' => $customerRole ? (string) $customerRole->_id : null,
            'status' => 'active',
            'profile' => [
                'preferred_language' => 'ar',
            ],
            'wallet_balance' => 0,
        ]);

        $token = $user->createToken('customer-token', [], now()->addDays(30))->plainTextToken;

        $user->load('role');

        ActivityLog::create([
            'user_id' => (string) $user->_id,
            'user_name' => $user->name,
            'action' => 'register',
            'module' => 'auth',
            'description' => 'تسجيل حساب عميل جديد',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        return $this->created([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in_days' => 30,
        ], 'تم إنشاء الحساب بنجاح');
        */
    }

    /**
     * بيانات المستخدم الحالي
     * GET /api/auth/me
     */
    public function me(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return $this->unauthorized('جلسة غير صالحة');
        }

        $user->load('role');

        return $this->success(
            new UserResource($user),
            'بيانات المستخدم'
        );
    }

    /**
     * تسجيل الخروج
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized('جلسة غير صالحة');
        }

        // حذف التوكن الحالي فقط
        try {
            // تسجيل في Activity Log قبل حذف Token
            ActivityLog::create([
                'user_id' => $user->id, // ✅ تصحيح
                'user_name' => $user->name,
                'action' => 'logout',
                'module' => 'auth',
                'description' => 'تسجيل خروج',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        } catch (\Exception $e) {
            Log::error('Activity Log Error on Logout: ' . $e->getMessage());
        }
    
        // حذف التوكن الحالي
        $user->currentAccessToken()->delete();

        return $this->success(null, 'تم تسجيل الخروج بنجاح');
    }

    /**
     * تسجيل الخروج من جميع الأجهزة
     * POST /api/auth/logout-all
     */
    public function logoutAll(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized('جلسة غير صالحة');
        }

        try {
            ActivityLog::create([
                'user_id' => $user->id, // ✅ تصحيح
                'user_name' => $user->name,
                'action' => 'logout_all_devices',
                'module' => 'auth',
                'description' => 'تسجيل خروج من جميع الأجهزة',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        } catch (\Exception $e) {
            Log::error('Activity Log Error on Logout All: ' . $e->getMessage());
        }
    
        // حذف جميع Tokens
        $user->tokens()->delete();

        return $this->success(null, 'تم تسجيل الخروج من جميع الأجهزة بنجاح');
    }

    /**
     * تحديث الملف الشخصي
     * PUT /api/auth/profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized('جلسة غير صالحة');
        }

        $request->validate([
            'name' => 'sometimes|string|min:2|max:100',
            'phone' => 'sometimes|nullable|string|max:20',
            'avatar' => 'sometimes|nullable|string',
            'profile.city' => 'sometimes|string',
            'profile.country' => 'sometimes|string',
            'profile.preferred_language' => 'sometimes|in:ar,en',
        ]);

        $updateData = [];

        if ($request->has('name')) $updateData['name'] = $request->name;
        if ($request->has('phone')) $updateData['phone'] = $request->phone;
        if ($request->has('avatar')) $updateData['avatar'] = $request->avatar;

        if ($request->has('profile')) {
            $currentProfile = $user->profile ?? [];
            $updateData['profile'] = array_merge($currentProfile, $request->profile);
        }

        $user->update($updateData);
        $user->load('role');

        try {
            ActivityLog::create([
                'user_id' => $user->id, // ✅ تصحيح
                'user_name' => $user->name,
                'action' => 'update_profile',
                'module' => 'auth',
                'description' => 'تحديث الملف الشخصي',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        } catch (\Exception $e) {
            Log::error('Activity Log Error on Update Profile: ' . $e->getMessage());
        }

        return $this->success(
            new UserResource($user),
            'تم تحديث الملف الشخصي بنجاح'
        );
    }

    /**
     * تغيير كلمة المرور
     * PUT /api/auth/change-password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة',
            'password.required' => 'كلمة المرور الجديدة مطلوبة',
            'password.min' => 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $user = $request->user();

        if (!$user) {
            return $this->unauthorized('جلسة غير صالحة');
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('كلمة المرور الحالية غير صحيحة', 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // حذف جميع Tokens القديمة (اختياري)
        // $user->tokens()->delete();

         try {
            ActivityLog::create([
                'user_id' => $user->id, // ✅ تصحيح
                'user_name' => $user->name,
                'action' => 'change_password',
                'module' => 'auth',
                'description' => 'تغيير كلمة المرور',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        } catch (\Exception $e) {
            Log::error('Activity Log Error on Change Password: ' . $e->getMessage());
        }

        return $this->success(null, 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * التحقق من صلاحية الجلسة
     * GET /api/auth/check
     */
    public function checkAuth(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized('جلسة غير صالحة');
        }

        return $this->success([
            'authenticated' => true,
            'user_type' => $user->type,
            'is_admin' => $user->isAdmin(),
        ], 'الجلسة صالحة');
    }
}