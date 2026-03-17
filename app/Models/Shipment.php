<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Shipment extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'shipments';

    protected $fillable = [
        'shipment_number',
        'order_id',
        'shipping_method_id',
        'shipping_address_id',
        'carrier',
        'tracking_number',
        'tracking_url',
        'weight',
        'dimensions',
        'shipping_cost',
        'status',
        'status_history',
        'estimated_delivery',
        'delivered_at',
        'shipped_at',
    ];

    protected $casts = [
        'dimensions'     => 'array',
        'status_history' => 'array',
        'shipping_cost'  => 'decimal:2',
        'weight'         => 'decimal:2',
        'delivered_at'   => 'datetime',
        'shipped_at'     => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function addTracking(string $status, string $location, string $note = '')
    {
        $history = $this->status_history ?? [];
        $history[] = [
            'status'    => $status,
            'location'  => $location,
            'note'      => $note,
            'timestamp' => now()->toISOString(),
        ];

        $this->update([
            'status'         => $status,
            'status_history' => $history,
        ]);
    }
}