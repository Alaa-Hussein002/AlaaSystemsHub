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
                'full_name' => ['ar' => 'علاء الدين', 'en' => 'Alaa Eddin'],
                // 'title' => ['ar' => 'مطور ويب متكامل', 'en' => 'Full Stack Developer'],
                'bio' => [
                    'ar' => 'مطور ويب محترف مع خبرة في بناء تطبيقات حديثة',
                    'en' => 'Professional web developer with experience in building modern applications'
                ],
                'available_for_hire' => true,
                'is_published' => true,
                'contact' => [
                    'email' => 'contact@alaasystems.com',
                    'phone' => '+967771234567'
                ],
                'social_links' => [
                    'github' => 'https://github.com/username',
                    'linkedin' => 'https://linkedin.com/in/username',
                ],
            ]
        );
    }
}