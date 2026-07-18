<?php
// database/seeders/AdminUserSeeder.php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

    $adminRole = Role::where('name', 'admin')->first();

        User::create([
            'name' => 'Alaa Hussein',
            'email' => 'ala.hussein002@gmail.com',
            'password' => Hash::make('Alaa.002#'),
            'phone' => '+967737131058',
            'avatar' => null,
            'type' => 'admin',
            'role_id' => $adminRole->id, // We'll handle roles through the admin panel
            'status' => 'active',
            'profile' => null,
            'wallet_balance' => 0,
            'last_login_at' => null,
            'last_login_ip' => null,
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Admin user created successfully!');
        $this->command->info('📧 Email: ala.hussein002@gmail.com');
        $this->command->info('🔑 Password: Alaa.002#');
        $this->command->info('👤 Type: admin');
        $this->command->info('🎭 Role: Administrator');
    }
}