<?php

namespace App\Livewire\Businesses;

use App\Models\Business;
use App\Models\BusinessCategory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Directory extends Component
{
    use WithPagination;

    private const ITEMS_PER_PAGE = 9;

    private const FALLBACK_CATEGORIES = [
        'Software Development',
        'Supply Chain',
        'Digital Marketing',
        'Construction',
        'Financial Services',
        'Renewables',
        'Creative Agency',
        'Venture Capital',
    ];

    #[Url]
    public string $search = '';

    #[Url]
    public string $category = 'All';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function categories(): array
    {
        $cats = BusinessCategory::active()->pluck('name')->all();

        return array_merge(['All'], empty($cats) ? self::FALLBACK_CATEGORIES : $cats);
    }

    public function render()
    {
        $query = Business::where('status', 'approved')
            ->with(['user', 'businessCategory'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->latest();

        if ($this->category !== 'All') {
            $query->where('category', $this->category);
        }

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return view('livewire.businesses.directory', [
            'businesses' => $query->paginate(self::ITEMS_PER_PAGE),
            'categories' => $this->categories(),
        ]);
    }
}
