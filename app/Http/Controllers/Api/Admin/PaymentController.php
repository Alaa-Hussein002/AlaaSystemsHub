<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\ActivityLog;
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

    public function index(Request $request)
    {
        $query = Payment::orderBy('created_at', 'desc');
        if ($request->has('status')) $query->where('status', $request->status);
        if ($request->has('method')) $query->where('payment_method', $request->method);

        $payments = $query->get();
        return $this->success(PaymentResource::collection($payments));
    }

    public function show(string $paymentNumber)
    {
        $payment = Payment::where('payment_number', $paymentNumber)->with('order')->first();
        if (!$payment) return $this->notFound('الدفعة غير موجودة');
        return $this->success(new PaymentResource($payment));
    }

    public function confirm(Request $request, string $paymentNumber)
    {
        $payment = Payment::where('payment_number', $paymentNumber)->first();
        if (!$payment) return $this->notFound('الدفعة غير موجودة');

        if ($payment->status === 'confirmed') {
            return $this->error('الدفعة مؤكدة بالفعل');
        }

        $payment->confirm(auth()->user()->_id, $request->get('note', 'تم تأكيد الدفع'));

        // إنشاء فاتورة
        $order = $payment->order;
        if ($order) {
            $this->paymentService->generateInvoice($order, $payment);
        }

        ActivityLog::log('update', 'payments', "أكّد الدفعة #{$paymentNumber}", 'payment', $payment->_id);

        return $this->success(new PaymentResource($payment->fresh()), 'تم تأكيد الدفعة');
    }

    public function reject(Request $request, string $paymentNumber)
    {
        $request->validate(['reason' => 'required|string']);

        $payment = Payment::where('payment_number', $paymentNumber)->first();
        if (!$payment) return $this->notFound('الدفعة غير موجودة');

        $payment->reject(auth()->user()->_id, $request->reason);

        ActivityLog::log('update', 'payments', "رفض الدفعة #{$paymentNumber}", 'payment', $payment->_id);

        return $this->success(new PaymentResource($payment->fresh()), 'تم رفض الدفعة');
    }
}