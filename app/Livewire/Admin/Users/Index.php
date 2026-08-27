<?php

namespace App\Livewire\Admin\Users;

use App\Models\MemberTitle;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $selectedUserId = null;

    public ?int $blockingUserId = null;

    public ?int $deletingUserId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function view(int $id): void
    {
        $this->selectedUserId = $id;
    }

    public function closeView(): void
    {
        $this->selectedUserId = null;
    }

    public function openBlock(int $id): void
    {
        $user = User::find($id);
        if (! $user || $user->role === 'admin') {
            return;
        }
        $this->blockingUserId = $id;
    }

    public function cancelBlock(): void
    {
        $this->blockingUserId = null;
    }

    public function confirmToggleBlock(): void
    {
        admin_authorize('users', 'can_edit');

        $user = User::findOrFail($this->blockingUserId);
        $user->is_blocked = ! $user->is_blocked;
        $user->save();

        if ($user->is_blocked) {
            $user->tokens()->delete();
        }

        $this->blockingUserId = null;
    }

    public function openDelete(int $id): void
    {
        $user = User::find($id);
        if (! $user || $user->role === 'admin') {
            return;
        }
        $this->deletingUserId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingUserId = null;
    }

    public function confirmDelete(): void
    {
        admin_authorize('users', 'can_delete');

        $user = User::findOrFail($this->deletingUserId);

        if ($user->business) {
            $user->business()->delete();
        }

        $user->tokens()->delete();
        $user->delete();

        if ($this->selectedUserId === $this->deletingUserId) {
            $this->selectedUserId = null;
        }

        $this->deletingUserId = null;
    }

    public function assignTitle(int $userId, ?int $titleId): void
    {
        admin_authorize('users', 'can_edit');

        User::findOrFail($userId)->update(['member_title_id' => $titleId ?: null]);
    }

    public function render()
    {
        $query = User::with(['business', 'memberTitle'])->orderByDesc('created_at')->orderByDesc('id');

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10);

        return view('livewire.admin.users.index', [
            'users' => $users,
            'selectedUser' => $this->selectedUserId ? User::with(['business', 'memberTitle'])->find($this->selectedUserId) : null,
            'blockingUser' => $this->blockingUserId ? User::find($this->blockingUserId) : null,
            'deletingUser' => $this->deletingUserId ? User::find($this->deletingUserId) : null,
            'memberTitles' => MemberTitle::active()->get(),
        ]);
    }
}
