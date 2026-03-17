<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ====================================
        // 👑 مدير النظام الأعلى
        // ====================================
        Role::updateOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'مدير النظام الأعلى',
                'description'  => 'صلاحية كاملة على جميع أقسام النظام',
                'is_system'    => true,
                'permissions'  => collect(Role::availableModules())->map(function ($actions, $module) {
                    return ['module' => $module, 'actions' => $actions];
                })->values()->toArray(),
            ]
        );

        // ====================================
        // 👨‍💼 مدير المحتوى
        // ====================================
        Role::updateOrCreate(
            ['name' => 'content_manager'],
            [
                'display_name' => 'مدير المحتوى',
                'description'  => 'إدارة المحتوى والمشاريع والمهارات',
                'is_system'    => false,
                'permissions'  => [
                    ['module' => 'dashboard',  'actions' => ['view']],
                    ['module' => 'profile',    'actions' => ['view', 'update']],
                    ['module' => 'projects',   'actions' => ['view', 'create', 'update']],
                    ['module' => 'skills',     'actions' => ['view', 'create', 'update']],
                    ['module' => 'products',   'actions' => ['view', 'create', 'update']],
                    ['module' => 'media',      'actions' => ['view', 'upload']],
                    ['module' => 'messages',   'actions' => ['view', 'reply']],
                ],
            ]
        );

        // ====================================
        // 📦 مدير الطلبات
        // ====================================
        Role::updateOrCreate(
            ['name' => 'order_manager'],
            [
                'display_name' => 'مدير الطلبات',
                'description'  => 'إدارة الطلبات والمدفوعات والفواتير',
                'is_system'    => false,
                'permissions'  => [
                    ['module' => 'dashboard',  'actions' => ['view']],
                    ['module' => 'orders',     'actions' => ['view', 'update_status']],
                    ['module' => 'payments',   'actions' => ['view', 'confirm', 'reject']],
                    ['module' => 'invoices',   'actions' => ['view', 'create', 'download']],
                    ['module' => 'customers',  'actions' => ['view']],
                    ['module' => 'shipping',   'actions' => ['view', 'create', 'update']],
                ],
            ]
        );

        // ====================================
        // 👤 عميل (زبون)
        // ====================================
        Role::updateOrCreate(
            ['name' => 'customer'],
            [
                'display_name' => 'عميل',
                'description'  => 'صلاحيات العميل الأساسية',
                'is_system'    => true,
                'permissions'  => [],
            ]
        );

        $this->command->info('✅ تم إنشاء الأدوار بنجاح!');
    }
}