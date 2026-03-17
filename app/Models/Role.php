<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Role extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'roles';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_system',
        'permissions',   // array of { module, actions[] }
    ];

    protected $casts = [
        'is_system'   => 'boolean',
        'permissions' => 'array',
    ];

    // ====================================
    // العلاقات
    // ====================================

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    // ====================================
    // Helpers
    // ====================================

    public function hasPermission(string $module, string $action): bool
    {
        $permissions = collect($this->permissions ?? []);
        $modulePerms = $permissions->firstWhere('module', $module);

        return $modulePerms && in_array($action, $modulePerms['actions'] ?? []);
    }

    /**
     * الحصول على جميع الأقسام (modules) المتاحة في النظام
     */
    public static function availableModules(): array
    {
        return [
            'dashboard'  => ['view', 'export'],
            'profile'    => ['view', 'update'],
            'projects'   => ['view', 'create', 'update', 'delete'],
            'skills'     => ['view', 'create', 'update', 'delete'],
            'products'   => ['view', 'create', 'update', 'delete', 'manage_pricing'],
            'orders'     => ['view', 'update_status', 'cancel', 'refund'],
            'payments'   => ['view', 'confirm', 'reject'],
            'invoices'   => ['view', 'create', 'download'],
            'customers'  => ['view', 'create', 'update', 'delete', 'send_offers'],
            'coupons'    => ['view', 'create', 'update', 'delete'],
            'games'      => ['view', 'create', 'update', 'delete'],
            'analytics'  => ['view', 'export'],
            'messages'   => ['view', 'reply', 'delete'],
            'settings'   => ['view', 'update'],
            'users'      => ['view', 'create', 'update', 'delete', 'assign_roles'],
            'media'      => ['view', 'upload', 'delete'],
            'shipping'   => ['view', 'create', 'update', 'delete'],
        ];
    }
}