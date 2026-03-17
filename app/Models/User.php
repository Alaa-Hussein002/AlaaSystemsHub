<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as MongoUser;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends MongoUser
{
    use HasApiTokens, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'type',          // admin | customer
        'role_id',
        'status',        // active | inactive | banned
        'profile',       // embedded document
        'wallet_balance',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'password'          => 'hashed',
        'wallet_balance'    => 'decimal:2',
        'profile'           => 'array',
    ];

    // ====================================
    // العلاقات (Relationships)
    // ====================================

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id');
    }

    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class, 'user_id');
    }

    public function gameScores()
    {
        return $this->hasMany(GameScore::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function customerOffers()
    {
        return $this->hasMany(CustomerOffer::class, 'user_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    // ====================================
    // Helpers
    // ====================================

    public function isAdmin(): bool
    {
        return $this->type === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->type === 'admin' && $this->role?->name === 'super_admin';
    }

    public function isCustomer(): bool
    {
        return $this->type === 'customer';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasPermission(string $module, string $action): bool
    {
        if ($this->isSuperAdmin()) return true;

        $role = $this->role;
        if (!$role) return false;

        $permissions = collect($role->permissions ?? []);
        $modulePerms = $permissions->firstWhere('module', $module);

        return $modulePerms && in_array($action, $modulePerms['actions'] ?? []);
    }
}