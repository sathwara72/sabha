<?php

namespace App\Livewire\Admin\Meetings;

use App\Models\OneToOneMeeting;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $deletingId = null;

    public ?int $viewingId = null;

    public ?OneToOneMeeting $viewingMeeting = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openView(int $id): void
    {
        $this->viewingId = $id;
        $this->viewingMeeting = OneToOneMeeting::with([
            'user.business',
            'user.memberTitle',
            'withMember.business',
            'withMember.memberTitle',
        ])->find($id);
    }

    public function closeView(): void
    {
        $this->viewingId = null;
        $this->viewingMeeting = null;
    }

    public function openDelete(int $id): void
    {
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function confirmDelete(): void
    {
        admin_authorize('meetings', 'can_delete');

        OneToOneMeeting::findOrFail($this->deletingId)->delete();
        $this->deletingId = null;
    }

    public function render()
    {
        $query = OneToOneMeeting::with(['user', 'withMember'])->latest('meeting_at');

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('withMember', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $meetings = $query->paginate(12);

        return view('livewire.admin.meetings.index', [
            'meetings' => $meetings,
            'totalCount' => OneToOneMeeting::count(),
        ]);
    }
}
