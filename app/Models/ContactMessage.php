<?php
// app/Models/ContactMessage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'category',
        'priority',
        'status',
        'reply',
        'replied_at',
        'replied_by',
        'attachments',
        'ip_address',
        'is_spam',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_spam' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function repliedByUser()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    public function scopeNotSpam($query)
    {
        return $query->where('is_spam', false);
    }
}