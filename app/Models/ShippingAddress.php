<?php
// app/Models/ShippingAddress.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;

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
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}