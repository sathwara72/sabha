<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\Business;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: false)]
    public string $search = '';

    public string $statusFilter = 'all';

    public ?int $rejectingId = null;

    public string $rejectingName = '';

    public string $rejectReason = '';

    public ?int $deletingId = null;

    public string $deletingName = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        Business::findOrFail($id)->update([
            'status' => 'approved',
            'is_verified' => true,
            'rejection_reason' => null,
        ]);
    }

    public function openReject(int $id, string $name): void
    {
        $this->rejectingId = $id;
        $this->rejectingName = $name;
        $this->rejectReason = '';
        $this->resetErrorBag('rejectReason');
    }

    public function cancelReject(): void
    {
        $this->rejectingId = null;
        $this->rejectingName = '';
        $this->rejectReason = '';
    }

    public function confirmReject(): void
    {
        $this->validate(['rejectReason' => 'required|string']);

        Business::findOrFail($this->rejectingId)->update([
            'status' => 'rejected',
            'is_verified' => false,
            'rejection_reason' => $this->rejectReason,
        ]);

        $this->cancelReject();
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
        Business::findOrFail($this->deletingId)->delete();
        $this->cancelDelete();
    }

    public function render()
    {
        $query = Business::with(['user', 'businessCategory'])->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $businesses = $query->paginate(9);

        $counts = [
            'all' => Business::count(),
            'pending' => Business::where('status', 'pending')->count(),
            'approved' => Business::where('status', 'approved')->count(),
            'rejected' => Business::where('status', 'rejected')->count(),
        ];

        return view('livewire.admin.businesses.index', [
            'businesses' => $businesses,
            'counts' => $counts,
        ]);
    }
}
