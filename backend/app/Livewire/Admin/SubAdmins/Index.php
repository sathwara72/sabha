<?php

namespace App\Livewire\Admin\SubAdmins;

use App\Models\SubAdminPermission;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    // ---- Add Sub-Admin ----
    public bool $isAddModalOpen = false;

    public ?int $newSubAdminUserId = null;

    // ---- Permission matrix ----
    public ?int $managingUserId = null;

    /** @var array<string, array<string, bool>> module => ability => bool */
    public array $permissions = [];

    public ?int $revokingId = null;

    public string $successMsg = '';

    public function openAdd(): void
    {
        $this->newSubAdminUserId = null;
        $this->resetErrorBag();
        $this->isAddModalOpen = true;
    }

    public function cancelAdd(): void
    {
        $this->isAddModalOpen = false;
    }

    public function addSubAdmin(): void
    {
        $this->validate([
            'newSubAdminUserId' => 'required|integer|exists:users,id',
        ]);

        $user = User::findOrFail($this->newSubAdminUserId);

        if ($user->role === 'admin') {
            $this->addError('newSubAdminUserId', 'This user is already a full admin.');

            return;
        }

        $user->update(['role' => 'sub_admin']);

        $this->isAddModalOpen = false;
        $this->successMsg = "{$user->name} is now a sub-admin. Set their permissions below.";
        $this->manage($user->id);
    }

    public function manage(int $userId): void
    {
        $user = User::where('role', 'sub_admin')->findOrFail($userId);
        $this->managingUserId = $user->id;

        $existing = SubAdminPermission::where('user_id', $userId)->get()->keyBy('module');

        $this->permissions = [];
        foreach (SubAdminPermission::MODULES as $key => $label) {
            $row = $existing->get($key);
            $this->permissions[$key] = [
                'can_view' => (bool) ($row->can_view ?? false),
                'can_add' => (bool) ($row->can_add ?? false),
                'can_edit' => (bool) ($row->can_edit ?? false),
                'can_delete' => (bool) ($row->can_delete ?? false),
                'can_approve' => (bool) ($row->can_approve ?? false),
            ];
        }
    }

    public function closeManage(): void
    {
        $this->managingUserId = null;
        $this->permissions = [];
    }

    public function savePermissions(): void
    {
        foreach ($this->permissions as $module => $abilities) {
            SubAdminPermission::updateOrCreate(
                ['user_id' => $this->managingUserId, 'module' => $module],
                $abilities,
            );
        }

        $this->successMsg = 'Permissions saved successfully!';
        $this->closeManage();
    }

    public function openRevoke(int $id): void
    {
        $this->revokingId = $id;
    }

    public function cancelRevoke(): void
    {
        $this->revokingId = null;
    }

    public function confirmRevoke(): void
    {
        $user = User::where('role', 'sub_admin')->findOrFail($this->revokingId);
        $user->update(['role' => 'user']);
        SubAdminPermission::where('user_id', $user->id)->delete();

        $this->revokingId = null;
        $this->successMsg = 'Sub-admin access revoked.';
    }

    public function render()
    {
        $query = User::where('role', 'sub_admin')->withCount('subAdminPermissions');

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subAdmins = $query->orderBy('name')->paginate(10);

        $candidateOptions = [];
        $candidateValueMap = [];
        foreach (User::whereNotIn('role', ['admin', 'sub_admin'])->orderBy('name')->get(['id', 'name', 'phone']) as $u) {
            $label = $u->phone ? "{$u->name} ({$u->phone})" : $u->name;
            $candidateOptions[] = $label;
            $candidateValueMap[$label] = $u->id;
        }

        return view('livewire.admin.sub-admins.index', [
            'subAdmins' => $subAdmins,
            'candidateOptions' => $candidateOptions,
            'candidateValueMap' => $candidateValueMap,
            'managingUser' => $this->managingUserId ? User::find($this->managingUserId) : null,
            'modules' => SubAdminPermission::MODULES,
        ]);
    }
}
