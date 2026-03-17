<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ActivityLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'activity_logs';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',  
        'module',
        'description',
        'target_type',
        'target_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * تسجيل نشاط بسهولة
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        $targetType = null,
        $targetId = null,
        $oldValues = null,
        $newValues = null
    ): self {
        /** @var \App\Models\User|null $user */
        $user = auth()->guard()->user();

        return static::create([
            'user_id'     => $user ? (string) $user->_id : null,
            'user_name'   => $user ? $user->name : 'System',
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'target_type' => $targetType,
            'target_id'   => $targetId ? (string) $targetId : null,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->header('User-Agent'),
        ]);
    }
}