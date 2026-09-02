<?php

namespace App\Livewire\Profile;

use App\Models\OneToOneMeeting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class MeetingForm extends Component
{
    use WithFileUploads;

    public ?int $meetingId = null;

    public ?int $meetingWithMemberId = null;

    public string $meetingDate = '';

    public string $meetingLocation = '';

    public string $meetingDiscussion = '';

    public string $meetingComments = '';

    public $meetingImageFile;

    public string $meetingImagePreview = '';

    public function mount(?int $id = null): void
    {
        if (! $id) {
            return;
        }

        $meeting = OneToOneMeeting::where('user_id', Auth::id())->findOrFail($id);

        $this->meetingId = $meeting->id;
        $this->meetingWithMemberId = $meeting->with_member_id;
        $this->meetingDate = optional($meeting->meeting_at)->format('Y-m-d\TH:i');
        $this->meetingLocation = $meeting->location;
        $this->meetingDiscussion = $meeting->points_of_discussion ?? '';
        $this->meetingComments = $meeting->comments ?? '';
        $this->meetingImagePreview = media_url($meeting->image) ?? '';
    }

    public function saveMeeting()
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

        if ($this->meetingId) {
            OneToOneMeeting::where('user_id', Auth::id())->findOrFail($this->meetingId)->update($data);
        } else {
            $data['user_id'] = Auth::id();
            OneToOneMeeting::create($data);
        }

        return $this->redirect(route('profile', ['tab' => 'meetings']), navigate: false);
    }

    public function render()
    {
        $memberLabels = [];
        $memberValueMap = [];
        foreach (User::where('id', '!=', Auth::id())->whereNotIn('role', ['admin', 'sub_admin'])->where('is_blocked', false)->orderBy('name')->get(['id', 'name', 'phone']) as $member) {
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

        return view('livewire.profile.meeting-form', [
            'memberLabels' => $memberLabels,
            'memberValueMap' => $memberValueMap,
            'editingMemberName' => $editingMemberName,
        ]);
    }
}
