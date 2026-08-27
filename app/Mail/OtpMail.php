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

    public string $otp;
    public string $userName;
    public string $type;

    public function __construct(string $otp, string $userName, string $type = 'registration')
    {
        $this->otp = $otp;
        $this->userName = $userName;
        $this->type = $type;
    }

    public function envelope(): Envelope
    {
        $subject = $this->type === 'reset_password'
            ? 'Your Sabha Password Reset Code'
            : 'Your Sabha Account Verification Code';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }
}
