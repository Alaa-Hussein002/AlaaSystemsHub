<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ContactMessage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'contact_messages';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'category',      // project_inquiry | support | partnership | other
        'priority',      // low | normal | high | urgent
        'status',        // unread | read | replied | archived | spam
        'reply',
        'replied_at',
        'replied_by',
        'attachments',
        'ip_address',
        'is_spam',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_spam'     => 'boolean',
        'replied_at'  => 'datetime',
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