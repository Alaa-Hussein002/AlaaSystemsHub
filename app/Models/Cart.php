<?php
// app/Models/Cart.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'items',
        'coupon_id',
        'coupon_code',
        'discount_amount',
        'subtotal',
        'total',
        'currency',
        'items_count',
        'expires_at',
    ];

    protected $casts = [
        'items' => 'array',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'items_count' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function recalculate()
    {
        $items = collect($this->items ?? []);
        $this->subtotal = $items->sum('total_price');
        $this->items_count = $items->sum('quantity');
        $this->total = $this->subtotal - ($this->discount_amount ?? 0);
        $this->save();
    }

    public function clear()
    {
        $this->update([
            'items' => [],
            'coupon_id' => null,
            'coupon_code' => null,
            'discount_amount' => 0,
            'subtotal' => 0,
            'total' => 0,
            'items_count' => 0,
        ]);
    }
}