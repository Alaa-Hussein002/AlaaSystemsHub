<?php

use Illuminate\Support\Facades\Route;

// Health
use App\Http\Controllers\Api\HealthCheckController;

// Auth
use App\Http\Controllers\Api\Auth\AuthController;

// Guest (Public)
use App\Http\Controllers\Api\Guest\ProfileController;
use App\Http\Controllers\Api\Guest\ProjectController;
use App\Http\Controllers\Api\Guest\SkillController;
use App\Http\Controllers\Api\Guest\ExperienceController;
use App\Http\Controllers\Api\Guest\EducationController;
use App\Http\Controllers\Api\Guest\CertificateController;
use App\Http\Controllers\Api\Guest\TestimonialController;
use App\Http\Controllers\Api\Guest\ContactController;
use App\Http\Controllers\Api\Guest\StoreController;
use App\Http\Controllers\Api\Guest\GameController;

// Customer
use App\Http\Controllers\Api\Customer\CartController;
use App\Http\Controllers\Api\Customer\OrderController;
use App\Http\Controllers\Api\Customer\PaymentController;

// Admin
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Api\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Api\Admin\SkillController as AdminSkillController;
use App\Http\Controllers\Api\Admin\ExperienceController as AdminExperienceController;
use App\Http\Controllers\Api\Admin\EducationController as AdminEducationController;
use App\Http\Controllers\Api\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Api\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Api\Admin\ProductCategoryController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Api\Admin\InvoiceController;
use App\Http\Controllers\Api\Admin\CouponController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\GameController as AdminGameController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\AnalyticsController;
use App\Http\Controllers\Api\Admin\MessageController;
use App\Http\Controllers\Api\Admin\NotificationController;
use App\Http\Controllers\Api\Admin\MediaController;

/*
|--------------------------------------------------------------------------
| فحص النظام
|--------------------------------------------------------------------------
*/
Route::get('/health', [HealthCheckController::class, 'index']);

/*
|--------------------------------------------------------------------------
| 🔐 المصادقة
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me',              [AuthController::class, 'me']);
        Route::post('/logout',         [AuthController::class, 'logout']);
        Route::put('/profile',         [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
    });
});

/*
|--------------------------------------------------------------------------
| 🌐 APIs العامة
|--------------------------------------------------------------------------
*/
Route::prefix('public')->group(function () {
    Route::get('/profile',         [ProfileController::class, 'index']);
    Route::get('/projects',        [ProjectController::class, 'index']);
    Route::get('/projects/{slug}', [ProjectController::class, 'show']);
    Route::get('/skills',          [SkillController::class, 'index']);
    Route::get('/experiences',     [ExperienceController::class, 'index']);
    Route::get('/educations',      [EducationController::class, 'index']);
    Route::get('/certificates',    [CertificateController::class, 'index']);
    Route::get('/testimonials',    [TestimonialController::class, 'index']);
    Route::post('/contact',        [ContactController::class, 'store']);

    Route::get('/store/categories',      [StoreController::class, 'categories']);
    Route::get('/store/products',        [StoreController::class, 'products']);
    Route::get('/store/products/{slug}', [StoreController::class, 'productDetails']);
    Route::get('/store/payment-methods', [StoreController::class, 'paymentMethods']);

    Route::get('/games',                    [GameController::class, 'index']);
    Route::get('/games/{slug}',             [GameController::class, 'show']);
    Route::post('/games/{slug}/play',       [GameController::class, 'play']);
    Route::post('/games/{slug}/score',      [GameController::class, 'submitScore']);
    Route::get('/games/{slug}/leaderboard', [GameController::class, 'leaderboard']);
});

/*
|--------------------------------------------------------------------------
| 🛒 APIs العميل
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->middleware('auth:sanctum')->group(function () {
    Route::get('/cart',                        [CartController::class, 'index']);
    Route::post('/cart/add',                   [CartController::class, 'add']);
    Route::put('/cart/update',                 [CartController::class, 'update']);
    Route::delete('/cart/remove/{productId}',  [CartController::class, 'remove']);
    Route::post('/cart/coupon',                [CartController::class, 'applyCoupon']);
    Route::delete('/cart/coupon',              [CartController::class, 'removeCoupon']);
    Route::delete('/cart/clear',               [CartController::class, 'clear']);

    Route::get('/orders',                       [OrderController::class, 'index']);
    Route::post('/orders',                      [OrderController::class, 'store']);
    Route::get('/orders/{orderNumber}',         [OrderController::class, 'show']);
    Route::post('/orders/{orderNumber}/cancel', [OrderController::class, 'cancel']);

    Route::get('/payments',  [PaymentController::class, 'index']);
    Route::post('/payments', [PaymentController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| 🛡️ APIs لوحة التحكم (Admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // لوحة التحكم
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // الملف الشخصي
    Route::get('/profile',  [AdminProfileController::class, 'show']);
    Route::put('/profile',  [AdminProfileController::class, 'update']);

    // المشاريع
    Route::apiResource('projects', AdminProjectController::class);

    // المهارات
    Route::apiResource('skills', AdminSkillController::class);

    // الخبرات
    Route::apiResource('experiences', AdminExperienceController::class);

    // التعليم
    Route::apiResource('educations', AdminEducationController::class);

    // الشهادات
    Route::apiResource('certificates', AdminCertificateController::class);

    // التوصيات
    Route::apiResource('testimonials', AdminTestimonialController::class);

    // تصنيفات المنتجات
    Route::apiResource('product-categories', ProductCategoryController::class);

    // المنتجات
    Route::apiResource('products', AdminProductController::class);

    // الطلبات
    Route::get('/orders',                         [AdminOrderController::class, 'index']);
    Route::get('/orders/{orderNumber}',           [AdminOrderController::class, 'show']);
    Route::put('/orders/{orderNumber}/status',    [AdminOrderController::class, 'updateStatus']);

    // المدفوعات
    Route::get('/payments',                       [AdminPaymentController::class, 'index']);
    Route::get('/payments/{paymentNumber}',       [AdminPaymentController::class, 'show']);
    Route::post('/payments/{paymentNumber}/confirm', [AdminPaymentController::class, 'confirm']);
    Route::post('/payments/{paymentNumber}/reject',  [AdminPaymentController::class, 'reject']);

    // الفواتير
    Route::get('/invoices',                  [InvoiceController::class, 'index']);
    Route::get('/invoices/{invoiceNumber}',  [InvoiceController::class, 'show']);

    // الكوبونات
    Route::apiResource('coupons', CouponController::class);

    // العملاء
    Route::get('/customers',                    [CustomerController::class, 'index']);
    Route::get('/customers/{id}',               [CustomerController::class, 'show']);
    Route::post('/customers/{id}/send-offer',   [CustomerController::class, 'sendOffer']);

    // الألعاب
    Route::apiResource('games', AdminGameController::class);

    // المستخدمين الإداريين
    Route::apiResource('users', UserController::class);

    // الأدوار والصلاحيات
    Route::apiResource('roles', RoleController::class);
    Route::get('/permissions', [RoleController::class, 'availablePermissions']);

    // الإعدادات
    Route::get('/settings',       [SettingController::class, 'index']);
    Route::get('/settings/{key}', [SettingController::class, 'show']);
    Route::put('/settings',       [SettingController::class, 'update']);

    // التحليلات
    Route::get('/analytics', [AnalyticsController::class, 'overview']);

    // الرسائل
    Route::get('/messages',                  [MessageController::class, 'index']);
    Route::get('/messages/{id}',             [MessageController::class, 'show']);
    Route::post('/messages/{id}/reply',      [MessageController::class, 'reply']);
    Route::post('/messages/{id}/spam',       [MessageController::class, 'markAsSpam']);
    Route::delete('/messages/{id}',          [MessageController::class, 'destroy']);

    // الإشعارات
    Route::get('/notifications',             [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read',  [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all',   [NotificationController::class, 'markAllAsRead']);

    // الوسائط
    Route::get('/media',          [MediaController::class, 'index']);
    Route::post('/media/upload',  [MediaController::class, 'upload']);
    Route::delete('/media/{id}',  [MediaController::class, 'destroy']);
});