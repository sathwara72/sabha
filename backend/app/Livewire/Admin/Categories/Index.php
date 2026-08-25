<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Business;
use App\Models\BusinessCategory;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $isAddModalOpen = false;

    public string $addName = '';

    public ?int $editingId = null;

    public string $editName = '';

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
        $this->resetErrorBag('addName');
        $this->isAddModalOpen = true;
    }

    public function cancelAdd(): void
    {
        $this->isAddModalOpen = false;
        $this->addName = '';
    }

    public function addCategory(): void
    {
        $this->validate([
            'addName' => 'required|string|max:100|unique:business_categories,name',
        ]);

        $maxOrder = BusinessCategory::max('sort_order') ?? -1;

        BusinessCategory::create([
            'name' => $this->addName,
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        $this->successMsg = "Category \"{$this->addName}\" added successfully!";
        $this->cancelAdd();
    }

    public function openEditModal(int $id, string $name): void
    {
        $this->editingId = $id;
        $this->editName = $name;
        $this->resetErrorBag('editName');
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editName = '';
    }

    public function updateCategory(): void
    {
        $this->validate([
            'editName' => 'required|string|max:100|unique:business_categories,name,' . $this->editingId,
        ]);

        BusinessCategory::findOrFail($this->editingId)->update(['name' => $this->editName]);

        $this->successMsg = "Category updated to \"{$this->editName}\" successfully!";
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
        $name = $this->deletingName;
        BusinessCategory::findOrFail($this->deletingId)->delete();
        $this->successMsg = "\"{$name}\" category deleted";
        $this->cancelDelete();
    }

    public function render()
    {
        $query = BusinessCategory::orderBy('sort_order');

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $categories = $query->paginate(9);

        $counts = Business::whereIn('category', $categories->pluck('name'))
            ->selectRaw('category, count(*) as aggregate')
            ->groupBy('category')
            ->pluck('aggregate', 'category');

        $categories->getCollection()->transform(function ($cat) use ($counts) {
            $cat->businesses_count = $counts[$cat->name] ?? 0;

            return $cat;
        });

        return view('livewire.admin.categories.index', [
            'categories' => $categories,
            'totalCount' => BusinessCategory::count(),
        ]);
    }
}
