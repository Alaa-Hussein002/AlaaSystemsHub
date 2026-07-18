<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = ContactMessage::notSpam()->orderBy('created_at', 'desc');
        if ($request->has('status')) $query->where('status', $request->status);

        $messages = $query->get();
        return $this->success($messages);
    }

    public function show(string $id)
    {
        $message = ContactMessage::find($id);
        if (!$message) return $this->notFound('الرسالة غير موجودة');

        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }

        return $this->success($message);
    }

    public function reply(Request $request, string $id)
    {
        $request->validate([
            'reply' => 'required|string',
            'send_method' => 'nullable|in:email,whatsapp,both' // إضافة طريقة الإرسال
        ]);
    
        $message = ContactMessage::find($id);
        if (!$message) return $this->notFound('الرسالة غير موجودة');
    
        $message->update([
            'reply'      => $request->reply,
            'replied_at' => now(),
            'replied_by' => (string) $request->user()->_id,
            'status'     => 'replied',
        ]);
    
        ActivityLog::log('update', 'messages', "رد على رسالة من: {$message->name}", 'message', $id);
        
        // إرجاع بيانات المرسل للتوجيه
        return $this->success([
            'message' => $message,
            'redirect_data' => [
                'email' => $message->email,
                'phone' => $message->phone,
                'name' => $message->name,
                'send_method' => $request->send_method ?? 'none'
            ]
        ], 'تم حفظ الرد بنجاح');
    }

    public function destroy(string $id)
    {
        $message = ContactMessage::find($id);
        if (!$message) return $this->notFound('الرسالة غير موجودة');
        $message->delete();
        return $this->success(null, 'تم الحذف');
    }

    public function markAsSpam(string $id)
    {
        $message = ContactMessage::find($id);
        if (!$message) return $this->notFound('الرسالة غير موجودة');
        $message->update(['is_spam' => true, 'status' => 'spam']);
        return $this->success(null, 'تم التأشير كرسالة مزعجة');
    }
}