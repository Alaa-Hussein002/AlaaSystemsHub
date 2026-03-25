<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\AnalyticsEvent;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Setting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    use ApiResponse;

    /**
     * التصنيفات
     * GET /api/public/store/categories
     */
    public function categories()
    {
        $categories = ProductCategory::active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->with('children')
            ->get();

        return $this->success(
            ProductCategoryResource::collection($categories),
            'تصنيفات المنتجات'
        );
    }

    /**
     * قائمة المنتجات
     * GET /api/public/store/products
     */
    public function products(Request $request)
    {
        $query = Product::published();

        // فلترة حسب التصنيف
        if ($request->has('category')) {
            $category = ProductCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', (string) $category->_id);
            }
        }

        // فلترة حسب النوع
        if ($request->has('type')) {
            $query->where('product_type', $request->type);
        }

        // فلترة المميزة
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // فلترة المجانية
        if ($request->boolean('free')) {
            $query->where('pricing.is_free', true);
        }

        // البحث
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name.ar', 'like', "%{$search}%")
                  ->orWhere('name.en', 'like', "%{$search}%")
                  ->orWhere('tags', $search);
            });
        }

        // ترتيب
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('pricing.price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('pricing.price', 'desc');
                break;
            case 'popular':
                $query->orderBy('stats.sales_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->with('category')->get();

        return $this->success(
            ProductResource::collection($products),
            'قائمة المنتجات'
        );
    }

        /**
     * تفاصيل منتج
     * GET /api/public/store/products/{slug}
     */
        // 🟢 تم التعديل لتستقبل $product_slug
        public function productDetails(string $product_slug)
    {
        try {
            // 1. نبحث بالـ slug
            $product = \App\Models\Product::published()
                ->where('slug', $product_slug)
                ->with('category')
                ->first();

            if (!$product) {
                // إذا لم نجده، لنتأكد أنه ربما يكون ID
                if (preg_match('/^[a-f\d]{24}$/i', $product_slug)) {
                    $product = \App\Models\Product::published()
                        ->with('category')
                        ->find($product_slug);
                }
            }

            if (!$product) {
                return response()->json(['message' => 'المنتج غير موجود'], 404);
            }

            // 🟢 الإضافة الجديدة: فحص انتهاء الخصم وإلغائه تلقائياً
                        // 🟢 فحص انتهاء الخصم وإلغائه تلقائياً بأمان تام
            $pricing = $product->pricing ?? [];
            if (!empty($pricing['offer_end'])) {
                try {
                    // استخدام Carbon بأمان تام
                    $endDate = \Carbon\Carbon::parse($pricing['offer_end']);
                    
                    if ($endDate->isPast()) {
                        // إعادة السعر للحالة الطبيعية
                        $pricing['offer_type'] = 'none';
                        $pricing['discount_value'] = 0;
                        $pricing['is_free'] = false;
                        $pricing['offer_end'] = null; // تفريغ التاريخ كي لا يفحصه مرة أخرى
                        
                        // تحديث الداتا بيس
                        $product->update(['pricing' => $pricing]);
                        
                        // تحديث الكائن ليعرض السعر الأساسي للزائر
                        $product->pricing = $pricing;
                    }
                } catch (\Exception $e) { 
                    // تجاهل الخطأ بصمت إذا كان التاريخ غير صالح
                }
            }

            // زيادة المشاهدات
            try {
                $product->incrementViews();
            } catch (\Exception $e) { }

            // الإحصائيات 
            try {
                if (class_exists(\App\Models\AnalyticsEvent::class)) {
                    $productName = is_array($product->name) 
                        ? ($product->name['ar'] ?? $product->name['en'] ?? 'بدون اسم') 
                        : (string) $product->name;

                    \App\Models\AnalyticsEvent::create([
                        'event_type'     => 'product_view',
                        'event_category' => 'store',
                        'target_type'    => 'product',
                        'target_id'      => (string) $product->_id,
                        'target_title'   => $productName,
                        'visitor'        => [
                            'ip_hash'    => md5(request()->ip()),
                            'session_id' => session()->getId(),
                        ],
                        'page_url' => request()->fullUrl(),
                    ]);
                }
            } catch (\Exception $e) { }

            // المنتجات المشابهة
            $related = \App\Models\Product::published()
                ->where('_id', '!=', $product->_id)
                ->where('category_id', $product->category_id)
                ->limit(4)
                ->get();

            // إرجاع البيانات
            return response()->json([
                'status' => true,
                'data' => [
                    'product' => new \App\Http\Resources\ProductResource($product),
                    'related' => \App\Http\Resources\ProductResource::collection($related),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'خطأ داخلي',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * طرق الدفع المتاحة
     * GET /api/public/store/payment-methods
     */
    public function paymentMethods()
    {
        $settings = Setting::getValue('payment_settings') ?? [];
        $methods = $settings['payment_methods'] ?? [];

        $activeMethods = [];
        foreach ($methods as $key => $method) {
            if ($method['enabled'] ?? false) {
                $activeMethods[] = [
                    'key'          => $key,
                    'label'        => $method['label'] ?? $key,
                    'icon'         => $method['icon'] ?? '💰',
                    'instructions' => $method['instructions'] ?? null,
                    'accounts'     => $method['accounts'] ?? null,
                    'wallets'      => $method['wallets'] ?? null,
                    'details'      => $method['details'] ?? null,
                ];
            }
        }

        return $this->success($activeMethods, 'طرق الدفع المتاحة');
    }
}