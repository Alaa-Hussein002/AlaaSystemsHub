<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class OrderService
{
    public function createFromCart(Cart $cart, array $orderData, User $user): Order
    {
        if (empty($cart->items)) {
            throw new \Exception('السلة فارغة');
        }

        // إنشاء رقم الطلب
        $orderNumber = Order::generateOrderNumber();

        // تجهيز عناصر الطلب مع روابط التحميل للمنتجات الرقمية
        $items = [];
        foreach ($cart->items as $cartItem) {
            $product = Product::find($cartItem['product_id']);

            $item = [
                'product_id'        => $cartItem['product_id'],
                'product_name'      => $cartItem['product_name'],
                'product_type'      => $cartItem['product_type'],
                'product_thumbnail' => $cartItem['product_thumbnail'],
                'quantity'          => $cartItem['quantity'],
                'unit_price'        => $cartItem['unit_price'],
                'discount'          => 0,
                'total_price'       => $cartItem['total_price'],
            ];

            // إضافة رابط التحميل للمنتجات الرقمية
            if ($cartItem['product_type'] === 'digital' && $product) {
                $item['digital_asset'] = [
                    'file_url'        => $product->digital_asset['file_url'] ?? null,
                    'download_token'  => Str::random(64),
                    'downloads_used'  => 0,
                    'download_limit'  => $product->digital_asset['download_limit'] ?? 5,
                    'download_expiry' => now()->addDays(30)->toISOString(),
                ];
            }

            $items[] = $item;
        }

        // إنشاء الطلب
        $order = Order::create([
            'order_number'   => $orderNumber,
            'user_id'        => (string) $user->_id,
            'customer_info'  => [
                'name'  => $orderData['name'] ?? $user->name,
                'email' => $orderData['email'] ?? $user->email,
                'phone' => $orderData['phone'] ?? $user->phone,
            ],
            'items'          => $items,
            'pricing'        => [
                'subtotal'        => $cart->subtotal,
                'discount_amount' => $cart->discount_amount ?? 0,
                'coupon_code'     => $cart->coupon_code,
                'coupon_id'       => $cart->coupon_id,
                'tax_amount'      => 0,
                'tax_rate'        => 0,
                'shipping_cost'   => 0,
                'total'           => $cart->total,
                'currency'        => $cart->currency ?? 'USD',
            ],
            'payment_method' => $orderData['payment_method'] ?? 'bank_transfer',
            'payment_status' => 'pending',
            'order_status'   => 'pending',
            'status_history' => [
                [
                    'status'     => 'pending',
                    'note'       => 'تم إنشاء الطلب',
                    'changed_by' => null,
                    'changed_at' => now()->toISOString(),
                ],
            ],
            'notes' => [
                'customer_note' => $orderData['customer_note'] ?? '',
                'admin_note'    => '',
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);

        // تحديث عدد المبيعات
        foreach ($cart->items as $cartItem) {
            $product = Product::find($cartItem['product_id']);
            if ($product) {
                $product->incrementSales($cartItem['quantity']);
            }
        }

        // تحديث استخدام الكوبون
        if ($cart->coupon_id) {
            Coupon::where('_id', $cart->coupon_id)->increment('used_count');
        }

        // تفريغ السلة
        $cart->update([
            'items'           => [],
            'coupon_id'       => null,
            'coupon_code'     => null,
            'discount_amount' => 0,
            'subtotal'        => 0,
            'total'           => 0,
            'items_count'     => 0,
        ]);

        // إشعار للمدير
        $admin = User::where('type', 'admin')->first();
        if ($admin) {
            Notification::create([
                'user_id'    => (string) $admin->_id,
                'type'       => 'new_order',
                'title'      => ['ar' => 'طلب جديد!', 'en' => 'New Order!'],
                'message'    => [
                    'ar' => "طلب جديد #{$orderNumber} بقيمة {$cart->total} {$cart->currency}",
                    'en' => "New order #{$orderNumber} worth {$cart->total} {$cart->currency}",
                ],
                'icon'       => '🛒',
                'action_url' => "/admin/orders/{$orderNumber}",
                'data'       => [
                    'order_id'     => (string) $order->_id,
                    'order_number' => $orderNumber,
                    'amount'       => $cart->total,
                ],
                'is_read' => false,
            ]);
        }

        return $order;
    }
}