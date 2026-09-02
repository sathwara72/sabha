<?php

namespace App\Livewire\Admin\Trustees;

use App\Models\Trustee;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $deletingId = null;

    public string $deletingName = '';

    public string $successMsg = '';

    // In-page Form Modal state
    public bool $isFormModalOpen = false;

    public ?int $editingId = null;

    public ?Trustee $editingTrustee = null;

    public ?int $userId = null;

    public string $position = '';

    public int $sortOrder = 0;

    public bool $isActive = true;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        admin_authorize('trustees', 'can_add');
        $this->resetValidation();
        $this->editingId = null;
        $this->editingTrustee = null;
        $this->userId = null;
        $this->position = '';
        $this->sortOrder = 0;
        $this->isActive = true;
        $this->isFormModalOpen = true;
    }

    public function openEdit(int $id): void
    {
        admin_authorize('trustees', 'can_edit');
        $this->resetValidation();
        $trustee = Trustee::with('user')->findOrFail($id);
        $this->editingId = $trustee->id;
        $this->editingTrustee = $trustee;
        $this->userId = $trustee->user_id;
        $this->position = $trustee->position;
        $this->sortOrder = $trustee->sort_order;
        $this->isActive = (bool) $trustee->is_active;
        $this->isFormModalOpen = true;
    }

    public function closeFormModal(): void
    {
        $this->isFormModalOpen = false;
        $this->editingId = null;
        $this->editingTrustee = null;
        $this->userId = null;
        $this->position = '';
        $this->sortOrder = 0;
        $this->isActive = true;
        $this->resetValidation();
    }

    public function saveTrustee(): void
    {
        admin_authorize('trustees', $this->editingId ? 'can_edit' : 'can_add');

        if ($this->editingId) {
            $this->validate([
                'position' => 'required|string|max:100',
                'sortOrder' => 'required|integer|min:0',
            ]);

            Trustee::findOrFail($this->editingId)->update([
                'position' => $this->position,
                'sort_order' => $this->sortOrder,
                'is_active' => $this->isActive,
            ]);

            $this->successMsg = 'Trustee updated successfully';
        } else {
            $this->validate([
                'userId' => 'required|integer|exists:users,id|unique:trustees,user_id',
                'position' => 'required|string|max:100',
            ], [], ['userId' => 'member']);

            $maxOrder = Trustee::max('sort_order') ?? -1;

            Trustee::create([
                'user_id' => $this->userId,
                'position' => $this->position,
                'sort_order' => $maxOrder + 1,
                'is_active' => true,
            ]);

            $this->successMsg = 'Trustee added successfully';
        }

        $this->closeFormModal();
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

        $candidateUsers = [];
        if ($this->isFormModalOpen && ! $this->editingId) {
            $candidateUsers = User::whereNotIn('role', ['admin', 'sub_admin'])
                ->where('registration_status', 'active')
                ->whereDoesntHave('trustee')
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
        }

        return view('livewire.admin.trustees.index', [
            'trustees' => $trustees,
            'totalCount' => Trustee::count(),
            'candidateUsers' => $candidateUsers,
        ]);
    }
}
