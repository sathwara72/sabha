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

    public ?int $revokingId = null;

    public string $successMsg = '';

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

        return view('livewire.admin.sub-admins.index', [
            'subAdmins' => $subAdmins,
        ]);
    }
}
