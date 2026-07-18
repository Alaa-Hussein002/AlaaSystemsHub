<?php
// app/Models/PaymentMethod.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'details',
        'instructions',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'details' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}