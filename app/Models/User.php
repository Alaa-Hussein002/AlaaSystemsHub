<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'type',
        'role_id',
        'status',
        'profile',
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
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'wallet_balance' => 'decimal:2',
        'profile' => 'array',
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }

    public function gameScores()
    {
        return $this->hasMany(GameScore::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function customerOffers()
    {
        return $this->hasMany(CustomerOffer::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->type === 'admin';
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
        if (!$this->role) return false;

        $permissions = collect($this->role->permissions ?? []);
        $modulePerms = $permissions->firstWhere('module', $module);

        return $modulePerms && in_array($action, $modulePerms['actions'] ?? []);
    }
}