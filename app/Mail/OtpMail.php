<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $userName;
    public $expiryMinutes;

    public function __construct(string $otp, string $userName = 'المستخدم', int $expiryMinutes = 5)
    {
        $this->otp = $otp;
        $this->userName = $userName;
        $this->expiryMinutes = $expiryMinutes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رمز إعادة تعيين كلمة المرور - Alaa Hussein',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}