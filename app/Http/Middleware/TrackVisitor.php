<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AnalyticsEvent;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // تسجيل الزيارة بعد الاستجابة (لا يؤثر على السرعة)
        try {
            AnalyticsEvent::create([
                'event_type'     => 'page_view',
                'event_category' => 'general',
                'page_url'       => $request->fullUrl(),
                'visitor' => [
                    'user_id'    => $request->user() ? (string) $request->user()->_id : null,
                    'session_id' => session()->getId(),
                    'ip_hash'    => md5($request->ip()),
                ],
                'device' => [
                    'type'    => $this->detectDevice($request),
                    'browser' => $request->header('User-Agent'),
                ],
                'referrer' => [
                    'url' => $request->header('referer'),
                ],
            ]);
        } catch (\Exception $e) {
            // لا نوقف التطبيق بسبب خطأ في التحليلات
        }

        return $response;
    }

    private function detectDevice(Request $request): string
    {
        $agent = strtolower($request->header('User-Agent', ''));
        if (str_contains($agent, 'mobile')) return 'mobile';
        if (str_contains($agent, 'tablet')) return 'tablet';
        return 'desktop';
    }
}