<?php

namespace App\Livewire\Admin\Locations;

use App\Models\Area;
use App\Models\City;
use Livewire\Component;

class Areas extends Component
{
    public City $city;

    public string $addAreaName = '';

    public ?int $deletingAreaId = null;

    public string $deletingAreaName = '';

    public function mount(int $id): void
    {
        $this->city = City::findOrFail($id);
    }

    public function addArea(): void
    {
        admin_authorize('locations', 'can_add');

        $this->validate([
            'addAreaName' => 'required|string|max:100|unique:areas,name,NULL,id,city_id,' . $this->city->id,
        ]);

        Area::create(['city_id' => $this->city->id, 'name' => $this->addAreaName]);

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
        return view('livewire.admin.locations.areas', [
            'areas' => $this->city->areas()->orderBy('name')->get(),
        ]);
    }
}
