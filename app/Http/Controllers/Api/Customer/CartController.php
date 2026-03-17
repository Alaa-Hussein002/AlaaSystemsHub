<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponse;

    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * عرض السلة
     * GET /api/customer/cart
     */
    public function index(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart(
            $request->user()->_id
        );

        return $this->success(
            new CartResource($cart),
            'سلة التسوق'
        );
    }

    /**
     * إضافة منتج
     * POST /api/customer/cart/add
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
            'quantity'   => 'nullable|integer|min:1',
        ], [
            'product_id.required' => 'معرّف المنتج مطلوب',
        ]);

        try {
            $cart = $this->cartService->getOrCreateCart($request->user()->_id);
            $cart = $this->cartService->addItem(
                $cart,
                $request->product_id,
                $request->get('quantity', 1)
            );

            return $this->success(
                new CartResource($cart),
                'تمت الإضافة إلى السلة'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * تحديث الكمية
     * PUT /api/customer/cart/update
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
            'quantity'   => 'required|integer|min:0',
        ]);

        try {
            $cart = $this->cartService->getOrCreateCart($request->user()->_id);
            $cart = $this->cartService->updateItemQuantity(
                $cart,
                $request->product_id,
                $request->quantity
            );

            return $this->success(
                new CartResource($cart),
                'تم تحديث السلة'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * حذف منتج من السلة
     * DELETE /api/customer/cart/remove/{productId}
     */
    public function remove(Request $request, string $productId)
    {
        try {
            $cart = $this->cartService->getOrCreateCart($request->user()->_id);
            $cart = $this->cartService->removeItem($cart, $productId);

            return $this->success(
                new CartResource($cart),
                'تم حذف المنتج من السلة'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * تطبيق كوبون
     * POST /api/customer/cart/coupon
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ], [
            'coupon_code.required' => 'كود الكوبون مطلوب',
        ]);

        try {
            $cart = $this->cartService->getOrCreateCart($request->user()->_id);
            $cart = $this->cartService->applyCoupon($cart, $request->coupon_code);

            return $this->success(
                new CartResource($cart),
                'تم تطبيق الكوبون بنجاح'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * إزالة الكوبون
     * DELETE /api/customer/cart/coupon
     */
    public function removeCoupon(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart($request->user()->_id);
        $cart = $this->cartService->removeCoupon($cart);

        return $this->success(
            new CartResource($cart),
            'تم إزالة الكوبون'
        );
    }

    /**
     * تفريغ السلة
     * DELETE /api/customer/cart/clear
     */
    public function clear(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart($request->user()->_id);
        $cart = $this->cartService->clear($cart);

        return $this->success(
            new CartResource($cart),
            'تم تفريغ السلة'
        );
    }
}