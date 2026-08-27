<?php

namespace App\Livewire\Admin\Events;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\GalleryImage;
use App\Services\EventTicketApprover;
use App\Services\GalleryMediaUploader;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Show extends Component
{
    use WithFileUploads;
    use WithPagination;

    public int $eventId;

    public string $activeTab = 'registrations';

    public string $memberSearch = '';

    public string $memberFilter = 'all';

    public ?int $actionLoadingId = null;

    public ?int $rejectModalId = null;

    public string $rejectReason = '';

    public bool $isUploadModalOpen = false;

    public array $mediaFiles = [];

    public string $uploadError = '';

    public ?int $deleteMediaId = null;

    public function mount(int $id): void
    {
        $this->eventId = $id;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function updatedMemberSearch(): void
    {
        $this->resetPage('regPage');
    }

    public function setMemberFilter(string $filter): void
    {
        $this->memberFilter = $filter;
        $this->resetPage('regPage');
    }

    public function approve(int $id): void
    {
        admin_authorize('events', 'can_approve');

        $registration = EventRegistration::with(['user', 'event'])->findOrFail($id);
        app(EventTicketApprover::class)->approve($registration);
    }

    public function openReject(int $id): void
    {
        $this->rejectModalId = $id;
        $this->rejectReason = '';
    }

    public function cancelReject(): void
    {
        $this->rejectModalId = null;
        $this->rejectReason = '';
    }

    public function confirmReject(): void
    {
        admin_authorize('events', 'can_approve');

        if (! $this->rejectModalId || trim($this->rejectReason) === '') {
            return;
        }

        \App\Models\EventRegistration::findOrFail($this->rejectModalId)->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejectReason,
        ]);

        $this->cancelReject();
    }

    public function toggleAttendance(int $id): void
    {
        admin_authorize('events', 'can_edit');

        $registration = \App\Models\EventRegistration::findOrFail($id);
        $registration->update(['is_attended' => ! $registration->is_attended]);
    }

    public function openUploadModal(): void
    {
        $this->mediaFiles = [];
        $this->uploadError = '';
        $this->isUploadModalOpen = true;
    }

    public function closeUploadModal(): void
    {
        $this->isUploadModalOpen = false;
        $this->mediaFiles = [];
        $this->uploadError = '';
    }

    public function uploadMedia(GalleryMediaUploader $uploader): void
    {
        admin_authorize('events', 'can_add');

        $this->uploadError = '';

        if (empty($this->mediaFiles)) {
            $this->uploadError = 'Please select at least one image, video, or ZIP archive.';

            return;
        }

        $created = $uploader->upload($this->mediaFiles, $this->eventId);

        if (empty($created)) {
            $this->uploadError = 'No valid media files were uploaded.';

            return;
        }

        $this->closeUploadModal();
        $this->resetPage('galPage');
    }

    public function openDeleteMedia(int $id): void
    {
        $this->deleteMediaId = $id;
    }

    public function cancelDeleteMedia(): void
    {
        $this->deleteMediaId = null;
    }

    public function confirmDeleteMedia(): void
    {
        admin_authorize('events', 'can_delete');

        $image = GalleryImage::find($this->deleteMediaId);

        if ($image) {
            $path = public_path($image->image_path);
            if (file_exists($path)) {
                @unlink($path);
            }
            $image->delete();
        }

        $this->deleteMediaId = null;
    }

    public function render()
    {
        $event = Event::find($this->eventId);

        $registrationsQuery = \App\Models\EventRegistration::with(['user', 'purchasedBy'])
            ->where('event_id', $this->eventId)
            ->latest();

        if ($this->memberFilter !== 'all') {
            $registrationsQuery->where('status', $this->memberFilter);
        }

        if ($this->memberSearch !== '') {
            $search = $this->memberSearch;
            $registrationsQuery->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('guest_name', 'like', "%{$search}%")
                    ->orWhere('guest_email', 'like', "%{$search}%")
                    ->orWhere('guest_mobile', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $registrations = $registrationsQuery->paginate(10, pageName: 'regPage');
        $registrationsTotal = \App\Models\EventRegistration::where('event_id', $this->eventId)->count();

        $gallery = GalleryImage::where('event_id', $this->eventId)->latest()->paginate(8, pageName: 'galPage');
        $galleryTotal = GalleryImage::where('event_id', $this->eventId)->count();

        return view('livewire.admin.events.show', [
            'event' => $event,
            'registrations' => $registrations,
            'registrationsTotal' => $registrationsTotal,
            'gallery' => $gallery,
            'galleryTotal' => $galleryTotal,
        ]);
    }
}
