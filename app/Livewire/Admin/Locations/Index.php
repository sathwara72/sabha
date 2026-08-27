<?php

namespace App\Livewire\Admin\Locations;

use App\Models\City;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $deletingCityId = null;

    public string $deletingCityName = '';

    public string $successMsg = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openDeleteCity(int $id, string $name): void
    {
        $this->deletingCityId = $id;
        $this->deletingCityName = $name;
    }

    public function cancelDeleteCity(): void
    {
        $this->deletingCityId = null;
        $this->deletingCityName = '';
    }

    public function confirmDeleteCity(): void
    {
        admin_authorize('locations', 'can_delete');

        $name = $this->deletingCityName;
        City::findOrFail($this->deletingCityId)->delete();

        $this->successMsg = "\"{$name}\" city deleted (and all its areas)";
        $this->cancelDeleteCity();
    }

    public function render()
    {
        $query = City::withCount('areas')->orderBy('sort_order');

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $cities = $query->paginate(10);

        return view('livewire.admin.locations.index', [
            'cities' => $cities,
            'totalCount' => City::count(),
        ]);
    }
}
