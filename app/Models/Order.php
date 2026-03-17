<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_info',     // embedded { name, email, phone }
        'items',             // embedded array
        'pricing',           // embedded { subtotal, discount, tax, shipping, total, currency }
        'payment_method',
        'payment_status',    // pending | paid | failed | refunded
        'order_status',      // pending | confirmed | processing | shipped | delivered | completed | cancelled
        'status_history',    // embedded array
        'shipping_address',  // embedded or null
        'shipping_method',
        'shipment_id',
        'notes',             // { customer_note, admin_note }
        'is_gift',
        'ip_address',
        'user_agent',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'customer_info'  => 'array',
        'items'          => 'array',
        'pricing'        => 'array',
        'status_history' => 'array',
        'shipping_address' => 'array',
        'notes'          => 'array',
        'is_gift'        => 'boolean',
        'completed_at'   => 'datetime',
        'cancelled_at'   => 'datetime',
    ];

    // ====================================
    // العلاقات
    // ====================================

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'order_id');
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class, 'order_id');
    }

    // ====================================
    // Helpers
    // ====================================

    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $last = static::where('order_number', 'like', "ORD-{$year}-%")
                      ->orderBy('created_at', 'desc')
                      ->first();

        if ($last) {
            $lastNum = intval(substr($last->order_number, -5));
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }

        return sprintf("ORD-%s-%05d", $year, $newNum);
    }

    public function addStatusHistory(string $status, string $note = '', $changedBy = null)
    {
        $history = $this->status_history ?? [];
        $history[] = [
            'status'     => $status,
            'note'       => $note,
            'changed_by' => $changedBy ? (string) $changedBy : null,
            'changed_at' => now()->toISOString(),
        ];

        $this->update([
            'order_status'   => $status,
            'status_history' => $history,
        ]);
    }

    public function getTotalAttribute()
    {
        return $this->pricing['total'] ?? 0;
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('order_status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('order_status', 'completed');
    }
}