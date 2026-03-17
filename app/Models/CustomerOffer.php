<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class CustomerOffer extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'customer_offers';

    protected $fillable = [
        'title',
        'user_id',
        'product_ids',
        'discount_type',
        'discount_value',
        'message',
        'offer_code',
        'start_date',
        'end_date',
        'is_used',
        'used_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'product_ids'    => 'array',
        'message'        => 'array',
        'discount_value' => 'decimal:2',
        'is_used'        => 'boolean',
        'is_active'      => 'boolean',
        'used_at'        => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}