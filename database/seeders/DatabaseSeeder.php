<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 بدء تهيئة قاعدة البيانات...');
        $this->command->newLine();

        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            SettingsSeeder::class,
            PersonalProfileSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('🎉 تمت تهيئة قاعدة البيانات بنجاح!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📊 الملخص:');
        $this->command->info('   ✅ 4 أدوار (Super Admin, Content Manager, Order Manager, Customer)');
        $this->command->info('   ✅ 1 حساب مدير أعلى');
        $this->command->info('   ✅ إعدادات الموقع الافتراضية');
        $this->command->info('   ✅ الملف الشخصي الأساسي');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}