<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'mongodb';

    public function up(): void
    {
        $db = DB::connection('mongodb')->getMongoDB();

        // 🔐 Users
        $db->selectCollection('users')->createIndex(['email' => 1], ['unique' => true]);
        $db->selectCollection('users')->createIndex(['type' => 1]);
        $db->selectCollection('users')->createIndex(['role_id' => 1]);
        $db->selectCollection('users')->createIndex(['status' => 1]);
        $db->selectCollection('users')->createIndex(['phone' => 1]);
        $db->selectCollection('users')->createIndex(['created_at' => 1]);

        // 🛡️ Roles
        $db->selectCollection('roles')->createIndex(['name' => 1], ['unique' => true]);

        // 👤 Personal Profiles
        $db->selectCollection('personal_profiles')->createIndex(['is_published' => 1]);

        // 📁 Projects
        $db->selectCollection('projects')->createIndex(['slug' => 1], ['unique' => true]);
        $db->selectCollection('projects')->createIndex(['status' => 1]);
        $db->selectCollection('projects')->createIndex(['is_published' => 1]);
        $db->selectCollection('projects')->createIndex(['is_featured' => 1]);
        $db->selectCollection('projects')->createIndex(['sort_order' => 1]);
        $db->selectCollection('projects')->createIndex(['category' => 1]);
        $db->selectCollection('projects')->createIndex(['created_at' => 1]);
        $db->selectCollection('projects')->createIndex(['tags' => 1]);

        // 🎯 Skills
        $db->selectCollection('skills')->createIndex(['is_published' => 1]);
        $db->selectCollection('skills')->createIndex(['sort_order' => 1]);

        // 💼 Experiences
        $db->selectCollection('experiences')->createIndex(['is_published' => 1]);
        $db->selectCollection('experiences')->createIndex(['sort_order' => 1]);
        $db->selectCollection('experiences')->createIndex(['is_current' => 1]);

        // 🎓 Educations
        $db->selectCollection('educations')->createIndex(['is_published' => 1]);
        $db->selectCollection('educations')->createIndex(['sort_order' => 1]);

        // 📜 Certificates
        $db->selectCollection('certificates')->createIndex(['is_published' => 1]);
        $db->selectCollection('certificates')->createIndex(['sort_order' => 1]);

        // 💬 Testimonials
        $db->selectCollection('testimonials')->createIndex(['project_id' => 1]);
        $db->selectCollection('testimonials')->createIndex(['is_published' => 1]);
        $db->selectCollection('testimonials')->createIndex(['is_featured' => 1]);
        $db->selectCollection('testimonials')->createIndex(['sort_order' => 1]);

        // 📂 Product Categories
        $db->selectCollection('product_categories')->createIndex(['slug' => 1], ['unique' => true]);
        $db->selectCollection('product_categories')->createIndex(['parent_id' => 1]);
        $db->selectCollection('product_categories')->createIndex(['is_active' => 1]);
        $db->selectCollection('product_categories')->createIndex(['sort_order' => 1]);
        $db->selectCollection('product_categories')->createIndex(['product_type' => 1]);

        // 🛍️ Products
        $db->selectCollection('products')->createIndex(['slug' => 1], ['unique' => true]);
        $db->selectCollection('products')->createIndex(['category_id' => 1]);
        $db->selectCollection('products')->createIndex(['product_type' => 1]);
        $db->selectCollection('products')->createIndex(['status' => 1]);
        $db->selectCollection('products')->createIndex(['is_active' => 1]);
        $db->selectCollection('products')->createIndex(['is_featured' => 1]);
        $db->selectCollection('products')->createIndex(['sort_order' => 1]);
        $db->selectCollection('products')->createIndex(['tags' => 1]);
        $db->selectCollection('products')->createIndex(['created_at' => 1]);
        $db->selectCollection('products')->createIndex(['status' => 1, 'is_active' => 1, 'product_type' => 1]);

        // 🛒 Carts
        $db->selectCollection('carts')->createIndex(['user_id' => 1]);
        $db->selectCollection('carts')->createIndex(['session_id' => 1]);
        $db->selectCollection('carts')->createIndex(['expires_at' => 1]);

        // 📦 Orders
        $db->selectCollection('orders')->createIndex(['order_number' => 1], ['unique' => true]);
        $db->selectCollection('orders')->createIndex(['user_id' => 1]);
        $db->selectCollection('orders')->createIndex(['order_status' => 1]);
        $db->selectCollection('orders')->createIndex(['payment_status' => 1]);
        $db->selectCollection('orders')->createIndex(['payment_method' => 1]);
        $db->selectCollection('orders')->createIndex(['created_at' => 1]);
        $db->selectCollection('orders')->createIndex(['order_status' => 1, 'payment_status' => 1]);
        $db->selectCollection('orders')->createIndex(['user_id' => 1, 'created_at' => 1]);

        // 💳 Payments
        $db->selectCollection('payments')->createIndex(['payment_number' => 1], ['unique' => true]);
        $db->selectCollection('payments')->createIndex(['order_id' => 1]);
        $db->selectCollection('payments')->createIndex(['user_id' => 1]);
        $db->selectCollection('payments')->createIndex(['status' => 1]);
        $db->selectCollection('payments')->createIndex(['payment_method' => 1]);
        $db->selectCollection('payments')->createIndex(['created_at' => 1]);
        $db->selectCollection('payments')->createIndex(['status' => 1, 'payment_method' => 1]);

        // 🧾 Invoices
        $db->selectCollection('invoices')->createIndex(['invoice_number' => 1], ['unique' => true]);
        $db->selectCollection('invoices')->createIndex(['order_id' => 1]);
        $db->selectCollection('invoices')->createIndex(['user_id' => 1]);
        $db->selectCollection('invoices')->createIndex(['payment_id' => 1]);
        $db->selectCollection('invoices')->createIndex(['status' => 1]);
        $db->selectCollection('invoices')->createIndex(['created_at' => 1]);

        // 🎫 Coupons
        $db->selectCollection('coupons')->createIndex(['code' => 1], ['unique' => true]);
        $db->selectCollection('coupons')->createIndex(['is_active' => 1]);
        $db->selectCollection('coupons')->createIndex(['start_date' => 1]);
        $db->selectCollection('coupons')->createIndex(['end_date' => 1]);

        // 🎁 Customer Offers
        $db->selectCollection('customer_offers')->createIndex(['offer_code' => 1], ['unique' => true]);
        $db->selectCollection('customer_offers')->createIndex(['user_id' => 1]);
        $db->selectCollection('customer_offers')->createIndex(['is_active' => 1]);
        $db->selectCollection('customer_offers')->createIndex(['is_used' => 1]);
        $db->selectCollection('customer_offers')->createIndex(['start_date' => 1]);
        $db->selectCollection('customer_offers')->createIndex(['end_date' => 1]);

        // 🚚 Shipping Methods
        $db->selectCollection('shipping_methods')->createIndex(['is_active' => 1]);
        $db->selectCollection('shipping_methods')->createIndex(['sort_order' => 1]);

        // 📍 Shipping Addresses
        $db->selectCollection('shipping_addresses')->createIndex(['user_id' => 1]);
        $db->selectCollection('shipping_addresses')->createIndex(['is_default' => 1]);

        // 📦 Shipments
        $db->selectCollection('shipments')->createIndex(['shipment_number' => 1], ['unique' => true]);
        $db->selectCollection('shipments')->createIndex(['order_id' => 1]);
        $db->selectCollection('shipments')->createIndex(['status' => 1]);
        $db->selectCollection('shipments')->createIndex(['tracking_number' => 1]);
        $db->selectCollection('shipments')->createIndex(['created_at' => 1]);

        // 🎮 Arcade Games
        $db->selectCollection('arcade_games')->createIndex(['slug' => 1], ['unique' => true]);
        $db->selectCollection('arcade_games')->createIndex(['is_active' => 1]);
        $db->selectCollection('arcade_games')->createIndex(['is_featured' => 1]);
        $db->selectCollection('arcade_games')->createIndex(['game_type' => 1]);
        $db->selectCollection('arcade_games')->createIndex(['sort_order' => 1]);

        // 🏆 Game Scores
        $db->selectCollection('game_scores')->createIndex(['game_id' => 1]);
        $db->selectCollection('game_scores')->createIndex(['user_id' => 1]);
        $db->selectCollection('game_scores')->createIndex(['score' => -1]);
        $db->selectCollection('game_scores')->createIndex(['created_at' => 1]);
        $db->selectCollection('game_scores')->createIndex(['game_id' => 1, 'score' => -1]);

        // 📊 Analytics Events
        $db->selectCollection('analytics_events')->createIndex(['event_type' => 1]);
        $db->selectCollection('analytics_events')->createIndex(['event_category' => 1]);
        $db->selectCollection('analytics_events')->createIndex(['target_type' => 1]);
        $db->selectCollection('analytics_events')->createIndex(['target_id' => 1]);
        $db->selectCollection('analytics_events')->createIndex(['created_at' => 1]);
        $db->selectCollection('analytics_events')->createIndex(['visitor.session_id' => 1]);
        $db->selectCollection('analytics_events')->createIndex(['event_type' => 1, 'created_at' => 1]);
        $db->selectCollection('analytics_events')->createIndex(['event_category' => 1, 'event_type' => 1]);

        // 📬 Contact Messages
        $db->selectCollection('contact_messages')->createIndex(['status' => 1]);
        $db->selectCollection('contact_messages')->createIndex(['category' => 1]);
        $db->selectCollection('contact_messages')->createIndex(['priority' => 1]);
        $db->selectCollection('contact_messages')->createIndex(['is_spam' => 1]);
        $db->selectCollection('contact_messages')->createIndex(['created_at' => 1]);

        // 📝 Activity Logs
        $db->selectCollection('activity_logs')->createIndex(['user_id' => 1]);
        $db->selectCollection('activity_logs')->createIndex(['action' => 1]);
        $db->selectCollection('activity_logs')->createIndex(['module' => 1]);
        $db->selectCollection('activity_logs')->createIndex(['target_type' => 1]);
        $db->selectCollection('activity_logs')->createIndex(['created_at' => 1]);
        $db->selectCollection('activity_logs')->createIndex(['module' => 1, 'action' => 1]);

        // ⚙️ Settings
        $db->selectCollection('settings')->createIndex(['key' => 1], ['unique' => true]);
        $db->selectCollection('settings')->createIndex(['group' => 1]);

        // 🔔 Notifications
        $db->selectCollection('notifications')->createIndex(['user_id' => 1]);
        $db->selectCollection('notifications')->createIndex(['type' => 1]);
        $db->selectCollection('notifications')->createIndex(['is_read' => 1]);
        $db->selectCollection('notifications')->createIndex(['created_at' => 1]);
        $db->selectCollection('notifications')->createIndex(['user_id' => 1, 'is_read' => 1]);

        // 🖼️ Media
        $db->selectCollection('media')->createIndex(['folder' => 1]);
        $db->selectCollection('media')->createIndex(['mime_type' => 1]);
        $db->selectCollection('media')->createIndex(['uploaded_by' => 1]);
        $db->selectCollection('media')->createIndex(['created_at' => 1]);

        // 🔑 Personal Access Tokens (Sanctum)
        $db->selectCollection('personal_access_tokens')->createIndex(['tokenable_id' => 1]);
        $db->selectCollection('personal_access_tokens')->createIndex(['token' => 1], ['unique' => true]);
    }

    public function down(): void
    {
        $collections = [
            'users', 'roles', 'personal_profiles', 'projects',
            'skills', 'experiences', 'educations', 'certificates',
            'testimonials', 'product_categories', 'products',
            'carts', 'orders', 'payments', 'invoices',
            'coupons', 'customer_offers',
            'shipping_methods', 'shipping_addresses', 'shipments',
            'arcade_games', 'game_scores',
            'analytics_events', 'contact_messages', 'activity_logs',
            'settings', 'notifications', 'media',
            'personal_access_tokens',
        ];

        $db = DB::connection('mongodb')->getMongoDB();
        foreach ($collections as $collection) {
            try {
                $db->dropCollection($collection);
            } catch (\Exception $e) {
                // تجاهل إذا المجموعة غير موجودة
            }
        }
    }
};