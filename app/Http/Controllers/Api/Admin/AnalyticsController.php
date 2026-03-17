<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Order;
use App\Models\Product;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    use ApiResponse;

    public function overview(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);

        // زيارات يومية
        $dailyVisits = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = AnalyticsEvent::where('event_type', 'page_view')
                ->where('created_at', '>=', $date->startOfDay()->toDateTimeString())
                ->where('created_at', '<=', $date->copy()->endOfDay()->toDateTimeString())
                ->count();
            $dailyVisits[] = ['date' => $date->format('Y-m-d'), 'count' => $count];
        }

        // المنتجات الأكثر مشاهدة
        $topProducts = Product::where('is_active', true)
            ->orderBy('stats.views_count', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'name'  => $p->name,
                'views' => $p->stats['views_count'] ?? 0,
                'sales' => $p->stats['sales_count'] ?? 0,
            ]);

        // مصادر الزيارات
        $sources = AnalyticsEvent::where('event_type', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function ($e) {
                $device = $e->device['type'] ?? 'unknown';
                return $device;
            })
            ->map(fn($group) => $group->count());

        // إيرادات شهرية
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Order::where('payment_status', 'paid')
                ->where('created_at', '>=', $month->startOfMonth()->toDateTimeString())
                ->where('created_at', '<=', $month->copy()->endOfMonth()->toDateTimeString())
                ->get()
                ->sum(fn($o) => $o->pricing['total'] ?? 0);
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