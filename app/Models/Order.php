<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_info',
        'items',
        'pricing',
        'payment_method',
        'payment_status',
        'order_status',
        'status_history',
        'shipping_address',
        'shipping_method',
        'shipment_id',
        'notes',
        'is_gift',
        'ip_address',
        'user_agent',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'customer_info' => 'array',
        'items' => 'array',
        'pricing' => 'array',
        'status_history' => 'array',
        'shipping_address' => 'array',
        'notes' => 'array',
        'is_gift' => 'boolean',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    // Helpers
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $last = static::where('order_number', 'like', "ORD-{$year}-%")
            ->orderBy('created_at', 'desc')
            ->first();

        $newNum = $last ? intval(substr($last->order_number, -5)) + 1 : 1;
        return sprintf("ORD-%s-%05d", $year, $newNum);
    }

    public function addStatusHistory(string $status, string $note = '', $changedBy = null)
    {
        $history = $this->status_history ?? [];
        $history[] = [
            'status' => $status,
            'note' => $note,
            'changed_by' => $changedBy,
            'changed_at' => now()->toISOString(),
        ];

        $this->update([
            'order_status' => $status,
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