<?php
// app/Models/Invoice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'order_id',
        'user_id',
        'payment_id',
        'seller_info',
        'buyer_info',
        'items',
        'subtotal',
        'discount_total',
        'tax_total',
        'grand_total',
        'currency',
        'status',
        'issue_date',
        'due_date',
        'paid_date',
        'notes',
        'terms',
        'pdf_url',
    ];

    protected $casts = [
        'seller_info' => 'array',
        'buyer_info' => 'array',
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $last = static::where('invoice_number', 'like', "INV-{$year}-%")
            ->orderBy('created_at', 'desc')
            ->first();

        $newNum = $last ? intval(substr($last->invoice_number, -5)) + 1 : 1;
        return sprintf("INV-%s-%05d", $year, $newNum);
    }
}