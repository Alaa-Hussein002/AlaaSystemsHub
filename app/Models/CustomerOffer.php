<?php
// app/Models/CustomerOffer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOffer extends Model
{
    use HasFactory;

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
        'product_ids' => 'array',
        'message' => 'array',
        'discount_value' => 'decimal:2',
        'is_used' => 'boolean',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}