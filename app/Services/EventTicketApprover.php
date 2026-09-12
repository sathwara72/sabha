<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Utils\QRCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Approves an event registration: assigns a unique ticket number (and event
 * code, if the event doesn't have one yet) and emails the attendee a QR
 * code of their ticket to present at check-in. Shared by every admin
 * surface that can approve a registration (Bookings, Event detail) so the
 * ticket-issuing logic — and the email — can't drift between them.
 */
class EventTicketApprover
{
    public function approve(EventRegistration $registration): EventRegistration
    {
        $registration->loadMissing(['user', 'event']);
        $event = $registration->event;

        $eventCode = $event->event_code ?: $this->generateEventCode($event);

        $year = $event->date ? $event->date->format('Y') : date('Y');

        $ticketNo = $registration->ticket_number ?: $this->generateUniqueTicketNumber($year, $eventCode);

        $registration->update([
            'status' => 'approved',
            'ticket_number' => $ticketNo,
            'rejection_reason' => null,
        ]);

        $this->sendApprovalEmail($registration, $event, $ticketNo);

        return $registration;
    }

    private function generateEventCode(Event $event): string
    {
        $cleanTitle = preg_replace('/[^a-zA-Z0-9\s]/', '', $event->title);
        $words = array_values(array_filter(explode(' ', trim($cleanTitle))));

        if (count($words) >= 2) {
            $code = '';
            foreach ($words as $word) {
                $code .= strtoupper(substr($word, 0, 1));
            }
        } else {
            $code = strtoupper(substr($cleanTitle, 0, 4));
        }

        $code = preg_replace('/[^A-Z0-9]/', '', $code);
        if (strlen($code) < 3) {
            $code .= mt_rand(100, 999);
        }

        $eventCode = substr($code, 0, 6);
        $event->update(['event_code' => $eventCode]);

        return $eventCode;
    }

    private function generateUniqueTicketNumber(string $year, string $eventCode): string
    {
        do {
            $ticketNo = $year . '-' . $eventCode . '-' . mt_rand(1000, 9999);
        } while (EventRegistration::where('ticket_number', $ticketNo)->exists());

        return $ticketNo;
    }

    private function sendApprovalEmail(EventRegistration $registration, Event $event, string $ticketNo): void
    {
        try {
            $userEmail = $registration->attendeeEmail();
            $userName = $registration->attendeeName();
            $eventName = $event->title;

            if (! $userEmail) {
                Log::error("Skipped approval email for registration #{$registration->id}: no attendee email on file.");

                return;
            }

            $qrCode = new QRCode($ticketNo, [
                's' => 'qrm', // Medium ECC
                'sf' => 8,    // Scale factor
                'p' => 2,     // Padding
            ]);
            $image = $qrCode->render_image();
            ob_start();
            imagepng($image);
            $imageData = ob_get_clean();
            imagedestroy($image);

            Mail::send([], [], function ($message) use ($userEmail, $userName, $eventName, $ticketNo, $imageData) {
                $qrCodeCid = $message->embedData($imageData, 'qrcode.png', 'image/png');

                $message->to($userEmail)
                    ->subject("Your Ticket for {$eventName} is Approved!")
                    ->html("
                        <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff;\">
                            <h2 style=\"color: #1e3a8a; margin-bottom: 20px;\">Hello {$userName},</h2>
                            <p style=\"font-size: 16px; color: #334155; line-height: 1.6;\">
                                We are excited to inform you that your seat reservation request for the event <strong>{$eventName}</strong> has been approved!
                            </p>
                            <div style=\"margin: 25px 0; padding: 15px; background-color: #f1f5f9; border-radius: 8px; text-align: center;\">
                                <span style=\"font-size: 14px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; display: block;\">Your Ticket Number</span>
                                <strong style=\"font-size: 22px; color: #0f172a; font-family: monospace;\">{$ticketNo}</strong>
                            </div>
                            <p style=\"font-size: 15px; color: #334155; margin-bottom: 10px;\">
                                Please present the QR code below at the entry gate to check in:
                            </p>
                            <div style=\"text-align: center; margin: 25px 0;\">
                                <img src=\"{$qrCodeCid}\" alt=\"Ticket QR Code\" style=\"border: 2px solid #cbd5e1; padding: 10px; border-radius: 12px; background-color: #fff; width: 220px; height: 220px;\" />
                            </div>
                            <hr style=\"border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;\" />
                            <p style=\"font-size: 15px; color: #334155;\">See you at the event!</p>
                            <p style=\"font-size: 15px; font-weight: bold; color: #0f172a; margin-top: 5px;\">
                                Best regards,<br/>
                                Sabha Team
                            </p>
                        </div>
                    ");
            });

            Log::info("SABHA Approved Ticket Email successfully dispatched to {$userEmail}. Ticket No: {$ticketNo}.");
        } catch (\Exception $e) {
            Log::error("Failed to send approval email to {$registration->attendeeEmail()}: " . $e->getMessage());
        }
    }
}
