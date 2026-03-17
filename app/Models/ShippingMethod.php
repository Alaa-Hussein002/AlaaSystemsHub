<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'shipping_methods';

    protected $fillable = [
        'name',
        'description',
        'base_cost',
        'cost_per_kg',
        'free_shipping_threshold',
        'estimated_days_min',
        'estimated_days_max',
        'available_zones',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'name'                    => 'array',
        'base_cost'               => 'decimal:2',
        'cost_per_kg'             => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'estimated_days_min'      => 'integer',
        'estimated_days_max'      => 'integer',
        'available_zones'         => 'array',
        'is_active'               => 'boolean',
        'sort_order'              => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}