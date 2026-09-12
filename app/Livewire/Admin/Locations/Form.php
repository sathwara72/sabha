<?php

namespace App\Livewire\Admin\Locations;

use App\Models\City;
use Livewire\Component;

class Form extends Component
{
    public ?int $cityId = null;

    public string $name = '';

    public function mount(?int $id = null): void
    {
        if (! $id) {
            return;
        }

        $city = City::findOrFail($id);
        $this->cityId = $city->id;
        $this->name = $city->name;
    }

    public function save()
    {
        admin_authorize('locations', $this->cityId ? 'can_edit' : 'can_add');

        $this->validate([
            'name' => 'required|string|max:100|unique:cities,name,' . $this->cityId,
        ]);

        if ($this->cityId) {
            City::findOrFail($this->cityId)->update(['name' => $this->name]);
        } else {
            $maxOrder = City::max('sort_order') ?? -1;
            City::create(['name' => $this->name, 'sort_order' => $maxOrder + 1, 'is_active' => true]);
        }

        return $this->redirect(route('admin.locations.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.locations.form');
    }
}
