<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\PersonalProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Resend;

class ContactController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        // ✅ Log البداية
        Log::info('=== Contact Form Started ===', [
            'data' => $request->all(),
            'ip' => $request->ip(),
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20|regex:/^\+\d{1,3}\d{7,12}$/',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:2000',
            ], [
                'name.required' => 'الاسم مطلوب',
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email' => 'البريد الإلكتروني غير صحيح',
                'phone.required' => 'رقم الهاتف مطلوب',
                'phone.regex' => 'صيغة رقم الهاتف غير صحيحة',
                'subject.required' => 'الموضوع مطلوب',
                'message.required' => 'الرسالة مطلوبة',
            ]);

            Log::info('✅ Validation passed');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        // ✅ Check for spam مع معالجة الأخطاء
        $isSpam = false;
        try {
            Log::info('Checking spam...');
            $isSpam = $this->detectSpam($request);
            Log::info('✅ Spam check done', ['is_spam' => $isSpam]);
        } catch (\Exception $e) {
            Log::warning('⚠️ Spam detection failed', [
                'error' => $e->getMessage()
            ]);
        }

        // ✅ Create contact message
        try {
            Log::info('Creating message...');
            
            $message = ContactMessage::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'category' => $this->detectCategory($validated['subject']),
                'priority' => $this->detectPriority($validated['message']),
                'status' => 'unread',
                'ip_address' => $request->ip(),
                'is_spam' => $isSpam,
            ]);

            Log::info('✅ Message created', ['id' => $message->id]);

        } catch (\Exception $e) {
            Log::error('❌ Failed to create message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->error(
                'حدث خطأ أثناء حفظ رسالتك. يرجى المحاولة لاحقاً',
                500
            );
        }

        // ✅ Log activity
        try {
            Log::info('Logging activity...');
            ActivityLog::log(
                'contact_form_submitted',
                'messages',
                "رسالة جديدة من {$validated['name']}",
                'contact_message',
                $message->id
            );
            Log::info('✅ Activity logged');
        } catch (\Exception $e) {
            Log::error('❌ Activity log failed', [
                'error' => $e->getMessage()
            ]);
        }

        // ✅ Send email notification
        try {
            Log::info('Sending email notification...');
            $this->sendEmailNotification($message);
            Log::info('✅ Email notification sent');
        } catch (\Exception $e) {
            Log::error('❌ Email notification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // ✅ لا نُرجع خطأ - الرسالة محفوظة
        }

        Log::info('=== Contact Form Completed ===');

        return $this->success(
            null,
            'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً'
        );
    }

    /**
     * ✅ إرسال إيميل فقط
     */
    private function sendEmailNotification($message)
    {
        try {
            $adminProfile = PersonalProfile::first();
            $contactData = $this->parseContactData($adminProfile->contact);
            $adminEmail = $contactData['email'] ?? 'ala.hussein002@gmail.com';
    
            Log::info('📧 Sending via Resend to: ' . $adminEmail);
    
            $resend = Resend::client(env('RESEND_API_KEY'));
    
            $resend->emails->send([
                'from' => 'Alaa Systems <onboarding@resend.dev>',
                'to' => [$adminEmail],
                'subject' => "📨 رسالة جديدة - {$message->name}",
                'text' => $this->buildEmailBody($message),
            ]);
    
            Log::info('✅ Email sent successfully');
    
        } catch (\Exception $e) {
            Log::error('❌ Email failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ✅ بناء محتوى الإيميل
     */
    private function buildEmailBody($message)
    {
        return "📨 رسالة جديدة من نموذج التواصل\n\n" .
               "=====================================\n" .
               "👤 الاسم: {$message->name}\n" .
               "📧 البريد: {$message->email}\n" .
               "📱 الهاتف: {$message->phone}\n" .
               "📌 الموضوع: {$message->subject}\n" .
               "=====================================\n\n" .
               "💬 الرسالة:\n" .
               "{$message->message}\n\n" .
               "=====================================\n" .
               "⏰ التاريخ: {$message->created_at->format('d/m/Y H:i')}\n" .
               "🌐 عنوان IP: {$message->ip_address}\n" .
               "🔐 معرف الرسالة: #{$message->id}\n" .
               "=====================================\n";
    }

    /**
     * ✅ تحليل بيانات الاتصال
     */
    private function parseContactData($contactJson)
    {
        try {
            if (is_string($contactJson)) {
                return json_decode($contactJson, true) ?? [];
            }
            
            if (is_array($contactJson)) {
                return $contactJson;
            }
        } catch (\Exception $e) {
            Log::error('Error parsing contact data: ' . $e->getMessage());
        }
        
        return [];
    }

    /**
     * ✅ تصنيف الرسالة
     */
    private function detectCategory($subject)
    {
        $subject = strtolower($subject);
        
        if (str_contains($subject, 'مشروع')) return 'project_inquiry';
        if (str_contains($subject, 'دعم') || str_contains($subject, 'مساعدة')) return 'support';
        if (str_contains($subject, 'شراكة') || str_contains($subject, 'تعاون')) return 'partnership';
        
        return 'other';
    }

    /**
     * ✅ تحديد الأولوية
     */
    private function detectPriority($message)
    {
        $message = strtolower($message);
        
        if (str_contains($message, 'عاجل') || str_contains($message, 'ضروري')) return 'urgent';
        if (str_contains($message, 'مهم')) return 'high';
        
        return 'normal';
    }

    /**
     * ✅ كشف الرسائل المزعجة
     */
    private function detectSpam(Request $request): bool
    {
        try {
            $message = strtolower($request->message);
            
            // ✅ الكلمات المحظورة
            $spamKeywords = ['viagra', 'casino', 'lottery', 'winner', 'prize', 'click here', 'free money'];
            
            foreach ($spamKeywords as $keyword) {
                if (str_contains($message, $keyword)) {
                    return true;
                }
            }

            // ✅ فحص عدد الرسائل من نفس IP
            $recentCount = ContactMessage::where('ip_address', $request->ip())
                ->where('created_at', '>', now()->subMinutes(10))
                ->count();

            return $recentCount >= 3;

        } catch (\Exception $e) {
            Log::warning('Spam detection error: ' . $e->getMessage());
            return false; // في حالة الخطأ، لا نعتبرها spam
        }
    }
}