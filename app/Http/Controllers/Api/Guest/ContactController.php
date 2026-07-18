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

class ContactController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
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

        // Check for spam
        $isSpam = $this->detectSpam($request);

        // Create contact message
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

        // Log activity
        try {
            ActivityLog::log(
                'contact_form_submitted',
                'messages',
                "رسالة جديدة من {$validated['name']}",
                'contact_message',
                $message->id
            );
        } catch (\Exception $e) {
            Log::error('Activity log failed: ' . $e->getMessage());
        }

        // Send notifications immediately
        try {
            $this->sendNotifications($message);
        } catch (\Exception $e) {
            Log::error('Failed to send notifications: ' . $e->getMessage());
        }

        return $this->success(
            null,
            'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً'
        );
    }

    private function sendNotifications($message)
    {
        Log::info('=== Starting Notifications ===');
        Log::info('Message ID: ' . $message->id);
        
        $adminProfile = PersonalProfile::first();
        
        if (!$adminProfile) {
            Log::warning('❌ No admin profile found');
            return;
        }

        Log::info('Admin Profile found');

        $contactData = $this->parseContactData($adminProfile->contact);
        
        Log::info('Contact Data:', $contactData);

        // Send email to admin
        $adminEmail = $contactData['email'] ?? null;
        if ($adminEmail) {
            try {
                Log::info('📧 Attempting to send email to: ' . $adminEmail);
                
                $emailBody = $this->buildEmailBody($message);
                
                Mail::raw($emailBody, function ($mail) use ($adminEmail, $message) {
                    $mail->to($adminEmail)
                         ->subject("📨 رسالة جديدة من نموذج التواصل - {$message->name}");
                });
                
                Log::info('✅ Email sent successfully to: ' . $adminEmail);
            } catch (\Exception $e) {
                Log::error('❌ Email failed: ' . $e->getMessage());
                Log::error('Stack: ' . $e->getTraceAsString());
            }
        } else {
            Log::warning('⚠️ Admin email not found in contact data');
        }

        // Send WhatsApp notification
        $whatsappNumber = $contactData['whatsapp'] ?? null;
        if ($whatsappNumber) {
            try {
                Log::info('📱 Preparing WhatsApp notification for: ' . $whatsappNumber);
                $this->sendWhatsAppNotification($message, $whatsappNumber);
            } catch (\Exception $e) {
                Log::error('WhatsApp error: ' . $e->getMessage());
            }
        } else {
            Log::warning('⚠️ Admin WhatsApp number not found in contact data');
        }
    }

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

    private function sendWhatsAppNotification($message, $whatsappNumber)
    {
        try {
            if (config('services.twilio.sid') && config('services.twilio.token')) {
                Log::info('📱 Using Twilio for WhatsApp');
                $this->sendViaTwilio($message, $whatsappNumber);
            } else {
                Log::info('📱 No Twilio config - logging message');
                $this->logWhatsAppMessage($message, $whatsappNumber);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp notification error: ' . $e->getMessage());
        }
    }

    private function sendViaTwilio($message, $whatsappNumber)
    {
        try {
            $client = new \Twilio\Rest\Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            $whatsappText = "📨 رسالة جديدة من نموذج التواصل\n\n";
            $whatsappText .= "👤 الاسم: {$message->name}\n";
            $whatsappText .= "📧 البريد: {$message->email}\n";
            $whatsappText .= "📱 الهاتف: {$message->phone}\n";
            $whatsappText .= "📌 الموضوع: {$message->subject}\n\n";
            $whatsappText .= "💬 الرسالة:\n{$message->message}";

            $client->messages->create(
                "whatsapp:{$whatsappNumber}",
                [
                    "from" => "whatsapp:" . config('services.twilio.whatsapp_number'),
                    "body" => $whatsappText
                ]
            );

            Log::info("✅ WhatsApp message sent to: {$whatsappNumber}");
        } catch (\Exception $e) {
            Log::error('Twilio error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function logWhatsAppMessage($message, $whatsappNumber)
    {
        $whatsappText = "📨 رسالة جديدة من نموذج التواصل\n\n";
        $whatsappText .= "👤 الاسم: {$message->name}\n";
        $whatsappText .= "📧 البريد: {$message->email}\n";
        $whatsappText .= "📱 الهاتف: {$message->phone}\n";
        $whatsappText .= "📌 الموضوع: {$message->subject}\n\n";
        $whatsappText .= "💬 الرسالة:\n{$message->message}";

        Log::channel('whatsapp')->info("WhatsApp Message to {$whatsappNumber}:\n{$whatsappText}");
        Log::info("✅ WhatsApp message logged for: {$whatsappNumber}");
    }

    private function detectCategory($subject)
    {
        $subject = strtolower($subject);
        
        if (str_contains($subject, 'مشروع')) return 'project_inquiry';
        if (str_contains($subject, 'دعم') || str_contains($subject, 'مساعدة')) return 'support';
        if (str_contains($subject, 'شراكة') || str_contains($subject, 'تعاون')) return 'partnership';
        
        return 'other';
    }

    private function detectPriority($message)
    {
        $message = strtolower($message);
        
        if (str_contains($message, 'عاجل') || str_contains($message, 'ضروري')) return 'urgent';
        if (str_contains($message, 'مهم')) return 'high';
        
        return 'normal';
    }

    private function detectSpam(Request $request): bool
    {
        $message = strtolower($request->message);
        
        $spamKeywords = ['viagra', 'casino', 'lottery', 'winner', 'prize', 'click here', 'free money'];
        
        foreach ($spamKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        $recentCount = ContactMessage::where('ip_address', $request->ip())
            ->where('created_at', '>', now()->subMinutes(10))
            ->count();

        return $recentCount >= 3;
    }
}