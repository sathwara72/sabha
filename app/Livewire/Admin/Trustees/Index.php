<?php

namespace App\Livewire\Admin\Trustees;

use App\Models\Trustee;
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
        admin_authorize('trustees', 'can_delete');

        $name = $this->deletingName;
        Trustee::findOrFail($this->deletingId)->delete();
        $this->successMsg = "\"{$name}\" removed from trustees";
        $this->cancelDelete();
    }

    public function render()
    {
        $query = Trustee::with(['user.business'])->orderBy('sort_order');

        if ($this->search !== '') {
            $search = $this->search;
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $trustees = $query->paginate(10);

        return view('livewire.admin.trustees.index', [
            'trustees' => $trustees,
            'totalCount' => Trustee::count(),
        ]);
    }
}
