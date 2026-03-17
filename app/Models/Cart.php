<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Cart extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'carts';

    protected $fillable = [
        'user_id',
        'session_id',
        'items',           // embedded array
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
        'items'           => 'array',
        'discount_amount' => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'total'           => 'decimal:2',
        'items_count'     => 'integer',
        'expires_at'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    // ====================================
    // Methods
    // ====================================

    public function addItem(Product $product, int $quantity = 1)
    {
        $items = collect($this->items ?? []);

        $existingIndex = $items->search(function ($item) use ($product) {
            return ($item['product_id'] ?? '') === (string) $product->_id;
        });

        if ($existingIndex !== false) {
            $items[$existingIndex]['quantity'] += $quantity;
            $items[$existingIndex]['total_price'] =
                $items[$existingIndex]['quantity'] * $items[$existingIndex]['unit_price'];
        } else {
            $items->push([
                'product_id'        => (string) $product->_id,
                'product_name'      => $product->name,
                'product_thumbnail' => $product->media['thumbnail'] ?? null,
                'quantity'          => $quantity,
                'unit_price'        => $product->pricing['price'] ?? 0,
                'total_price'       => ($product->pricing['price'] ?? 0) * $quantity,
            ]);
        }

        $this->items = $items->toArray();
        $this->recalculate();
    }

    public function removeItem(string $productId)
    {
        $items = collect($this->items ?? []);
        $this->items = $items->reject(function ($item) use ($productId) {
            return ($item['product_id'] ?? '') === $productId;
        })->values()->toArray();
        $this->recalculate();
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
            'items'           => [],
            'coupon_id'       => null,
            'coupon_code'     => null,
            'discount_amount' => 0,
            'subtotal'        => 0,
            'total'           => 0,
            'items_count'     => 0,
        ]);
    }
}