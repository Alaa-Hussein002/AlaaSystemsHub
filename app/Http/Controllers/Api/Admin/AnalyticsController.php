<?php
// app/Http/Controllers/Api/Admin/AnalyticsController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Order;
use App\Models\Product;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    use ApiResponse;

    public function overview(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);

        // ✅ زيارات يومية
        $dailyVisits = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = AnalyticsEvent::where('event_type', 'page_view')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->count();
            $dailyVisits[] = [
                'date' => $date->format('Y-m-d'), 
                'count' => $count
            ];
        }

        // ✅ المنتجات الأكثر مشاهدة
        $topProducts = Product::where('is_published', true)
            ->orderByRaw("CAST(JSON_EXTRACT(stats, '$.views_count') AS UNSIGNED) DESC")
            ->limit(10)
            ->get()
            ->map(function($p) {
                $stats = $p->stats ?? [];
                return [
                    'name'  => $p->name,
                    'views' => $stats['views_count'] ?? 0,
                    'sales' => $stats['sales_count'] ?? 0,
                ];
            });

        // ✅ مصادر الزيارات (by device type)
        $sources = AnalyticsEvent::where('event_type', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function ($event) {
                $device = $event->device['type'] ?? 'unknown';
                return $device;
            })
            ->map(fn($group) => $group->count());

        // ✅ إيرادات شهرية
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Order::where('payment_status', 'paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->get()
                ->sum(function($order) {
                    return $order->pricing['total'] ?? 0;
                });
                
            $monthlyRevenue[] = [
                'month'   => $month->format('Y-m'),
                'revenue' => $revenue,
            ];
        }

        return $this->success([
            'daily_visits'    => $dailyVisits,
            'top_products'    => $topProducts,
            'device_stats'    => $sources,
            'monthly_revenue' => $monthlyRevenue,
        ]);
    }
}