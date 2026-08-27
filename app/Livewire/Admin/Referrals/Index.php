<?php

namespace App\Livewire\Admin\Referrals;

use App\Models\BusinessReferral;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public ?int $selectedId = null;

    public ?int $deletingId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function view(int $id): void
    {
        $this->selectedId = $id;
    }

    public function closeView(): void
    {
        $this->selectedId = null;
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
        admin_authorize('referrals', 'can_delete');

        BusinessReferral::findOrFail($this->deletingId)->delete();
        $this->deletingId = null;

        if ($this->selectedId === $this->deletingId) {
            $this->selectedId = null;
        }
    }

    public function render()
    {
        $query = BusinessReferral::with(['giver', 'receiver'])->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('contact_name', 'like', "%{$search}%")
                    ->orWhereHas('giver', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('receiver', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $referrals = $query->paginate(10);

        $counts = [
            'all' => BusinessReferral::count(),
            'pending' => BusinessReferral::where('status', 'pending')->count(),
            'in_progress' => BusinessReferral::where('status', 'in_progress')->count(),
            'closed' => BusinessReferral::where('status', 'closed')->count(),
            'rejected' => BusinessReferral::where('status', 'rejected')->count(),
        ];

        return view('livewire.admin.referrals.index', [
            'referrals' => $referrals,
            'counts' => $counts,
            'selectedReferral' => $this->selectedId ? BusinessReferral::with(['giver', 'receiver'])->find($this->selectedId) : null,
        ]);
    }
}
