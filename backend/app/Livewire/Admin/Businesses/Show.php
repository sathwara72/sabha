<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\Business;
use Livewire\Component;

class Show extends Component
{
    public int $businessId;

    public bool $rejectModalOpen = false;

    public string $rejectReason = '';

    public bool $deleteModalOpen = false;

    public bool $blockOwnerModalOpen = false;

    public function mount(int $id): void
    {
        $this->businessId = $id;
    }

    public function approve(): void
    {
        $this->business()->update([
            'status' => 'approved',
            'is_verified' => true,
            'rejection_reason' => null,
        ]);
    }

    public function openReject(): void
    {
        $this->rejectReason = '';
        $this->resetErrorBag('rejectReason');
        $this->rejectModalOpen = true;
    }

    public function cancelReject(): void
    {
        $this->rejectModalOpen = false;
        $this->rejectReason = '';
    }

    public function confirmReject(): void
    {
        $this->validate(['rejectReason' => 'required|string']);

        $this->business()->update([
            'status' => 'rejected',
            'is_verified' => false,
            'rejection_reason' => $this->rejectReason,
        ]);

        $this->cancelReject();
    }

    public function openDeleteModal(): void
    {
        $this->deleteModalOpen = true;
    }

    public function cancelDelete(): void
    {
        $this->deleteModalOpen = false;
    }

    public function confirmDelete()
    {
        $this->business()->delete();

        return redirect()->route('admin.businesses.index');
    }

    public function openBlockOwnerModal(): void
    {
        $this->blockOwnerModalOpen = true;
    }

    public function cancelBlockOwner(): void
    {
        $this->blockOwnerModalOpen = false;
    }

    public function confirmToggleBlockOwner(): void
    {
        $business = $this->business();
        $user = $business->user;

        if (! $user || $user->role === 'admin') {
            $this->blockOwnerModalOpen = false;

            return;
        }

        $user->is_blocked = ! $user->is_blocked;
        $user->save();

        if ($user->is_blocked) {
            $user->tokens()->delete();
        }

        $this->blockOwnerModalOpen = false;
    }

    private function business(): Business
    {
        return Business::findOrFail($this->businessId);
    }

    public function render()
    {
        $business = Business::with('user')->find($this->businessId);

        return view('livewire.admin.businesses.show', [
            'business' => $business,
        ]);
    }
}
