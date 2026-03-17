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
    public function productDetails(string $slug)
    {
        $product = Product::published()
            ->where('slug', $slug)
            ->with('category')
            ->first();

        if (!$product) {
            return $this->notFound('المنتج غير موجود');
        }

        $product->incrementViews();

        // تسجيل الزيارة
        AnalyticsEvent::create([
            'event_type'     => 'product_view',
            'event_category' => 'store',
            'target_type'    => 'product',
            'target_id'      => (string) $product->_id,
            'target_title'   => $product->name['ar'] ?? $product->name,
            'visitor'        => [
                'ip_hash'    => md5(request()->ip()),
                'session_id' => session()->getId(),
            ],
            'page_url' => request()->fullUrl(),
        ]);

        // منتجات مشابهة
        $related = Product::published()
            ->where('_id', '!=', $product->_id)
            ->where('category_id', $product->category_id)
            ->limit(4)
            ->get();

        return $this->success([
            'product' => new ProductResource($product),
            'related' => ProductResource::collection($related),
        ], 'تفاصيل المنتج');
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