<?php

namespace App\Livewire\Admin\SubAdmins;

use App\Models\SubAdminPermission;
use App\Models\User;
use Livewire\Component;

class Form extends Component
{
    public ?int $userId = null;

    public ?User $subAdminUser = null;

    public ?int $newSubAdminUserId = null;

    /** @var array<string, array<string, bool>> module => ability => bool */
    public array $permissions = [];

    public function mount(?int $id = null): void
    {
        foreach (SubAdminPermission::MODULES as $key => $label) {
            $this->permissions[$key] = [
                'can_view' => false,
                'can_add' => false,
                'can_edit' => false,
                'can_delete' => false,
                'can_approve' => false,
            ];
        }

        if (! $id) {
            return;
        }

        $user = User::where('role', 'sub_admin')->findOrFail($id);
        $this->userId = $user->id;
        $this->subAdminUser = $user;

        $existing = SubAdminPermission::where('user_id', $id)->get()->keyBy('module');

        foreach (SubAdminPermission::MODULES as $key => $label) {
            $row = $existing->get($key);
            if ($row) {
                $this->permissions[$key] = [
                    'can_view' => (bool) $row->can_view,
                    'can_add' => (bool) $row->can_add,
                    'can_edit' => (bool) $row->can_edit,
                    'can_delete' => (bool) $row->can_delete,
                    'can_approve' => (bool) $row->can_approve,
                ];
            }
        }
    }

    public function selectAll(): void
    {
        foreach ($this->permissions as $module => $abilities) {
            foreach ($abilities as $ability => $val) {
                $this->permissions[$module][$ability] = true;
            }
        }
    }

    public function clearAll(): void
    {
        foreach ($this->permissions as $module => $abilities) {
            foreach ($abilities as $ability => $val) {
                $this->permissions[$module][$ability] = false;
            }
        }
    }

    public function toggleRow(string $module): void
    {
        if (! isset($this->permissions[$module])) {
            return;
        }

        $allTrue = ! in_array(false, $this->permissions[$module], true);
        foreach ($this->permissions[$module] as $ability => $val) {
            $this->permissions[$module][$ability] = ! $allTrue;
        }
    }

    public function toggleColumn(string $ability): void
    {
        $allTrue = true;
        foreach ($this->permissions as $module => $abilities) {
            if (! ($abilities[$ability] ?? false)) {
                $allTrue = false;
                break;
            }
        }

        foreach ($this->permissions as $module => $abilities) {
            $this->permissions[$module][$ability] = ! $allTrue;
        }
    }

    public function save()
    {
        $targetUserId = $this->userId;

        if (! $targetUserId) {
            $this->validate([
                'newSubAdminUserId' => 'required|integer|exists:users,id',
            ]);

            $user = User::findOrFail($this->newSubAdminUserId);

            if ($user->role === 'admin') {
                $this->addError('newSubAdminUserId', 'This user is already a full admin.');

                return;
            }

            $user->update(['role' => 'sub_admin']);
            $targetUserId = $user->id;
        }

        foreach ($this->permissions as $module => $abilities) {
            SubAdminPermission::updateOrCreate(
                ['user_id' => $targetUserId, 'module' => $module],
                $abilities,
            );
        }

        return $this->redirect(route('admin.sub-admins.index'), navigate: false);
    }

    public function render()
    {
        $candidateOptions = [];
        $candidateValueMap = [];

        if (! $this->userId) {
            foreach (User::whereNotIn('role', ['admin', 'sub_admin'])->orderBy('name')->get(['id', 'name', 'phone']) as $u) {
                $label = $u->phone ? "{$u->name} ({$u->phone})" : $u->name;
                $candidateOptions[] = $label;
                $candidateValueMap[$label] = $u->id;
            }
        }

        return view('livewire.admin.sub-admins.form', [
            'candidateOptions' => $candidateOptions,
            'candidateValueMap' => $candidateValueMap,
            'modules' => SubAdminPermission::MODULES,
        ]);
    }
}
