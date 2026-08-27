<?php

namespace App\Livewire\Profile;

use App\Models\OneToOneMeeting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Meetings extends Component
{
    use WithPagination;

    public ?int $deletingId = null;

    public string $successMsg = '';

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

        return view('livewire.profile.meetings', [
            'meetings' => $meetings,
        ]);
    }
}
