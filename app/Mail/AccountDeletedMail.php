<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AccountDeletedMail extends Mailable
{
    public function __construct(
        public readonly string $accountName,
        public readonly string $recipientEmail,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akun SkillPath AI Anda telah dihapus',
        );
    }

    public function content(): Content
    {
        $accountName = e($this->accountName);
        $recipientEmail = e($this->recipientEmail);
        $appUrl = e(
            (string) config(
                'app.url',
            ),
        );

        $html = <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto; padding: 24px; color: #171717;">
            <h1 style="font-size: 24px; margin: 0 0 16px;">Akun SkillPath AI telah dihapus</h1>
            <p style="margin: 0 0 16px; line-height: 1.6;">Halo {$accountName},</p>
            <p style="margin: 0 0 16px; line-height: 1.6;">Akun SkillPath AI yang menggunakan alamat email <strong>{$recipientEmail}</strong> telah dihapus oleh pengelola sistem.</p>
            <p style="margin: 0 0 16px; line-height: 1.6;">Setelah penghapusan ini, Anda tidak dapat lagi masuk menggunakan akun tersebut. Data yang terhubung dengan akun akan diproses sesuai struktur dan kebijakan penghapusan data SkillPath AI.</p>
            <p style="margin: 0 0 20px; line-height: 1.6;">Jika ingin menggunakan SkillPath AI kembali, Anda dapat membuat akun baru.</p>
            <a href="{$appUrl}" style="display: inline-block; border: 2px solid #171717; border-radius: 10px; padding: 10px 16px; background: #171717; color: #ffffff; font-weight: 700; text-decoration: none;">Buka SkillPath AI</a>
            <p style="margin: 24px 0 0; line-height: 1.6; color: #5f5f5f;">Email ini dikirim secara otomatis sebagai pemberitahuan penghapusan akun.</p>
        </div>
        HTML;

        return new Content(
            htmlString: $html,
        );
    }
}
