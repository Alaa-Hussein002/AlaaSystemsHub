<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        // ====================================
        // إعدادات عامة
        // ====================================
        Setting::setValue('general', 'site_settings', [
            'site_name' => [
                'ar' => 'علاء سيستمز هب',
                'en' => 'Alaa Systems Hub',
            ],
            'site_description' => [
                'ar' => 'الموقع الشخصي والمتجر الرقمي لعلاء حسين - مهندس نظم معلومات',
                'en' => 'Personal portfolio & digital store of Alaa Hussein - IS Engineer',
            ],
            'logo'              => null,
            'logo_dark'         => null,
            'favicon'           => null,
            'primary_color'     => '#6366F1',
            'secondary_color'   => '#8B5CF6',
            'default_language'  => 'ar',
            'supported_languages' => ['ar', 'en'],
            'maintenance_mode'    => false,
            'maintenance_message' => 'الموقع تحت الصيانة، سنعود قريباً!',
        ]);

        // ====================================
        // إعدادات المتجر والدفع
        // ====================================
        Setting::setValue('store', 'payment_settings', [
            'accepted_currencies' => ['USD', 'YER', 'SAR'],
            'default_currency'    => 'USD',
            'tax_enabled'         => false,
            'tax_rate'            => 0,
            'payment_methods'     => [
                'bank_transfer' => [
                    'enabled' => true,
                    'label'   => ['ar' => 'تحويل بنكي', 'en' => 'Bank Transfer'],
                    'icon'    => '🏦',
                    'instructions' => [
                        'ar' => 'قم بالتحويل إلى أحد الحسابات التالية ثم أرفق إيصال التحويل',
                        'en' => 'Transfer to one of the accounts below and attach the receipt',
                    ],
                    'accounts' => [
                        [
                            'bank_name'      => 'بنك التضامن الإسلامي',
                            'account_number' => '1234567890',
                            'account_holder' => 'علاء حسين',
                            'iban'           => '',
                            'swift'          => '',
                        ],
                    ],
                ],
                'wallet' => [
                    'enabled' => true,
                    'label'   => ['ar' => 'محفظة إلكترونية', 'en' => 'E-Wallet'],
                    'icon'    => '📱',
                    'wallets' => [
                        [
                            'provider' => 'جوالي',
                            'number'   => '771234567',
                        ],
                        [
                            'provider' => 'فلوسك',
                            'number'   => '771234567',
                        ],
                    ],
                ],
                'cash_point' => [
                    'enabled' => true,
                    'label'   => ['ar' => 'نقطة حساب', 'en' => 'Cash Point'],
                    'icon'    => '💰',
                    'details' => [
                        'ar' => 'يمكنك الإيداع عبر أي نقطة حساب قريبة منك',
                        'en' => 'Deposit through any nearby cash point',
                    ],
                    'account_number' => '771234567',
                ],
            ],
        ]);

        // ====================================
        // إعدادات الإشعارات
        // ====================================
        Setting::setValue('notifications', 'notification_settings', [
            'email_notifications'   => true,
            'new_order_alert'       => true,
            'payment_received_alert'=> true,
            'new_message_alert'     => true,
            'low_stock_alert'       => true,
        ]);

        // ====================================
        // إعدادات SEO
        // ====================================
        Setting::setValue('seo', 'seo_settings', [
            'google_analytics_id'    => '',
            'google_search_console'  => '',
            'default_meta_title'     => 'Alaa Systems Hub | Portfolio & Digital Store',
            'default_meta_description' => 'الموقع الشخصي لعلاء حسين...',
            'og_image'               => null,
            'robots_txt'             => "User-agent: *\nAllow: /",
        ]);

        $this->command->info('✅ تم إنشاء الإعدادات الافتراضية بنجاح!');
    }
}