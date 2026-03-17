<?php

namespace App\Http\Controllers\Api\Guest;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Notification;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|min:2|max:100',
            'email'   => 'required|email',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:10|max:2000',
            'phone'   => 'nullable|string|max:20',
        ], [
            'name.required'    => 'الاسم مطلوب',
            'email.required'   => 'البريد الإلكتروني مطلوب',
            'email.email'      => 'البريد الإلكتروني غير صالح',
            'subject.required' => 'الموضوع مطلوب',
            'message.required' => 'الرسالة مطلوبة',
            'message.min'      => 'الرسالة قصيرة جداً',
        ]);

        $contact = ContactMessage::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'subject'    => $request->subject,
            'message'    => $request->message,
            'category'   => 'general',
            'priority'   => 'normal',
            'status'     => 'unread',
            'ip_address' => $request->ip(),
            'is_spam'    => false,
        ]);

        // إشعار للمدير
        $admin = User::where('type', 'admin')->first();
        if ($admin) {
            Notification::create([
                'user_id'    => (string) $admin->_id,
                'type'       => 'new_message',
                'title'      => ['ar' => 'رسالة جديدة', 'en' => 'New Message'],
                'message'    => [
                    'ar' => "رسالة جديدة من {$request->name}: {$request->subject}",
                    'en' => "New message from {$request->name}: {$request->subject}",
                ],
                'icon'       => '📬',
                'action_url' => '/admin/messages/' . $contact->_id,
                'data'       => ['message_id' => (string) $contact->_id],
                'is_read'    => false,
            ]);
        }

        return $this->created(null, 'تم إرسال رسالتك بنجاح، سنتواصل معك قريباً');
    }
}