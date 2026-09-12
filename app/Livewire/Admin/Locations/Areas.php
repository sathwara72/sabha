<?php

namespace App\Livewire\Admin\Locations;

use App\Models\Area;
use App\Models\City;
use Livewire\Component;

class Areas extends Component
{
    public City $city;

    public string $search = '';

    public string $addAreaName = '';

    public ?int $deletingAreaId = null;

    public string $deletingAreaName = '';

    public string $successMsg = '';

    public function mount(int $id): void
    {
        $this->city = City::findOrFail($id);
    }

    public function addArea(): void
    {
        admin_authorize('locations', 'can_add');

        $this->validate([
            'addAreaName' => 'required|string|max:100|unique:areas,name,NULL,id,city_id,' . $this->city->id,
        ], [
            'addAreaName.required' => 'Please enter an area name.',
            'addAreaName.unique' => 'This area name already exists in ' . $this->city->name . '.',
        ]);

        Area::create([
            'city_id' => $this->city->id,
            'name' => trim($this->addAreaName),
        ]);

        $this->successMsg = "Area \"{$this->addAreaName}\" added successfully.";
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

        $name = $this->deletingAreaName;
        Area::findOrFail($this->deletingAreaId)->delete();
        $this->successMsg = "Area \"{$name}\" deleted.";
        $this->cancelDeleteArea();
    }

    public function render()
    {
        $query = $this->city->areas()->orderBy('name');

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $areas = $query->get();
        $totalCount = $this->city->areas()->count();

        return view('livewire.admin.locations.areas', [
            'areas' => $areas,
            'totalCount' => $totalCount,
        ]);
    }
}
