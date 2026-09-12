<?php

namespace App\Livewire\Admin\Locations;

use App\Models\City;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    // City Modal state (Add / Edit)
    public bool $showCityModal = false;

    public ?int $cityId = null;

    public string $name = '';

    // Delete City Modal state
    public ?int $deletingCityId = null;

    public string $deletingCityName = '';

    public string $successMsg = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateCityModal(): void
    {
        admin_authorize('locations', 'can_add');
        $this->resetValidation();
        $this->cityId = null;
        $this->name = '';
        $this->showCityModal = true;
    }

    public function openEditCityModal(int $id): void
    {
        admin_authorize('locations', 'can_edit');
        $this->resetValidation();
        $city = City::findOrFail($id);
        $this->cityId = $city->id;
        $this->name = $city->name;
        $this->showCityModal = true;
    }

    public function closeCityModal(): void
    {
        $this->showCityModal = false;
        $this->cityId = null;
        $this->name = '';
        $this->resetValidation();
    }

    public function saveCity(): void
    {
        admin_authorize('locations', $this->cityId ? 'can_edit' : 'can_add');

        $this->validate([
            'name' => 'required|string|max:100|unique:cities,name,' . $this->cityId,
        ]);

        if ($this->cityId) {
            $city = City::findOrFail($this->cityId);
            $city->update(['name' => $this->name]);
            $this->successMsg = "City \"{$this->name}\" updated successfully.";
        } else {
            $maxOrder = City::max('sort_order') ?? -1;
            City::create([
                'name' => $this->name,
                'sort_order' => $maxOrder + 1,
                'is_active' => true,
            ]);
            $this->successMsg = "City \"{$this->name}\" created successfully.";
        }

        $this->closeCityModal();
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

        $this->successMsg = "\"{$name}\" city and all its areas deleted.";
        $this->cancelDeleteCity();
    }

    public function render()
    {
        $query = City::withCount('areas')->orderBy('sort_order');

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $cities = $query->paginate(12);

        return view('livewire.admin.locations.index', [
            'cities' => $cities,
            'totalCount' => City::count(),
        ]);
    }
}
