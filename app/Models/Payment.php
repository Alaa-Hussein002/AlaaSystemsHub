<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'order_id',
        'user_id',
        'amount',
        'currency',
        'payment_method',
        'payment_details',
        'status',
        'status_history',
        'confirmed_by',
        'confirmed_at',
        'rejected_reason',
        'refund',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'status_history' => 'array',
        'refund' => 'array',
        'metadata' => 'array',
        'confirmed_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    // Helpers
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
            'status' => 'confirmed',
            'note' => $note,
            'processed_by' => $adminId,
            'processed_at' => now()->toISOString(),
        ];

        $this->update([
            'status' => 'confirmed',
            'confirmed_by' => $adminId,
            'confirmed_at' => now(),
            'status_history' => $history,
        ]);

        if ($this->order) {
            $this->order->update(['payment_status' => 'paid']);
            $this->order->addStatusHistory('confirmed', 'تم تأكيد الدفع', $adminId);
        }
    }

    public function reject($adminId, string $reason = '')
    {
        $history = $this->status_history ?? [];
        $history[] = [
            'status' => 'rejected',
            'note' => $reason,
            'processed_by' => $adminId,
            'processed_at' => now()->toISOString(),
        ];

        $this->update([
            'status' => 'rejected',
            'rejected_reason' => $reason,
            'status_history' => $history,
        ]);

        if ($this->order) {
            $this->order->update(['payment_status' => 'failed']);
            $this->order->addStatusHistory('payment_rejected', $reason, $adminId);
        }
    }
}