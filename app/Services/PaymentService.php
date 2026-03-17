<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;

class PaymentService
{
    public function createPayment(Order $order, array $paymentData, User $user): Payment
    {
        $paymentNumber = Payment::generatePaymentNumber();

        $payment = Payment::create([
            'payment_number' => $paymentNumber,
            'order_id'       => (string) $order->_id,
            'user_id'        => (string) $user->_id,
            'amount'         => $order->pricing['total'],
            'currency'       => $order->pricing['currency'] ?? 'USD',
            'payment_method' => $paymentData['payment_method'],
            'payment_details' => [
                'method_type'       => $paymentData['payment_method'],
                'bank_name'         => $paymentData['bank_name'] ?? null,
                'account_number'    => $paymentData['account_number'] ?? null,
                'transfer_reference'=> $paymentData['transfer_reference'] ?? null,
                'transfer_date'     => $paymentData['transfer_date'] ?? null,
                'receipt_image'     => $paymentData['receipt_image'] ?? null,
                'wallet_provider'   => $paymentData['wallet_provider'] ?? null,
                'wallet_number'     => $paymentData['wallet_number'] ?? null,
            ],
            'status' => 'pending_confirmation',
            'status_history' => [
                [
                    'status'       => 'pending_confirmation',
                    'note'         => 'في انتظار تأكيد الدفع',
                    'processed_by' => null,
                    'processed_at' => now()->toISOString(),
                ],
            ],
            'refund' => [
                'is_refunded'   => false,
                'refund_amount' => 0,
            ],
            'metadata' => [
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->header('User-Agent'),
            ],
        ]);

        // إشعار المدير
        $admin = User::where('type', 'admin')->first();
        if ($admin) {
            Notification::create([
                'user_id'    => (string) $admin->_id,
                'type'       => 'payment_received',
                'title'      => ['ar' => 'دفعة جديدة!', 'en' => 'New Payment!'],
                'message'    => [
                    'ar' => "دفعة جديدة #{$paymentNumber} للطلب #{$order->order_number}",
                    'en' => "New payment #{$paymentNumber} for order #{$order->order_number}",
                ],
                'icon'       => '💳',
                'action_url' => "/admin/payments/{$paymentNumber}",
                'data'       => [
                    'payment_id'   => (string) $payment->_id,
                    'order_number' => $order->order_number,
                    'amount'       => $order->pricing['total'],
                ],
                'is_read' => false,
            ]);
        }

        return $payment;
    }

    public function generateInvoice(Order $order, Payment $payment): Invoice
    {
        $settings = Setting::getValue('site_settings') ?? [];
        $paymentSettings = Setting::getValue('payment_settings') ?? [];

        $invoiceNumber = Invoice::generateInvoiceNumber();

        $invoiceItems = [];
        foreach ($order->items as $item) {
            $invoiceItems[] = [
                'description' => $item['product_name']['ar'] ?? $item['product_name'] ?? 'منتج',
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'discount'    => $item['discount'] ?? 0,
                'total'       => $item['total_price'],
            ];
        }

        return Invoice::create([
            'invoice_number' => $invoiceNumber,
            'order_id'       => (string) $order->_id,
            'user_id'        => (string) $order->user_id,
            'payment_id'     => (string) $payment->_id,
            'seller_info'    => [
                'name'    => $settings['site_name']['ar'] ?? 'Alaa Systems Hub',
                'email'   => 'billing@alaasystems.com',
                'phone'   => '+967771234567',
                'address' => 'صنعاء، اليمن',
            ],
            'buyer_info'     => $order->customer_info,
            'items'          => $invoiceItems,
            'subtotal'       => $order->pricing['subtotal'],
            'discount_total' => $order->pricing['discount_amount'] ?? 0,
            'tax_total'      => $order->pricing['tax_amount'] ?? 0,
            'grand_total'    => $order->pricing['total'],
            'currency'       => $order->pricing['currency'] ?? 'USD',
            'status'         => 'paid',
            'issue_date'     => now()->toDateString(),
            'due_date'       => now()->toDateString(),
            'paid_date'      => now()->toDateString(),
            'notes'          => 'شكراً لثقتكم!',
            'terms'          => 'لا يمكن استرداد المبلغ للمنتجات الرقمية بعد التحميل',
        ]);
    }
}