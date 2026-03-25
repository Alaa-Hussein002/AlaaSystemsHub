<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\PaymentMethod;
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
        if ($request->has('method')) $query->where('payment_method', $request->input('method'));

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

        $payment->confirm($request->user()->_id, $request->get('note', 'تم تأكيد الدفع'));

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

        $payment->reject($request->user()->_id, $request->reason);

        ActivityLog::log('update', 'payments', "رفض الدفعة #{$paymentNumber}", 'payment', $payment->_id);

        return $this->success(new PaymentResource($payment->fresh()), 'تم رفض الدفعة');
    }

        // ====================================
    // إدارة إعدادات طرق الدفع (Payment Methods)
    // ====================================

    public function getMethods()
    {
        try {
            $methods = PaymentMethod::orderBy('sort_order', 'asc')->get();
            
            return response()->json([
                'status' => true,
                'data'   => $methods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطأ أثناء جلب البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

        public function storeMethod(Request $request)
    {
        // 1. أخرجنا الـ Validation خارج الـ try-catch لكي يعمل بشكل سليم
        // إذا كان هناك خطأ في المدخلات، سيرجع Laravel تلقائياً خطأ 422 لـ React
        $request->validate([
            'type'      => 'required|string|in:online,bank_transfer,wallet,cod,gateway',
            'name'      => 'required|string',
            'is_active' => 'boolean',
        ]);

        try {
            // 2. تأكد أنك قمت بعمل: use App\Models\PaymentMethod; في أعلى الملف
            $method = PaymentMethod::create([
                'type'         => $request->type,
                'name'         => $request->name,
                'is_active'    => $request->boolean('is_active', true), // استخدام boolean أدق
                'details'      => $request->get('details', []),
                'instructions' => $request->get('instructions', ''),
                'sort_order'   => PaymentMethod::count() + 1
            ]);

            // 3. استبدلنا $this->success بطريقة Laravel القياسية تجنباً للأخطاء
            return response()->json([
                'status'  => true,
                'message' => 'تمت إضافة طريقة الدفع بنجاح',
                'data'    => $method
            ], 200);
            
        } catch (\Exception $e) {
            // الآن إذا حدث خطأ 500، سيخبرك بالضبط ما هو الخطأ!
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ برمجي: ' . $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile()
            ], 500);
        }
    }

    public function updateMethod(Request $request, $id)
    {
        try {
            $method = PaymentMethod::find($id);
            if (!$method) return $this->notFound('طريقة الدفع غير موجودة');

            $method->update($request->only(['name', 'is_active', 'details', 'instructions', 'sort_order']));

            return $this->success($method, 'تم تحديث طريقة الدفع بنجاح');
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطأ داخلي: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteMethod($id)
    {
        try {
            $method = PaymentMethod::find($id);
            if (!$method) return $this->notFound('طريقة الدفع غير موجودة');

            $method->delete();

            return $this->success(null, 'تم حذف طريقة الدفع');
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطأ داخلي: ' . $e->getMessage()
            ], 500);
        }
    }
}