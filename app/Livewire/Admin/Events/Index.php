<?php

namespace App\Livewire\Admin\Events;

use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?int $deletingId = null;

    public string $deletingTitle = '';

    public function togglePopup(int $id): void
    {
        admin_authorize('events', 'can_edit');

        DB::transaction(function () use ($id) {
            $event = Event::findOrFail($id);
            $makeActive = ! $event->is_popup;

            Event::where('id', '!=', $id)->update(['is_popup' => false]);
            $event->update(['is_popup' => $makeActive]);
        });
    }

    public function openDelete(int $id, string $title): void
    {
        $this->deletingId = $id;
        $this->deletingTitle = $title;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
        $this->deletingTitle = '';
    }

    public function confirmDelete(): void
    {
        admin_authorize('events', 'can_delete');

        Event::findOrFail($this->deletingId)->delete();
        $this->cancelDelete();
    }

    public function render()
    {
        return view('livewire.admin.events.index', [
            'events' => Event::orderByDesc('date')->paginate(6),
        ]);
    }
}
