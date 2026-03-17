<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Order::orderBy('created_at', 'desc');

        if ($request->has('status'))         $query->where('order_status', $request->status);
        if ($request->has('payment_status')) $query->where('payment_status', $request->payment_status);
        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhere('customer_info.name', 'like', "%{$s}%")
                  ->orWhere('customer_info.email', 'like', "%{$s}%");
            });
        }

        $orders = $query->get();
        return $this->success(OrderResource::collection($orders));
    }

    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['payments', 'invoice'])
            ->first();
        if (!$order) return $this->notFound('الطلب غير موجود');
        return $this->success(new OrderResource($order));
    }

    public function updateStatus(Request $request, string $orderNumber)
    {
        $request->validate([
            'status' => 'required|in:confirmed,processing,shipped,delivered,completed,cancelled',
            'note'   => 'nullable|string',
        ]);

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) return $this->notFound('الطلب غير موجود');

        $order->addStatusHistory($request->status, $request->note ?? '', auth()->user()->_id);

        if ($request->status === 'completed') {
            $order->update(['completed_at' => now()]);
        }
        if ($request->status === 'cancelled') {
            $order->update(['cancelled_at' => now()]);
        }

        // إشعار العميل
        if ($order->user_id) {
            Notification::create([
                'user_id' => $order->user_id,
                'type'    => 'order_update',
                'title'   => ['ar' => 'تحديث طلبك', 'en' => 'Order Update'],
                'message' => [
                    'ar' => "تم تحديث حالة طلبك #{$orderNumber} إلى: {$request->status}",
                    'en' => "Your order #{$orderNumber} status updated to: {$request->status}",
                ],
                'icon'       => '📦',
                'action_url' => "/orders/{$orderNumber}",
                'data'       => ['order_number' => $orderNumber, 'status' => $request->status],
                'is_read'    => false,
            ]);
        }

        ActivityLog::log('update', 'orders', "حدّث حالة الطلب #{$orderNumber} إلى {$request->status}", 'order', $order->_id);

        return $this->success(new OrderResource($order), 'تم تحديث حالة الطلب');
    }
}