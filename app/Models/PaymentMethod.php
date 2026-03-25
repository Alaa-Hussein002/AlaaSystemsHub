<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'payment_methods';

    protected $fillable = [
        'type',          // online | bank_transfer | wallet | cod
        'name',          // اسم الطريقة المعروض للعميل
        'details',       // embedded { bank_name, account_number... أو public_key, secret_key }
        'instructions',  // تعليمات تظهر للعميل
        'is_active',     // true | false
        'sort_order'     // لترتيب ظهورها في السلة
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
        // 'details'    => 'array',
    ];
}