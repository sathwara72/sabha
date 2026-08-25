<?php

namespace App\Livewire\Admin\Bookings;

use App\Models\EventRegistration;
use Livewire\Component;

class Index extends Component
{
    public bool $isScannerOpen = false;

    public string $manualTicketNo = '';

    public ?bool $scanSuccess = null;

    public string $scanMessage = '';

    public function openScanner(): void
    {
        $this->isScannerOpen = true;
        $this->scanSuccess = null;
        $this->scanMessage = '';
        $this->manualTicketNo = '';
    }

    public function closeScanner(): void
    {
        $this->isScannerOpen = false;
        $this->scanSuccess = null;
        $this->scanMessage = '';
        $this->manualTicketNo = '';
    }

    public function resetScan(): void
    {
        $this->scanSuccess = null;
        $this->scanMessage = '';
    }

    public function approve(int $id): void
    {
        $registration = EventRegistration::with(['user', 'event'])->findOrFail($id);
        $event = $registration->event;

        $eventCode = $event->event_code;
        if (! $eventCode) {
            $eventCode = $this->generateEventCode($event->title);
            $event->update(['event_code' => $eventCode]);
        }

        $year = $event->date ? $event->date->format('Y') : date('Y');

        $ticketNo = $registration->ticket_number;
        if (! $ticketNo) {
            do {
                $ticketNo = $year . '-' . $eventCode . '-' . mt_rand(1000, 9999);
            } while (EventRegistration::where('ticket_number', $ticketNo)->exists());
        }

        $registration->update([
            'status' => 'approved',
            'ticket_number' => $ticketNo,
            'rejection_reason' => null,
        ]);
    }

    private function generateEventCode(string $title): string
    {
        $cleanTitle = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
        $words = array_filter(explode(' ', trim($cleanTitle)));
        $code = '';

        if (count($words) >= 2) {
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

        return substr($code, 0, 6);
    }

    public function reject(int $id, string $reason): void
    {
        if (trim($reason) === '') {
            return;
        }

        EventRegistration::findOrFail($id)->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    public function toggleAttendance(int $id): void
    {
        $registration = EventRegistration::findOrFail($id);
        $registration->update(['is_attended' => ! $registration->is_attended]);
    }

    public function processCheckIn(string $ticketNo): void
    {
        $ticketNo = trim($ticketNo);
        if ($ticketNo === '') {
            return;
        }

        $registration = EventRegistration::where('ticket_number', $ticketNo)->first();

        if (! $registration) {
            $this->scanSuccess = false;
            $this->scanMessage = 'Ticket not found.';

            return;
        }

        if (! in_array($registration->status, ['approved', 'confirmed'], true)) {
            $this->scanSuccess = false;
            $this->scanMessage = 'Ticket is not approved yet. Current status: ' . $registration->status;

            return;
        }

        if ($registration->is_attended) {
            $this->scanSuccess = true;
            $this->scanMessage = 'Ticket is already marked as attended.';

            return;
        }

        $registration->update(['is_attended' => true]);

        $this->scanSuccess = true;
        $this->scanMessage = 'Attendance marked successfully for ' . ($registration->user->name ?? 'attendee') . '!';
        $this->manualTicketNo = '';
    }

    public function submitManualCheckIn(): void
    {
        $this->processCheckIn($this->manualTicketNo);
    }

    public function render()
    {
        $registrations = EventRegistration::with(['user', 'event'])->latest()->get();

        return view('livewire.admin.bookings.index', [
            'registrations' => $registrations,
            'total' => $registrations->count(),
            'pendingCount' => $registrations->where('status', 'pending')->count(),
            'approvedCount' => $registrations->whereIn('status', ['approved', 'confirmed'])->count(),
            'rejectedCount' => $registrations->where('status', 'rejected')->count(),
        ]);
    }
}
