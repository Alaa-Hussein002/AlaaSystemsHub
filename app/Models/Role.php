<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_system',
        'permissions',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'permissions' => 'array',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Helpers
    public function hasPermission(string $module, string $action): bool
    {
        $permissions = collect($this->permissions ?? []);
        $modulePerms = $permissions->firstWhere('module', $module);

        return $modulePerms && in_array($action, $modulePerms['actions'] ?? []);
    }
}