<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Coupon extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'coupons';

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',         // percentage | fixed
        'discount_value',
        'minimum_order_amount',
        'maximum_discount_amount',
        'applicable_products',
        'applicable_categories',
        'applicable_users',
        'usage_limit',
        'usage_per_user',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'discount_value'         => 'decimal:2',
        'minimum_order_amount'   => 'decimal:2',
        'maximum_discount_amount'=> 'decimal:2',
        'applicable_products'    => 'array',
        'applicable_categories'  => 'array',
        'applicable_users'       => 'array',
        'usage_limit'            => 'integer',
        'usage_per_user'         => 'integer',
        'used_count'             => 'integer',
        'is_active'              => 'boolean',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($this->start_date && now()->lt($this->start_date)) return false;
        if ($this->end_date && now()->gt($this->end_date)) return false;

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->minimum_order_amount && $amount < $this->minimum_order_amount) {
            return 0;
        }

        $discount = $this->discount_type === 'percentage'
            ? ($amount * $this->discount_value / 100)
            : $this->discount_value;

        if ($this->maximum_discount_amount) {
            $discount = min($discount, $this->maximum_discount_amount);
        }

        return round($discount, 2);
    }
}