<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * تسجيل الدخول
     * POST /api/auth/login
     */
    public function login(LoginRequest $request)
{
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return $this->error('البريد الإلكتروني أو كلمة المرور غير صحيحة', 401);
    }

    if ($user->status !== 'active') {
        return $this->error('حسابك معطل، تواصل مع الإدارة', 403);
    }

    $user->update([
        'last_login_at' => now(),
        'last_login_ip' => $request->ip(),
    ]);

    // اختبار Token
    try {
        $token = $user->createToken('admin-token')->plainTextToken;
    } catch (\Exception $e) {
        return $this->error('خطأ في إنشاء التوكن: ' . $e->getMessage(), 500);
    }

    $user->load('role');

    return $this->success([
        'user'       => new UserResource($user),
        'token'      => $token,
        'token_type' => 'Bearer',
    ], 'تم تسجيل الدخول بنجاح');
}

    /**
     * تسجيل عميل جديد
     * POST /api/auth/register
     */
    public function register(RegisterRequest $request)
    {
        $customerRole = Role::where('name', 'customer')->first();

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'type'     => 'customer',
            'role_id'  => $customerRole ? (string) $customerRole->_id : null,
            'status'   => 'active',
            'profile'  => [
                'preferred_language' => 'ar',
            ],
            'wallet_balance' => 0,
        ]);

        $token = $user->createToken('customer-token')->plainTextToken;

        $user->load('role');

        return $this->created([
            'user'  => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'تم إنشاء الحساب بنجاح');
    }

    /**
     * بيانات المستخدم الحالي
     * GET /api/auth/me
     */
    public function me(Request $request)
    {
        $user = $request->user();
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

        // حذف التوكن الحالي فقط
        $user->currentAccessToken()->delete();

        ActivityLog::create([
            'user_id'     => (string) $user->_id,
            'user_name'   => $user->name,
            'action'      => 'logout',
            'module'      => 'auth',
            'description' => 'تسجيل خروج',
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->header('User-Agent'),
        ]);

        return $this->success(null, 'تم تسجيل الخروج بنجاح');
    }

    /**
     * تحديث الملف الشخصي
     * PUT /api/auth/profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'   => 'sometimes|string|min:2|max:100',
            'phone'  => 'sometimes|nullable|string|max:20',
            'avatar' => 'sometimes|nullable|string',
            'profile.city'               => 'sometimes|string',
            'profile.country'            => 'sometimes|string',
            'profile.preferred_language' => 'sometimes|in:ar,en',
        ]);

        $updateData = [];

        if ($request->has('name'))   $updateData['name']   = $request->name;
        if ($request->has('phone'))  $updateData['phone']  = $request->phone;
        if ($request->has('avatar')) $updateData['avatar'] = $request->avatar;

        if ($request->has('profile')) {
            $currentProfile = $user->profile ?? [];
            $updateData['profile'] = array_merge($currentProfile, $request->profile);
        }

        $user->update($updateData);
        $user->load('role');

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
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة',
            'password.required'         => 'كلمة المرور الجديدة مطلوبة',
            'password.min'              => 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed'        => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('كلمة المرور الحالية غير صحيحة', 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->success(null, 'تم تغيير كلمة المرور بنجاح');
    }
}