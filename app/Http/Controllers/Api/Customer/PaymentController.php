<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponse;

    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * إرسال إثبات دفع
     * POST /api/customer/payments
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_number'       => 'required|string',
            'payment_method'     => 'required|in:bank_transfer,wallet,cash_point',
            'transfer_reference' => 'nullable|string',
            'transfer_date'      => 'nullable|date',
            'bank_name'          => 'nullable|string',
            'receipt_image'      => 'nullable|string',
            'wallet_provider'    => 'nullable|string',
            'wallet_number'      => 'nullable|string',
        ], [
            'order_number.required'   => 'رقم الطلب مطلوب',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
        ]);

        $user = $request->user();

        $order = Order::where('order_number', $request->order_number)
            ->where('user_id', (string) $user->_id)
            ->first();

        if (!$order) {
            return $this->notFound('الطلب غير موجود');
        }

        if ($order->payment_status === 'paid') {
            return $this->error('هذا الطلب مدفوع بالفعل');
        }

        try {
            $payment = $this->paymentService->createPayment(
                $order,
                $request->all(),
                $user
            );

            return $this->created(
                new PaymentResource($payment),
                'تم إرسال إثبات الدفع بنجاح، سيتم مراجعته قريباً'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * قائمة مدفوعاتي
     * GET /api/customer/payments
     */
    public function index(Request $request)
    {
        $payments = Payment::where('user_id', (string) $request->user()->_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success(
            PaymentResource::collection($payments),
            'مدفوعاتي'
        );
    }
}