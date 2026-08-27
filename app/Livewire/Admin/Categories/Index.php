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
        admin_authorize('categories', 'can_delete');

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
