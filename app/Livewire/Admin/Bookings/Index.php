<?php

namespace App\Livewire\Admin\Bookings;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\EventTicketApprover;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public ?int $eventFilter = null;

    public bool $isScannerOpen = false;

    public string $manualTicketNo = '';

    public ?bool $scanSuccess = null;

    public string $scanMessage = '';

    // Rejection Modal State
    public ?int $rejectingId = null;

    public string $rejectionReason = '';

    public string $successMsg = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedEventFilter(): void
    {
        $this->resetPage();
    }

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

        $this->successMsg = "Ticket for " . ($registration->attendeeName() ?? 'Member') . " approved successfully.";
    }

    public function openRejectModal(int $id): void
    {
        $this->rejectingId = $id;
        $this->rejectionReason = '';
    }

    public function cancelReject(): void
    {
        $this->rejectingId = null;
        $this->rejectionReason = '';
    }

    public function confirmReject(): void
    {
        admin_authorize('bookings', 'can_approve');

        if (trim($this->rejectionReason) === '') {
            $this->addError('rejectionReason', 'Please provide a reason for rejection.');
            return;
        }

        $registration = EventRegistration::findOrFail($this->rejectingId);
        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => trim($this->rejectionReason),
        ]);

        $this->successMsg = "Booking request rejected.";
        $this->cancelReject();
    }

    public function toggleAttendance(int $id): void
    {
        admin_authorize('bookings', 'can_edit');

        $registration = EventRegistration::findOrFail($id);
        $newStatus = ! $registration->is_attended;
        $registration->update(['is_attended' => $newStatus]);

        $this->successMsg = ($registration->attendeeName() ?? 'Attendee') . ($newStatus ? " marked as attended." : " marked as absent.");
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
            $this->scanMessage = 'Ticket #' . $ticketNo . ' not found.';
            return;
        }

        if (! in_array($registration->status, ['approved', 'confirmed'], true)) {
            $this->scanSuccess = false;
            $this->scanMessage = 'Ticket is not approved yet. Current status: ' . ucfirst($registration->status);
            return;
        }

        if ($registration->is_attended) {
            $this->scanSuccess = true;
            $this->scanMessage = 'Ticket #' . $ticketNo . ' (' . ($registration->attendeeName() ?? 'Attendee') . ') is already marked as attended.';
            return;
        }

        $registration->update(['is_attended' => true]);

        $this->scanSuccess = true;
        $this->scanMessage = 'Attendance marked successfully for ' . ($registration->attendeeName() ?? 'attendee') . '!';
        $this->manualTicketNo = '';
    }

    public function submitManualCheckIn(): void
    {
        $this->processCheckIn($this->manualTicketNo);
    }

    public function render()
    {
        $baseQuery = EventRegistration::with(['user', 'event', 'purchasedBy']);

        // Overall counts
        $allRegistrations = (clone $baseQuery)->get();
        $totalCount = $allRegistrations->count();
        $pendingCount = $allRegistrations->where('status', 'pending')->count();
        $approvedCount = $allRegistrations->whereIn('status', ['approved', 'confirmed'])->count();
        $attendedCount = $allRegistrations->where('is_attended', true)->count();
        $rejectedCount = $allRegistrations->where('status', 'rejected')->count();

        // Filter query
        $query = (clone $baseQuery)->latest();

        if ($this->search !== '') {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('ticket_number', 'like', "%{$s}%")
                    ->orWhere('guest_name', 'like', "%{$s}%")
                    ->orWhere('guest_email', 'like', "%{$s}%")
                    ->orWhere('guest_mobile', 'like', "%{$s}%")
                    ->orWhereHas('user', function ($uq) use ($s) {
                        $uq->where('name', 'like', "%{$s}%")
                            ->orWhere('email', 'like', "%{$s}%")
                            ->orWhere('phone', 'like', "%{$s}%");
                    })
                    ->orWhereHas('event', function ($eq) use ($s) {
                        $eq->where('title', 'like', "%{$s}%");
                    });
            });
        }

        if ($this->eventFilter) {
            $query->where('event_id', $this->eventFilter);
        }

        if ($this->statusFilter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($this->statusFilter === 'approved') {
            $query->whereIn('status', ['approved', 'confirmed']);
        } elseif ($this->statusFilter === 'attended') {
            $query->where('is_attended', true);
        } elseif ($this->statusFilter === 'rejected') {
            $query->where('status', 'rejected');
        }

        $registrations = $query->paginate(12);
        $eventsList = Event::orderBy('date', 'desc')->get(['id', 'title']);

        return view('livewire.admin.bookings.index', [
            'registrations' => $registrations,
            'eventsList' => $eventsList,
            'total' => $totalCount,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'attendedCount' => $attendedCount,
            'rejectedCount' => $rejectedCount,
        ]);
    }
}
