<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;

    public string $type;

    public ?string $reason;

    /**
     * $type is one of: step1_approved, step1_rejected, step2_submitted,
     * payment_approved, payment_rejected.
     */
    public function __construct(User $user, string $type, ?string $reason = null)
    {
        $this->userName = $user->name;
        $this->type = $type;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'step1_approved' => 'Your Sabha Registration — Next Step: Upload Documents',
            'step1_rejected' => 'Your Sabha Registration Application',
            'step2_submitted' => 'We Received Your Documents — Sabha',
            'payment_approved' => 'Welcome to Sabha — Your Account is Active',
            'payment_rejected' => 'Action Needed on Your Sabha Registration',
        ];

        return new Envelope(
            subject: $subjects[$this->type] ?? 'Your Sabha Registration Update',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-status',
        );
    }
}
