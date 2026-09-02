<?php

namespace App\Livewire\Admin\Trustees;

use App\Models\Trustee;
use App\Models\User;
use Livewire\Component;

class Form extends Component
{
    public ?int $trusteeId = null;

    public ?Trustee $trustee = null;

    public ?int $userId = null;

    public string $position = '';

    public int $sortOrder = 0;

    public bool $isActive = true;

    public function mount(?int $id = null): void
    {
        if (! $id) {
            return;
        }

        $trustee = Trustee::with('user')->findOrFail($id);
        $this->trusteeId = $trustee->id;
        $this->trustee = $trustee;
        $this->userId = $trustee->user_id;
        $this->position = $trustee->position;
        $this->sortOrder = $trustee->sort_order;
        $this->isActive = $trustee->is_active;
    }

    public function save()
    {
        admin_authorize('trustees', $this->trusteeId ? 'can_edit' : 'can_add');

        if ($this->trusteeId) {
            $this->validate([
                'position' => 'required|string|max:100',
                'sortOrder' => 'required|integer|min:0',
            ]);

            Trustee::findOrFail($this->trusteeId)->update([
                'position' => $this->position,
                'sort_order' => $this->sortOrder,
                'is_active' => $this->isActive,
            ]);
        } else {
            $this->validate([
                'userId' => 'required|integer|exists:users,id|unique:trustees,user_id',
                'position' => 'required|string|max:100',
            ], [], ['userId' => 'member']);

            $maxOrder = Trustee::max('sort_order') ?? -1;

            Trustee::create([
                'user_id' => $this->userId,
                'position' => $this->position,
                'sort_order' => $maxOrder + 1,
                'is_active' => true,
            ]);
        }

        return $this->redirect(route('admin.trustees.index'), navigate: false);
    }

    public function render()
    {
        $candidateLabels = [];
        $candidateValueMap = [];

        if (! $this->trusteeId) {
            foreach (User::whereNotIn('role', ['admin', 'sub_admin'])->where('registration_status', 'active')->whereDoesntHave('trustee')->orderBy('name')->get(['id', 'name', 'email']) as $u) {
                $label = "{$u->name} ({$u->email})";
                $candidateLabels[] = $label;
                $candidateValueMap[$label] = $u->id;
            }
        }

        return view('livewire.admin.trustees.form', [
            'candidateLabels' => $candidateLabels,
            'candidateValueMap' => $candidateValueMap,
        ]);
    }
}
