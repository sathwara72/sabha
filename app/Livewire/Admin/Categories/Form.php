<?php

namespace App\Livewire\Admin\Categories;

use App\Models\BusinessCategory;
use Livewire\Component;

class Form extends Component
{
    public ?int $categoryId = null;

    public string $name = '';

    public function mount(?int $id = null): void
    {
        if (! $id) {
            return;
        }

        $category = BusinessCategory::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
    }

    public function save()
    {
        admin_authorize('categories', $this->categoryId ? 'can_edit' : 'can_add');

        $this->validate([
            'name' => 'required|string|max:100|unique:business_categories,name,' . $this->categoryId,
        ]);

        if ($this->categoryId) {
            BusinessCategory::findOrFail($this->categoryId)->update(['name' => $this->name]);
        } else {
            $maxOrder = BusinessCategory::max('sort_order') ?? -1;
            BusinessCategory::create([
                'name' => $this->name,
                'sort_order' => $maxOrder + 1,
                'is_active' => true,
            ]);
        }

        return $this->redirect(route('admin.categories.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.categories.form');
    }
}
