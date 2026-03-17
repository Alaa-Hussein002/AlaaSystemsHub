<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\CustomerOffer;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = User::where('type', 'customer')->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }
        $customers = $query->get();
        return $this->success(UserResource::collection($customers));
    }

    public function show(string $id)
    {
        $customer = User::where('type', 'customer')->find($id);
        if (!$customer) return $this->notFound('العميل غير موجود');

        $orders = Order::where('user_id', $id)->orderBy('created_at', 'desc')->get();

        return $this->success([
            'customer' => new UserResource($customer),
            'orders'   => OrderResource::collection($orders),
            'stats'    => [
                'total_orders' => $orders->count(),
                'total_spent'  => $orders->where('payment_status', 'paid')->sum(function ($o) {
                    return $o->pricing['total'] ?? 0;
                }),
            ],
        ]);
    }

    public function sendOffer(Request $request, string $id)
    {
        $request->validate([
            'title'          => 'required|string',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'message'        => 'required|array',
            'end_date'       => 'required|date|after:today',
        ]);

        $customer = User::where('type', 'customer')->find($id);
        if (!$customer) return $this->notFound('العميل غير موجود');

        $offerCode = 'OFFER-' . strtoupper(substr(uniqid(), -6));

        $offer = CustomerOffer::create([
            'title'          => $request->title,
            'user_id'        => $id,
            'product_ids'    => $request->product_ids ?? [],
            'discount_type'  => $request->discount_type,
            'discount_value' => $request->discount_value,
            'message'        => $request->message,
            'offer_code'     => $offerCode,
            'start_date'     => now()->toDateString(),
            'end_date'       => $request->end_date,
            'is_used'        => false,
            'is_active'      => true,
            'created_by'     => (string) auth()->user()->_id,
        ]);

        Notification::create([
            'user_id' => $id,
            'type'    => 'special_offer',
            'title'   => ['ar' => 'عرض خاص لك!', 'en' => 'Special Offer!'],
            'message' => $request->message,
            'icon'    => '🎁',
            'action_url' => '/store',
            'data'       => ['offer_code' => $offerCode],
            'is_read'    => false,
        ]);

        ActivityLog::log('create', 'customers', "أرسل عرض خاص للعميل: {$customer->name}", 'offer', $offer->_id);

        return $this->created($offer, 'تم إرسال العرض بنجاح');
    }
}