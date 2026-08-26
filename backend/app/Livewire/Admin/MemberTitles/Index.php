<?php

namespace App\Livewire\Admin\MemberTitles;

use App\Models\MemberTitle;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public const COLORS = ['primary', 'emerald', 'amber', 'rose', 'indigo', 'sky', 'violet', 'slate'];

    public string $search = '';

    public bool $isAddModalOpen = false;

    public string $addName = '';

    public string $addColor = 'primary';

    public ?int $editingId = null;

    public string $editName = '';

    public string $editColor = 'primary';

    public ?int $deletingId = null;

    public string $deletingName = '';

    public string $successMsg = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openAddModal(): void
    {
        $this->addName = '';
        $this->addColor = 'primary';
        $this->resetErrorBag('addName');
        $this->isAddModalOpen = true;
    }

    public function cancelAdd(): void
    {
        $this->isAddModalOpen = false;
        $this->addName = '';
    }

    public function addTitle(): void
    {
        admin_authorize('member-titles', 'can_add');

        $this->validate([
            'addName' => 'required|string|max:100|unique:member_titles,name',
            'addColor' => 'required|string|in:' . implode(',', self::COLORS),
        ]);

        $maxOrder = MemberTitle::max('sort_order') ?? -1;

        MemberTitle::create([
            'name' => $this->addName,
            'badge_color' => $this->addColor,
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        $this->successMsg = "Member title \"{$this->addName}\" added successfully!";
        $this->cancelAdd();
    }

    public function openEditModal(int $id, string $name, string $color): void
    {
        $this->editingId = $id;
        $this->editName = $name;
        $this->editColor = $color;
        $this->resetErrorBag('editName');
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editName = '';
    }

    public function updateTitle(): void
    {
        admin_authorize('member-titles', 'can_edit');

        $this->validate([
            'editName' => 'required|string|max:100|unique:member_titles,name,' . $this->editingId,
            'editColor' => 'required|string|in:' . implode(',', self::COLORS),
        ]);

        MemberTitle::findOrFail($this->editingId)->update([
            'name' => $this->editName,
            'badge_color' => $this->editColor,
        ]);

        $this->successMsg = "Member title updated to \"{$this->editName}\" successfully!";
        $this->cancelEdit();
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
            'colors' => self::COLORS,
        ]);
    }
}
