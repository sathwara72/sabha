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

    // Modal state for Add / Edit
    public bool $showModal = false;

    public ?int $categoryId = null;

    public string $name = '';

    // Delete Modal State
    public ?int $deletingId = null;

    public string $deletingName = '';

    public string $successMsg = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        admin_authorize('categories', 'can_add');
        $this->resetValidation();
        $this->categoryId = null;
        $this->name = '';
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        admin_authorize('categories', 'can_edit');
        $this->resetValidation();
        $category = BusinessCategory::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->categoryId = null;
        $this->name = '';
        $this->resetValidation();
    }

    public function saveCategory(): void
    {
        admin_authorize('categories', $this->categoryId ? 'can_edit' : 'can_add');

        $this->validate([
            'name' => 'required|string|max:100|unique:business_categories,name,' . $this->categoryId,
        ]);

        if ($this->categoryId) {
            $cat = BusinessCategory::findOrFail($this->categoryId);
            $cat->update(['name' => $this->name]);
            $this->successMsg = "Category \"{$this->name}\" updated successfully.";
        } else {
            $maxOrder = BusinessCategory::max('sort_order') ?? -1;
            BusinessCategory::create([
                'name' => $this->name,
                'sort_order' => $maxOrder + 1,
                'is_active' => true,
            ]);
            $this->successMsg = "Category \"{$this->name}\" created successfully.";
        }

        $this->closeModal();
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
        admin_authorize('categories', 'can_delete');

        $name = $this->deletingName;
        BusinessCategory::findOrFail($this->deletingId)->delete();
        $this->successMsg = "Category \"{$name}\" deleted.";
        $this->cancelDelete();
    }

    public function render()
    {
        $query = BusinessCategory::orderBy('sort_order');

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $categories = $query->paginate(24);

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
