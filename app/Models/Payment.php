<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Payment extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'payments';

    protected $fillable = [
        'payment_number',
        'order_id',
        'user_id',
        'amount',
        'currency',
        'payment_method',    // bank_transfer | wallet | cash_point | online
        'payment_details',   // embedded dynamic document
        'status',            // pending_confirmation | confirmed | rejected | refunded
        'status_history',
        'confirmed_by',
        'confirmed_at',
        'rejected_reason',
        'refund',            // embedded { is_refunded, amount, reason, at, by }
        'metadata',          // embedded { ip, user_agent, gateway_response }
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'payment_details' => 'array',
        'status_history'  => 'array',
        'refund'          => 'array',
        'metadata'        => 'array',
        'confirmed_at'    => 'datetime',
    ];

    // ====================================
    // العلاقات
    // ====================================

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'payment_id');
    }

    // ====================================
    // Helpers
    // ====================================

    public static function generatePaymentNumber(): string
    {
        $year = date('Y');
        $last = static::where('payment_number', 'like', "PAY-{$year}-%")
                      ->orderBy('created_at', 'desc')
                      ->first();

        $newNum = $last ? intval(substr($last->payment_number, -5)) + 1 : 1;
        return sprintf("PAY-%s-%05d", $year, $newNum);
    }

    public function confirm($adminId, string $note = '')
    {
        $history = $this->status_history ?? [];
        $history[] = [
            'status'       => 'confirmed',
            'note'         => $note,
            'processed_by' => (string) $adminId,
            'processed_at' => now()->toISOString(),
        ];

        $this->update([
            'status'         => 'confirmed',
            'confirmed_by'   => (string) $adminId,
            'confirmed_at'   => now(),
            'status_history' => $history,
        ]);

        // تحديث حالة الطلب
        if ($this->order) {
            $this->order->update(['payment_status' => 'paid']);
            $this->order->addStatusHistory('confirmed', 'تم تأكيد الدفع', $adminId);
        }
    }

    public function reject($adminId, string $reason = '')
    {
        $history = $this->status_history ?? [];
        $history[] = [
            'status'       => 'rejected',
            'note'         => $reason,
            'processed_by' => (string) $adminId,
            'processed_at' => now()->toISOString(),
        ];

        $this->update([
            'status'          => 'rejected',
            'rejected_reason' => $reason,
            'status_history'  => $history,
        ]);

        if ($this->order) {
            $this->order->update(['payment_status' => 'failed']);
            $this->order->addStatusHistory('payment_rejected', $reason, $adminId);
        }
    }
}