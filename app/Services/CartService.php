<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;

class CartService
{
    public function getOrCreateCart($userId = null, $sessionId = null): Cart
    {
        $query = Cart::query();

        if ($userId) {
            $query->where('user_id', (string) $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $cart = $query->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id'         => $userId ? (string) $userId : null,
                'session_id'      => $sessionId,
                'items'           => [],
                'subtotal'        => 0,
                'total'           => 0,
                'discount_amount' => 0,
                'items_count'     => 0,
                'currency'        => 'USD',
                'expires_at'      => now()->addDays(7),
            ]);
        }

        return $cart;
    }

    public function addItem(Cart $cart, string $productId, int $quantity = 1): Cart
    {
        $product = Product::find($productId);

        if (!$product) {
            throw new \Exception('المنتج غير موجود');
        }

        if ($product->status !== 'published' || !$product->is_active) {
            throw new \Exception('المنتج غير متاح حالياً');
        }

        // للمنتجات الرقمية، الكمية دائماً 1
        if ($product->product_type === 'digital') {
            $quantity = 1;
        }

        $items = collect($cart->items ?? []);

        // تحقق هل المنتج موجود مسبقاً
        $existingIndex = $items->search(function ($item) use ($productId) {
            return ($item['product_id'] ?? '') === $productId;
        });

        $price = $product->pricing['price'] ?? 0;

        if ($existingIndex !== false) {
            if ($product->product_type === 'digital') {
                throw new \Exception('المنتج الرقمي موجود بالفعل في السلة');
            }
            $items[$existingIndex]['quantity'] += $quantity;
            $items[$existingIndex]['total_price'] = $items[$existingIndex]['quantity'] * $price;
        } else {
            $items->push([
                'product_id'        => $productId,
                'product_name'      => $product->name,
                'product_type'      => $product->product_type,
                'product_thumbnail' => $product->media['thumbnail'] ?? null,
                'quantity'          => $quantity,
                'unit_price'        => $price,
                'total_price'       => $price * $quantity,
            ]);
        }

        $cart->items = $items->values()->toArray();
        $this->recalculate($cart);
        $cart->save();

        return $cart;
    }

    public function updateItemQuantity(Cart $cart, string $productId, int $quantity): Cart
    {
        if ($quantity < 1) {
            return $this->removeItem($cart, $productId);
        }

        $items = collect($cart->items ?? []);

        $existingIndex = $items->search(function ($item) use ($productId) {
            return ($item['product_id'] ?? '') === $productId;
        });

        if ($existingIndex === false) {
            throw new \Exception('المنتج غير موجود في السلة');
        }

        $items[$existingIndex]['quantity'] = $quantity;
        $items[$existingIndex]['total_price'] = $quantity * $items[$existingIndex]['unit_price'];

        $cart->items = $items->values()->toArray();
        $this->recalculate($cart);
        $cart->save();

        return $cart;
    }

    public function removeItem(Cart $cart, string $productId): Cart
    {
        $items = collect($cart->items ?? []);

        $cart->items = $items->reject(function ($item) use ($productId) {
            return ($item['product_id'] ?? '') === $productId;
        })->values()->toArray();

        $this->recalculate($cart);
        $cart->save();

        return $cart;
    }

    public function applyCoupon(Cart $cart, string $couponCode): Cart
    {
        $coupon = Coupon::where('code', strtoupper($couponCode))->first();

        if (!$coupon) {
            throw new \Exception('كوبون غير صالح');
        }

        if (!$coupon->isValid()) {
            throw new \Exception('الكوبون منتهي الصلاحية أو تم استنفاد الاستخدامات');
        }

        $discount = $coupon->calculateDiscount($cart->subtotal);

        $cart->coupon_id = (string) $coupon->_id;
        $cart->coupon_code = $coupon->code;
        $cart->discount_amount = $discount;

        $this->recalculate($cart);
        $cart->save();

        return $cart;
    }

    public function removeCoupon(Cart $cart): Cart
    {
        $cart->coupon_id = null;
        $cart->coupon_code = null;
        $cart->discount_amount = 0;

        $this->recalculate($cart);
        $cart->save();

        return $cart;
    }

    public function clear(Cart $cart): Cart
    {
        $cart->update([
            'items'           => [],
            'coupon_id'       => null,
            'coupon_code'     => null,
            'discount_amount' => 0,
            'subtotal'        => 0,
            'total'           => 0,
            'items_count'     => 0,
        ]);

        return $cart;
    }

    private function recalculate(Cart &$cart): void
    {
        $items = collect($cart->items ?? []);
        $cart->subtotal = $items->sum('total_price');
        $cart->items_count = $items->sum('quantity');
        $cart->total = max(0, $cart->subtotal - ($cart->discount_amount ?? 0));
    }
}