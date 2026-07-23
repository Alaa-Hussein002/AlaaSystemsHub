<?php
// database/seeders/PersonalProfileSeeder.php

namespace Database\Seeders;

use App\Models\PersonalProfile;
use Illuminate\Database\Seeder;

class PersonalProfileSeeder extends Seeder
{
    public function run(): void
    {
        PersonalProfile::updateOrCreate(
            ['id' => 1],
            [
                'full_name' => ['ar' => 'علاء حسين', 'en' => 'Alaa Hussein'],
                // 'title' => ['ar' => 'مطور ويب متكامل', 'en' => 'Full Stack Developer'],
                'available_for_hire' => true,
                'is_published' => true,
                'contact' => [
                    'email' => 'ala.hussein002@gmail.com',
                    'phone' => '+966509651996'
                ],
            ]
        );
    }
}