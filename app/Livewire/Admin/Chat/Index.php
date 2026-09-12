<?php

namespace App\Livewire\Admin\Chat;

use App\Models\Conversation;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $typeFilter = 'all';

    public ?int $deletingId = null;

    public string $deletingTitle = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setTypeFilter(string $type): void
    {
        $this->typeFilter = $type;
        $this->resetPage();
    }

    public function toggleArchive(int $id): void
    {
        admin_authorize('chat', 'can_edit');

        $conversation = Conversation::findOrFail($id);
        $conversation->update(['is_archived' => ! $conversation->is_archived]);
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
        admin_authorize('chat', 'can_delete');

        Conversation::findOrFail($this->deletingId)->delete();
        $this->cancelDelete();
    }

    public function render()
    {
        $query = Conversation::withCount([
                'participants as active_participant_count' => fn ($q) => $q->where('status', 'active'),
                'messages',
            ])
            ->with(['participants' => fn ($q) => $q->where('status', 'active')->with('user')])
            ->latest();

        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('participants.user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $conversations = $query->paginate(15);

        $conversations->getCollection()->transform(function (Conversation $c) {
            $c->display_title = $c->type === 'group'
                ? $c->title
                : $c->participants->pluck('user.name')->filter()->implode(' & ');

            return $c;
        });

        return view('livewire.admin.chat.index', [
            'conversations' => $conversations,
            'totalCount' => Conversation::count(),
            'groupCount' => Conversation::where('type', 'group')->count(),
            'directCount' => Conversation::where('type', 'direct')->count(),
        ]);
    }
}
