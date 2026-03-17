<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\ArcadeGame;
use App\Models\ContactMessage;
use App\Models\GameScore;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index()
    {
        // إحصائيات عامة
        $stats = [
            'total_orders'     => Order::count(),
            'pending_orders'   => Order::where('order_status', 'pending')->count(),
            'total_revenue'    => Order::where('payment_status', 'paid')->get()->sum(function ($o) {
                return $o->pricing['total'] ?? 0;
            }),
            'total_products'   => Product::where('is_active', true)->count(),
            'total_customers'  => User::where('type', 'customer')->count(),
            'total_games'      => ArcadeGame::where('is_active', true)->count(),
            'total_game_plays' => ArcadeGame::get()->sum(function ($g) {
                return $g->stats['play_count'] ?? 0;
            }),
            'unread_messages'  => ContactMessage::where('status', 'unread')->count(),
            'pending_payments' => Payment::where('status', 'pending_confirmation')->count(),
        ];

        // آخر 5 طلبات
        $recentOrders = Order::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'order_number'   => $order->order_number,
                    'customer'       => $order->customer_info['name'] ?? 'غير معروف',
                    'total'          => $order->pricing['total'] ?? 0,
                    'status'         => $order->order_status,
                    'payment_status' => $order->payment_status,
                    'created_at'     => $order->created_at?->toDateTimeString(),
                ];
            });

        // آخر 5 مدفوعات معلقة
        $pendingPayments = Payment::where('status', 'pending_confirmation')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($p) {
                return [
                    'payment_number' => $p->payment_number,
                    'amount'         => $p->amount,
                    'method'         => $p->payment_method,
                    'created_at'     => $p->created_at?->toDateTimeString(),
                ];
            });

        // إشعارات غير مقروءة
        $unreadNotifications = Notification::where('user_id', (string) auth()->user()->_id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // إحصائيات الزيارات (آخر 7 أيام)
        $visitStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = AnalyticsEvent::where('event_type', 'page_view')
                ->where('created_at', '>=', $date->startOfDay()->toDateTimeString())
                ->where('created_at', '<=', $date->endOfDay()->toDateTimeString())
                ->count();
            $visitStats[] = [
                'date'  => $date->format('Y-m-d'),
                'day'   => $date->format('D'),
                'count' => $count,
            ];
        }

        return $this->success([
            'stats'                => $stats,
            'recent_orders'        => $recentOrders,
            'pending_payments'     => $pendingPayments,
            'unread_notifications' => $unreadNotifications,
            'visit_stats'          => $visitStats,
        ], 'لوحة التحكم');
    }
}