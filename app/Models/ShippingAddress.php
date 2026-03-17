<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'shipping_addresses';

    protected $fillable = [
        'user_id',
        'label',
        'full_name',
        'phone',
        'country',
        'city',
        'district',
        'street',
        'building',
        'apartment',
        'postal_code',
        'landmark',
        'coordinates',
        'is_default',
    ];

    protected $casts = [
        'coordinates' => 'array',
        'is_default'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}