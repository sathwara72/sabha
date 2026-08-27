<?php

namespace App\Livewire\Profile;

use App\Models\OneToOneMeeting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Meetings extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $isFormModalOpen = false;

    public ?int $editingId = null;

    public ?int $meetingWithMemberId = null;

    public string $meetingDate = '';

    public string $meetingLocation = '';

    public string $meetingDiscussion = '';

    public string $meetingComments = '';

    public $meetingImageFile;

    public string $meetingImagePreview = '';

    public ?int $deletingId = null;

    public string $successMsg = '';

    public function openAdd(): void
    {
        $this->resetForm();
        $this->isFormModalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $meeting = OneToOneMeeting::where('user_id', Auth::id())->findOrFail($id);

        $this->editingId = $meeting->id;
        $this->meetingWithMemberId = $meeting->with_member_id;
        $this->meetingDate = optional($meeting->meeting_at)->format('Y-m-d\TH:i');
        $this->meetingLocation = $meeting->location;
        $this->meetingDiscussion = $meeting->points_of_discussion ?? '';
        $this->meetingComments = $meeting->comments ?? '';
        $this->meetingImagePreview = media_url($meeting->image) ?? '';
        $this->isFormModalOpen = true;
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->isFormModalOpen = false;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->meetingWithMemberId = null;
        $this->meetingDate = '';
        $this->meetingLocation = '';
        $this->meetingDiscussion = '';
        $this->meetingComments = '';
        $this->meetingImageFile = null;
        $this->meetingImagePreview = '';
        $this->resetErrorBag();
    }

    public function saveMeeting(): void
    {
        $validated = $this->validate([
            'meetingWithMemberId' => 'required|integer|exists:users,id',
            'meetingDate' => 'required|date',
            'meetingLocation' => 'required|string|max:255',
            'meetingDiscussion' => 'nullable|string',
            'meetingComments' => 'nullable|string',
            'meetingImageFile' => 'nullable|image|max:5120',
        ], [], [
            'meetingWithMemberId' => 'member',
        ]);

        if ((int) $this->meetingWithMemberId === Auth::id()) {
            $this->addError('meetingWithMemberId', 'You cannot log a meeting with yourself.');

            return;
        }

        $data = [
            'with_member_id' => $this->meetingWithMemberId,
            'meeting_at' => $this->meetingDate,
            'location' => $validated['meetingLocation'],
            'points_of_discussion' => $this->meetingDiscussion ?: null,
            'comments' => $this->meetingComments ?: null,
        ];

        if ($this->meetingImageFile) {
            $fileName = 'meeting_' . Auth::id() . '_' . time() . '.' . $this->meetingImageFile->getClientOriginalExtension();
            $this->meetingImageFile->storeAs('meeting-photos', $fileName, 'public');
            $data['image'] = '/storage/meeting-photos/' . $fileName;
        }

        if ($this->editingId) {
            OneToOneMeeting::where('user_id', Auth::id())->findOrFail($this->editingId)->update($data);
            $this->successMsg = __('site.profile.meetings.success_updated');
        } else {
            $data['user_id'] = Auth::id();
            OneToOneMeeting::create($data);
            $this->successMsg = __('site.profile.meetings.success_logged');
        }

        $this->resetForm();
        $this->isFormModalOpen = false;
    }

    public function openDelete(int $id): void
    {
        $exists = OneToOneMeeting::where('user_id', Auth::id())->where('id', $id)->exists();
        if ($exists) {
            $this->deletingId = $id;
        }
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function confirmDelete(): void
    {
        OneToOneMeeting::where('user_id', Auth::id())->where('id', $this->deletingId)->delete();
        $this->deletingId = null;
        $this->successMsg = __('site.profile.meetings.success_deleted');
    }

    public function render()
    {
        $meetings = OneToOneMeeting::with(['user', 'withMember'])
            ->where(function ($q) {
                $q->where('user_id', Auth::id())->orWhere('with_member_id', Auth::id());
            })
            ->latest('meeting_at')
            ->paginate(10);

        $memberLabels = [];
        $memberValueMap = [];
        foreach (User::where('id', '!=', Auth::id())->orderBy('name')->get(['id', 'name', 'phone']) as $member) {
            $label = $member->phone ? "{$member->name} ({$member->phone})" : $member->name;
            $memberLabels[] = $label;
            $memberValueMap[$label] = $member->id;
        }

        $editingMemberName = '';
        if ($this->meetingWithMemberId) {
            $selected = User::find($this->meetingWithMemberId);
            if ($selected) {
                $editingMemberName = $selected->phone ? "{$selected->name} ({$selected->phone})" : $selected->name;
            }
        }

        return view('livewire.profile.meetings', [
            'meetings' => $meetings,
            'memberLabels' => $memberLabels,
            'memberValueMap' => $memberValueMap,
            'editingMemberName' => $editingMemberName,
        ]);
    }
}
