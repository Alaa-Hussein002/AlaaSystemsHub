<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', (string) $request->user()->_id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $unreadCount = Notification::where('user_id', (string) $request->user()->_id)
            ->where('is_read', false)
            ->count();

        return $this->success([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    public function markAsRead(string $id)
    {
        $notification = Notification::find($id);
        if (!$notification) return $this->notFound('الإشعار غير موجود');
        $notification->markAsRead();
        return $this->success(null, 'تم القراءة');
    }

    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', (string) $request->user()->_id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return $this->success(null, 'تم تعليم الكل كمقروء');
    }
}