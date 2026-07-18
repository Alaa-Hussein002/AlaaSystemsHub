<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// ========================================
// Controllers - Auth & Health
// ========================================
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\HealthCheckController;
use App\Http\Controllers\Api\Auth\AuthController;

// ========================================
// Guest Controllers
// ========================================
use App\Http\Controllers\Api\Guest\ProfileController;
use App\Http\Controllers\Api\Guest\ProjectController;
use App\Http\Controllers\Api\Guest\SkillController;
use App\Http\Controllers\Api\Guest\ExperienceController;
use App\Http\Controllers\Api\Guest\EducationController;
use App\Http\Controllers\Api\Guest\CertificateController;
use App\Http\Controllers\Api\Guest\TestimonialController;
use App\Http\Controllers\Api\Guest\ContactController;
use App\Http\Controllers\Api\Guest\ArticleController;
use App\Http\Controllers\Api\Guest\StoreController;
use App\Http\Controllers\Api\Guest\GameController;

// ========================================
// Admin Controllers
// ========================================
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
use App\Http\Controllers\Api\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Api\Public\ArticleController as PublicArticleController;

// ========================================
// 🔧 Temporary Setup Endpoint
// احذفه بعد تشغيل الـ Setup!
// ========================================
Route::get('/setup-database-now-please', function() {
    try {
        $results = [];
        
        // Run migrations
        Artisan::call('migrate', ['--force' => true]);
        $results['migrations'] = Artisan::output();
        
        // Seed roles
        Artisan::call('db:seed', [
            '--class' => 'RoleSeeder',
            '--force' => true
        ]);
        $results['roles'] = Artisan::output();
        
        // Seed admin user
        Artisan::call('db:seed', [
            '--class' => 'AdminUserSeeder',
            '--force' => true
        ]);
        $results['admin'] = Artisan::output();
        
        // Storage link
        Artisan::call('storage:link');
        $results['storage'] = Artisan::output();
        
        // Cache
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        
        // Check tables
        $tables = DB::select('SHOW TABLES');
        $results['tables_count'] = count($tables);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Database setup completed!',
            'results' => $results,
            'tables' => $tables
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

// ========================================
// 🏥 Health Check
// ========================================
Route::get('/health', [HealthCheckController::class, 'index']);

// ========================================
// 🔐 Auth Routes
// ========================================

Route::prefix('auth')->group(function () {
    
    // ===== Public Routes - مسارات عامة =====
    Route::post('/login', [AuthController::class, 'login']);
    
    // CUSTOMER_FEATURE: Register temporarily disabled
    // Route::post('/register', [AuthController::class, 'register']);
    
    // Password Reset Routes - مسارات إعادة تعيين كلمة المرور
    Route::prefix('password')->group(function () {
        Route::post('/forgot', [PasswordResetController::class, 'forgotPassword']);
        Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp']);
        Route::post('/reset', [PasswordResetController::class, 'resetPassword']);
        Route::post('/resend-otp', [PasswordResetController::class, 'resendOtp']);
    });

    // ===== Protected Routes - مسارات محمية =====
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/check', [AuthController::class, 'checkAuth']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
    });
});

// ========================================
// 🌐 Public/Guest Routes
// ========================================

Route::prefix('public')->group(function () {
    // Profile & Portfolio
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{slug}', [ProjectController::class, 'show']);
    Route::get('/skills', [SkillController::class, 'index']);
    Route::get('/experiences', [ExperienceController::class, 'index']);
    Route::get('/educations', [EducationController::class, 'index']);
    Route::get('/educations/{id}', [EducationController::class, 'show']);
    Route::get('/certificates', [CertificateController::class, 'index']);
    Route::get('/certificates/{id}', [CertificateController::class, 'show']);
    Route::get('/testimonials', [TestimonialController::class, 'index']);
    
    // Contact
    Route::post('/contact', [ContactController::class, 'store']);

    // Articles
    Route::get('/articles', [PublicArticleController::class, 'index']);
    Route::get('/articles/featured', [PublicArticleController::class, 'featured']);
    Route::get('/articles/categories', [PublicArticleController::class, 'getCategories']);
    Route::get('/articles/{slug}', [PublicArticleController::class, 'show']);
    Route::get('/articles/{slug}/related', [PublicArticleController::class, 'related']);


    // Store (disabled for MVP)
    Route::get('/store/categories', [StoreController::class, 'categories']);
    Route::get('/store/products', [StoreController::class, 'products']);
    Route::get('/store/products/{slug}', [StoreController::class, 'productDetails']);
    Route::get('/store/payment-methods', [StoreController::class, 'paymentMethods']);

    // Games (disabled for MVP)
    Route::get('/games', [GameController::class, 'index']);
    Route::get('/games/{slug}', [GameController::class, 'show']);
    Route::post('/games/{slug}/play', [GameController::class, 'play']);
    Route::post('/games/{slug}/score', [GameController::class, 'submitScore']);
    Route::get('/games/{slug}/leaderboard', [GameController::class, 'leaderboard']);
});

// ========================================
// 👤 Customer Routes (auth required)
// ========================================

Route::prefix('customer')->middleware('auth:sanctum')->group(function () {
    // Cart
    Route::get('/cart', [\App\Http\Controllers\Api\Customer\CartController::class, 'index']);
    Route::post('/cart/add', [\App\Http\Controllers\Api\Customer\CartController::class, 'add']);
    Route::put('/cart/update', [\App\Http\Controllers\Api\Customer\CartController::class, 'update']);
    Route::delete('/cart/remove/{productId}', [\App\Http\Controllers\Api\Customer\CartController::class, 'remove']);
    Route::post('/cart/coupon', [\App\Http\Controllers\Api\Customer\CartController::class, 'applyCoupon']);
    Route::delete('/cart/coupon', [\App\Http\Controllers\Api\Customer\CartController::class, 'removeCoupon']);
    Route::delete('/cart/clear', [\App\Http\Controllers\Api\Customer\CartController::class, 'clear']);

    // Orders
    Route::get('/orders', [\App\Http\Controllers\Api\Customer\OrderController::class, 'index']);
    Route::post('/orders', [\App\Http\Controllers\Api\Customer\OrderController::class, 'store']);
    Route::get('/orders/{orderNumber}', [\App\Http\Controllers\Api\Customer\OrderController::class, 'show']);
    Route::post('/orders/{orderNumber}/cancel', [\App\Http\Controllers\Api\Customer\OrderController::class, 'cancel']);

    // Payments
    Route::get('/payments', [\App\Http\Controllers\Api\Customer\PaymentController::class, 'index']);
    Route::post('/payments', [\App\Http\Controllers\Api\Customer\PaymentController::class, 'store']);
});

// ========================================
// 🛡️ Admin Routes (auth + admin middleware)
// ========================================

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Profile
    Route::get('/profile', [AdminProfileController::class, 'show']);
    Route::put('/profile', [AdminProfileController::class, 'update']);

    // Projects
    Route::apiResource('projects', AdminProjectController::class);

    // Skills
    Route::apiResource('skills', AdminSkillController::class);

    // Experiences
    Route::apiResource('experiences', AdminExperienceController::class);

    // Education
    Route::apiResource('educations', AdminEducationController::class);

    // Certificates
    Route::apiResource('certificates', AdminCertificateController::class);

    // Testimonials
    Route::apiResource('testimonials', AdminTestimonialController::class);

    // Product Categories
    Route::apiResource('product-categories', ProductCategoryController::class);

    // Products
    Route::apiResource('products', AdminProductController::class);

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/{orderNumber}', [AdminOrderController::class, 'show']);
    Route::put('/orders/{orderNumber}/status', [AdminOrderController::class, 'updateStatus']);

    // Payments
    Route::get('/payments', [AdminPaymentController::class, 'index']);
    Route::get('/payments/{paymentNumber}', [AdminPaymentController::class, 'show']);
    Route::post('/payments/{paymentNumber}/confirm', [AdminPaymentController::class, 'confirm']);
    Route::post('/payments/{paymentNumber}/reject', [AdminPaymentController::class, 'reject']);

    // Payment Methods
    Route::get('/payment-methods', [AdminPaymentController::class, 'getMethods']);
    Route::post('/payment-methods', [AdminPaymentController::class, 'storeMethod']);
    Route::put('/payment-methods/{id}', [AdminPaymentController::class, 'updateMethod']);
    Route::delete('/payment-methods/{id}', [AdminPaymentController::class, 'deleteMethod']);

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/{invoiceNumber}', [InvoiceController::class, 'show']);

    // Coupons
    Route::apiResource('coupons', CouponController::class);

    // Customers
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::post('/customers/{id}/send-offer', [CustomerController::class, 'sendOffer']);

    // Games
    Route::apiResource('games', AdminGameController::class);

    // Users
    Route::apiResource('users', UserController::class);

    // Roles & Permissions
    Route::apiResource('roles', RoleController::class);
    Route::get('/permissions', [RoleController::class, 'availablePermissions']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index']);
    Route::get('/settings/{key}', [SettingController::class, 'show']);
    Route::put('/settings', [SettingController::class, 'update']);

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'overview']);

    // Messages (Contact Messages)
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/messages/{id}', [MessageController::class, 'show']);
    Route::post('/messages/{id}/reply', [MessageController::class, 'reply']);
    Route::post('/messages/{id}/spam', [MessageController::class, 'markAsSpam']);
    Route::delete('/messages/{id}', [MessageController::class, 'destroy']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Media
    Route::get('/media', [MediaController::class, 'index']);
    Route::post('/media/upload', [MediaController::class, 'upload']);
    Route::get('/media/{id}', [MediaController::class, 'show']);
    Route::delete('/media/{id}', [MediaController::class, 'destroy']);

    // Articles
    Route::apiResource('articles', AdminArticleController::class);
});