<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();

        User::updateOrCreate(
            ['email' => 'alaa@alaasystems.com'],
            [
                'name'     => 'علاء حسين',
                'email'    => 'alaa@alaasystems.com',
                'password' => bcrypt('Admin@2026!Secure'),
                'phone'    => '+967771234567',
                'avatar'   => null,
                'type'     => 'admin',
                'role_id'  => $superAdminRole ? (string) $superAdminRole->_id : null,
                'status'   => 'active',
                'profile'  => [
                    'gender'             => 'male',
                    'city'               => 'صنعاء',
                    'country'            => 'اليمن',
                    'preferred_language' => 'ar',
                    'timezone'           => 'Asia/Aden',
                ],
                'wallet_balance'    => 0,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ تم إنشاء حساب المدير الأعلى بنجاح!');
        $this->command->info('📧 البريد: alaa@alaasystems.com');
        $this->command->info('🔑 كلمة المرور: Admin@2026!Secure');
        $this->command->warn('⚠️  يرجى تغيير كلمة المرور فوراً بعد أول تسجيل دخول!');
    }
}