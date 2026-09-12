<?php

namespace App\Livewire\Admin\MemberTitles;

use App\Models\MemberTitle;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $deletingId = null;

    public string $deletingName = '';

    public string $successMsg = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openDelete(int $id, string $name): void
    {
        $this->deletingId = $id;
        $this->deletingName = $name;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
        $this->deletingName = '';
    }

    public function confirmDelete(): void
    {
        admin_authorize('member-titles', 'can_delete');

        $name = $this->deletingName;
        MemberTitle::findOrFail($this->deletingId)->delete();
        $this->successMsg = "\"{$name}\" member title deleted";
        $this->cancelDelete();
    }

    public function render()
    {
        $query = MemberTitle::withCount('users')->orderBy('sort_order');

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $titles = $query->paginate(9);

        return view('livewire.admin.member-titles.index', [
            'titles' => $titles,
            'totalCount' => MemberTitle::count(),
        ]);
    }
}
