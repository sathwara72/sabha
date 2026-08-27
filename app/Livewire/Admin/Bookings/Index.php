<?php

namespace App\Livewire\Admin\Bookings;

use App\Models\EventRegistration;
use App\Services\EventTicketApprover;
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
        admin_authorize('bookings', 'can_approve');

        $registration = EventRegistration::with(['user', 'event'])->findOrFail($id);
        app(EventTicketApprover::class)->approve($registration);
    }

    public function reject(int $id, string $reason): void
    {
        admin_authorize('bookings', 'can_approve');

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
        admin_authorize('bookings', 'can_edit');

        $registration = EventRegistration::findOrFail($id);
        $registration->update(['is_attended' => ! $registration->is_attended]);
    }

    public function processCheckIn(string $ticketNo): void
    {
        admin_authorize('bookings', 'can_edit');

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
