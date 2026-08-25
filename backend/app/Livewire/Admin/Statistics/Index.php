<?php

namespace App\Livewire\Admin\Statistics;

use App\Models\Statistic;
use Livewire\Component;

class Index extends Component
{
    public array $editValues = [];

    public ?int $updatingId = null;

    public string $successMsg = '';

    public string $errorMsg = '';

    public function mount(): void
    {
        $this->loadEditValues();
    }

    private function loadEditValues(): void
    {
        $this->editValues = Statistic::all()->mapWithKeys(function ($stat) {
            return [$stat->id => ['label' => $stat->label, 'value' => $stat->value]];
        })->all();
    }

    public function refreshData(): void
    {
        $this->loadEditValues();
    }

    public function update(int $id): void
    {
        $this->errorMsg = '';
        $this->successMsg = '';

        $data = $this->editValues[$id] ?? null;

        if (empty($data['label']) || empty($data['value'])) {
            $this->errorMsg = 'Label and value cannot be empty.';

            return;
        }

        $this->updatingId = $id;

        $statistic = Statistic::findOrFail($id);
        $statistic->update([
            'label' => $data['label'],
            'value' => $data['value'],
        ]);

        $this->successMsg = "\"{$data['label']}\" updated successfully!";
        $this->loadEditValues();
        $this->updatingId = null;
    }

    public function render()
    {
        return view('livewire.admin.statistics.index', [
            'stats' => Statistic::all(),
        ]);
    }
}
