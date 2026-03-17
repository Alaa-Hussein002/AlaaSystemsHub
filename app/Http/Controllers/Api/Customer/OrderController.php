<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    protected OrderService $orderService;
    protected CartService $cartService;

    public function __construct(OrderService $orderService, CartService $cartService)
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
    }

    /**
     * قائمة طلباتي
     * GET /api/customer/orders
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', (string) $request->user()->_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success(
            OrderResource::collection($orders),
            'طلباتي'
        );
    }

    /**
     * تفاصيل طلب
     * GET /api/customer/orders/{orderNumber}
     */
    public function show(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', (string) $request->user()->_id)
            ->with(['payments', 'invoice'])
            ->first();

        if (!$order) {
            return $this->notFound('الطلب غير موجود');
        }

        return $this->success(
            new OrderResource($order),
            'تفاصيل الطلب'
        );
    }

    /**
     * إنشاء طلب جديد من السلة
     * POST /api/customer/orders
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:bank_transfer,wallet,cash_point',
            'name'           => 'nullable|string',
            'email'          => 'nullable|email',
            'phone'          => 'nullable|string',
            'customer_note'  => 'nullable|string|max:500',
        ], [
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in'       => 'طريقة دفع غير مدعومة',
        ]);

        try {
            $user = $request->user();
            $cart = $this->cartService->getOrCreateCart($user->_id);

            if (empty($cart->items)) {
                return $this->error('السلة فارغة');
            }

            $order = $this->orderService->createFromCart(
                $cart,
                $request->all(),
                $user
            );

            $order->load(['payments', 'invoice']);

            return $this->created(
                new OrderResource($order),
                "تم إنشاء الطلب #{$order->order_number} بنجاح"
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * إلغاء طلب
     * POST /api/customer/orders/{orderNumber}/cancel
     */
    public function cancel(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', (string) $request->user()->_id)
            ->first();

        if (!$order) {
            return $this->notFound('الطلب غير موجود');
        }

        if (!in_array($order->order_status, ['pending', 'confirmed'])) {
            return $this->error('لا يمكن إلغاء هذا الطلب');
        }

        $order->addStatusHistory('cancelled', 'تم الإلغاء بواسطة العميل');
        $order->update([
            'cancelled_at' => now(),
        ]);

        return $this->success(
            new OrderResource($order),
            'تم إلغاء الطلب'
        );
    }
}