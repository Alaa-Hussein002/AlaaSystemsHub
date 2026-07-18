<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Role
        Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Site owner with full system access',
            'is_system' => true,
            'permissions' => [
                ['module' => 'dashboard', 'actions' => ['view', 'export']],
                ['module' => 'profile', 'actions' => ['view', 'update']],
                ['module' => 'projects', 'actions' => ['view', 'create', 'update', 'delete']],
                ['module' => 'skills', 'actions' => ['view', 'create', 'update', 'delete']],
                ['module' => 'experiences', 'actions' => ['view', 'create', 'update', 'delete']],
                ['module' => 'education', 'actions' => ['view', 'create', 'update', 'delete']],
                ['module' => 'certificates', 'actions' => ['view', 'create', 'update', 'delete']],
                ['module' => 'testimonials', 'actions' => ['view', 'create', 'update', 'delete']],
                ['module' => 'products', 'actions' => ['view', 'create', 'update', 'delete', 'manage_pricing']],
                ['module' => 'product_categories', 'actions' => ['view', 'create', 'update', 'delete']],
                ['module' => 'orders', 'actions' => ['view', 'update_status', 'cancel', 'refund']],
                ['module' => 'payments', 'actions' => ['view', 'confirm', 'reject']],
                ['module' => 'invoices', 'actions' => ['view', 'create', 'download']],
                ['module' => 'customers', 'actions' => ['view', 'create', 'update', 'delete', 'send_offers']],
                ['module' => 'coupons', 'actions' => ['view', 'create', 'update', 'delete']],
                ['module' => 'games', 'actions' => ['view', 'create', 'update', 'delete']],
                ['module' => 'analytics', 'actions' => ['view', 'export']],
                ['module' => 'messages', 'actions' => ['view', 'reply', 'delete']],
                ['module' => 'settings', 'actions' => ['view', 'update']],
                ['module' => 'users', 'actions' => ['view', 'create', 'update', 'delete']],
                ['module' => 'media', 'actions' => ['view', 'upload', 'delete']],
                ['module' => 'articles', 'actions' => ['view', 'create', 'update', 'delete']],
            ],
        ]);

        // Customer Role
        Role::create([
            'name' => 'customer',
            'display_name' => 'Customer',
            'description' => 'Regular customer with limited access',
            'is_system' => true,
            'permissions' => [
                ['module' => 'profile', 'actions' => ['view', 'update']],
                ['module' => 'orders', 'actions' => ['view', 'cancel']],
                ['module' => 'cart', 'actions' => ['view', 'add', 'update', 'remove']],
                ['module' => 'favorites', 'actions' => ['view', 'add', 'remove']],
            ],
        ]);

        $this->command->info('✅ Roles created: admin, customer');
    }
}