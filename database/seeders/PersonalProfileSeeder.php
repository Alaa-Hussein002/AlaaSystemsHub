<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalProfile;

class PersonalProfileSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'full_name' => [
                'ar' => 'علاء حسين',
                'en' => 'Alaa Hussein',
            ],
            'title' => [
                'ar' => 'مهندس نظم معلومات | مطور Full-Stack',
                'en' => 'Information Systems Engineer | Full-Stack Developer',
            ],
            'bio' => [
                'ar' => 'مهندس نظم معلومات شغوف بتطوير حلول تقنية متكاملة بواجهات تفاعلية حديثة. خريج جامعة صنعاء 2026 بتخصص نظم المعلومات. أملك خبرة في تطوير تطبيقات الويب والموبايل باستخدام React و Laravel، مع شغف خاص بالتصميم ثلاثي الأبعاد وتجربة المستخدم.',
                'en' => 'A passionate Information Systems Engineer dedicated to building integrated technical solutions with modern interactive interfaces. Graduate of Sana\'a University 2026, specializing in Information Systems. Experienced in web and mobile app development using React & Laravel, with a special passion for 3D design and user experience.',
            ],
            'photo'       => null,
            'cover_image' => null,
            'cv_file'     => null,
            'nationality' => 'يمني',
            'location' => [
                'city'    => 'صنعاء',
                'country' => 'اليمن',
                'coordinates' => [
                    'lat' => 15.3694,
                    'lng' => 44.1910,
                ],
            ],
            'contact' => [
                'email'    => 'alaa@alaasystems.com',
                'phone'    => '+967771234567',
                'whatsapp' => '+967771234567',
            ],
            'social_links' => [
                ['platform' => 'github',   'url' => 'https://github.com/alaa',      'icon' => 'FaGithub'],
                ['platform' => 'linkedin', 'url' => 'https://linkedin.com/in/alaa', 'icon' => 'FaLinkedin'],
                ['platform' => 'twitter',  'url' => 'https://twitter.com/alaa',     'icon' => 'FaTwitter'],
                ['platform' => 'telegram', 'url' => 'https://t.me/alaa',           'icon' => 'FaTelegram'],
            ],
            'highlights' => [
                ['icon' => '🎓', 'label' => 'بكالوريوس نظم معلومات', 'value' => 'جامعة صنعاء 2026'],
                ['icon' => '💻', 'label' => 'مشاريع منجزة',         'value' => '15+'],
                ['icon' => '⚡', 'label' => 'سنوات الخبرة',         'value' => '3+'],
                ['icon' => '🏆', 'label' => 'عملاء راضون',          'value' => '20+'],
            ],
            'available_for_hire' => true,
            'is_published'       => true,
            'seo' => [
                'meta_title'       => 'علاء حسين - مهندس نظم معلومات | مطور Full-Stack',
                'meta_description' => 'الموقع الشخصي لعلاء حسين - مهندس نظم معلومات من جامعة صنعاء',
                'meta_keywords'    => ['developer', 'portfolio', 'full-stack', 'react', 'laravel'],
            ],
        ];

        $existing = PersonalProfile::first();

        if ($existing) {
            $existing->update($data);
        } else {
            PersonalProfile::create($data);
        }

        $this->command->info('✅ تم إنشاء الملف الشخصي بنجاح!');
    }
}