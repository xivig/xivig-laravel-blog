<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorOtpMail extends Mailable {
    use Queueable, SerializesModels;

    public User $user;
    public string $otp;

    public function __construct(User $user, string $otp) {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Maison Gazette — Two-Step Verification Code',
        );
    }

    public function content(): Content {
        return new Content(
            view: 'emails.two-factor-otp',
            with: [
                'user' => $this->user,
                'otp'  => $this->otp,
            ],
        );
    }
}
