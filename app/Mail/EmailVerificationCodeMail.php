<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EmailVerificationCodeMail extends Mailable
{
    public function __construct(
        public readonly string $code,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode verifikasi email SkillPath AI',
        );
    }

    public function content(): Content
    {
        $html = <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto; padding: 24px; color: #171717;">
            <h1 style="font-size: 24px; margin: 0 0 16px;">Verifikasi email SkillPath AI</h1>
            <p style="margin: 0 0 16px; line-height: 1.6;">Gunakan kode 6 digit berikut untuk memverifikasi email akun Anda.</p>
            <div style="display: inline-block; border: 2px solid #171717; border-radius: 10px; padding: 12px 18px; font-size: 28px; font-weight: 700; letter-spacing: 6px;">{$this->code}</div>
            <p style="margin: 20px 0 0; line-height: 1.6;">Kode ini berlaku selama 10 menit.</p>
            <p style="margin: 8px 0 0; line-height: 1.6;">Jika Anda tidak meminta verifikasi email, abaikan pesan ini.</p>
        </div>
        HTML;

        return new Content(
            htmlString: $html,
        );
    }
}
