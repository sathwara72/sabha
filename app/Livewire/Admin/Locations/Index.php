<?php

namespace App\Livewire\Admin\Locations;

use App\Models\Area;
use App\Models\City;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $isAddCityModalOpen = false;

    public string $addCityName = '';

    public ?int $editingCityId = null;

    public string $editCityName = '';

    public ?int $deletingCityId = null;

    public string $deletingCityName = '';

    public ?int $managingCityId = null;

    public string $addAreaName = '';

    public ?int $deletingAreaId = null;

    public string $deletingAreaName = '';

    public string $successMsg = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // ───────────────────────── Cities ─────────────────────────

    public function openAddCityModal(): void
    {
        $this->addCityName = '';
        $this->resetErrorBag('addCityName');
        $this->isAddCityModalOpen = true;
    }

    public function cancelAddCity(): void
    {
        $this->isAddCityModalOpen = false;
        $this->addCityName = '';
    }

    public function addCity(): void
    {
        admin_authorize('locations', 'can_add');

        $this->validate(['addCityName' => 'required|string|max:100|unique:cities,name']);

        $maxOrder = City::max('sort_order') ?? -1;
        City::create(['name' => $this->addCityName, 'sort_order' => $maxOrder + 1, 'is_active' => true]);

        $this->successMsg = "City \"{$this->addCityName}\" added successfully!";
        $this->cancelAddCity();
    }

    public function openEditCityModal(int $id, string $name): void
    {
        $this->editingCityId = $id;
        $this->editCityName = $name;
        $this->resetErrorBag('editCityName');
    }

    public function cancelEditCity(): void
    {
        $this->editingCityId = null;
        $this->editCityName = '';
    }

    public function updateCity(): void
    {
        admin_authorize('locations', 'can_edit');

        $this->validate(['editCityName' => 'required|string|max:100|unique:cities,name,' . $this->editingCityId]);

        City::findOrFail($this->editingCityId)->update(['name' => $this->editCityName]);

        $this->successMsg = "City updated to \"{$this->editCityName}\" successfully!";
        $this->cancelEditCity();
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

        if ($this->managingCityId === $this->deletingCityId) {
            $this->managingCityId = null;
        }

        $this->successMsg = "\"{$name}\" city deleted (and all its areas)";
        $this->cancelDeleteCity();
    }

    // ───────────────────────── Areas ─────────────────────────

    public function manageAreas(int $cityId): void
    {
        $this->managingCityId = $cityId;
        $this->addAreaName = '';
        $this->resetErrorBag('addAreaName');
    }

    public function closeManageAreas(): void
    {
        $this->managingCityId = null;
        $this->addAreaName = '';
    }

    public function addArea(): void
    {
        admin_authorize('locations', 'can_add');

        $this->validate([
            'addAreaName' => 'required|string|max:100|unique:areas,name,NULL,id,city_id,' . $this->managingCityId,
        ]);

        Area::create(['city_id' => $this->managingCityId, 'name' => $this->addAreaName]);

        $this->addAreaName = '';
        $this->resetErrorBag('addAreaName');
    }

    public function openDeleteArea(int $id, string $name): void
    {
        $this->deletingAreaId = $id;
        $this->deletingAreaName = $name;
    }

    public function cancelDeleteArea(): void
    {
        $this->deletingAreaId = null;
        $this->deletingAreaName = '';
    }

    public function confirmDeleteArea(): void
    {
        admin_authorize('locations', 'can_delete');

        Area::findOrFail($this->deletingAreaId)->delete();
        $this->cancelDeleteArea();
    }

    public function render()
    {
        $query = City::withCount('areas')->orderBy('sort_order');

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $cities = $query->paginate(10);

        $managingCity = $this->managingCityId ? City::with('areas')->find($this->managingCityId) : null;

        return view('livewire.admin.locations.index', [
            'cities' => $cities,
            'totalCount' => City::count(),
            'managingCity' => $managingCity,
        ]);
    }
}
