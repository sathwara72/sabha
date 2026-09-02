<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\User;
use App\Services\GroupGovernanceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class GroupSettings extends Component
{
    use WithFileUploads;

    public int $groupId;

    public string $title = '';

    public string $description = '';

    public string $joinSetting = 'direct_join';

    public $avatarFile = null;

    public string $avatarPreview = '';

    public string $memberSearch = '';

    public array $selectedMemberIds = [];

    public string $errorMessage = '';

    public string $successMessage = '';

    public function mount(int $id): void
    {
        $group = Conversation::where('type', 'group')->findOrFail($id);

        abort_unless($this->isActiveMember($group), 403);

        $this->groupId = $group->id;
        $this->title = $group->title ?? '';
        $this->description = $group->description ?? '';
        $this->joinSetting = $group->join_setting;
        $this->avatarPreview = media_url($group->avatar) ?? '';
    }

    private function isActiveMember(Conversation $group): bool
    {
        return $group->participants()->where('user_id', Auth::id())->where('status', 'active')->exists();
    }

    private function group(): Conversation
    {
        return Conversation::where('type', 'group')->findOrFail($this->groupId);
    }

    public function removeAvatar(): void
    {
        $this->avatarFile = null;
        $this->avatarPreview = '';
    }

    public function saveDetails(): void
    {
        $this->errorMessage = '';
        $this->successMessage = '';

        $validated = $this->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'joinSetting' => 'required|in:direct_join,approval_required',
            'avatarFile' => 'nullable|image|max:5120',
        ]);

        try {
            app(GroupGovernanceService::class)->updateDetails(
                $this->group(),
                Auth::user(),
                $validated,
                $this->avatarFile,
            );
            $this->avatarFile = null;
            $this->avatarPreview = media_url($this->group()->avatar) ?? '';
            $this->successMessage = 'Group details updated.';
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function addSelectedMember(int $userId): void
    {
        if (! in_array($userId, $this->selectedMemberIds, true)) {
            $this->selectedMemberIds[] = $userId;
        }
        $this->memberSearch = '';
    }

    public function removeSelectedMember(int $userId): void
    {
        $this->selectedMemberIds = array_values(array_diff($this->selectedMemberIds, [$userId]));
    }

    public function submitAddMembers(): void
    {
        $this->errorMessage = '';
        $this->successMessage = '';

        if (empty($this->selectedMemberIds)) {
            return;
        }

        try {
            app(GroupGovernanceService::class)->addMembers($this->group(), Auth::user(), $this->selectedMemberIds);
            $this->selectedMemberIds = [];
            $this->successMessage = 'Members added.';
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function approveRequest(int $userId): void
    {
        $this->runGovernanceAction(fn ($svc, $group) => $svc->approveJoinRequest($group, Auth::user(), User::findOrFail($userId)));
    }

    public function rejectRequest(int $userId): void
    {
        $this->runGovernanceAction(fn ($svc, $group) => $svc->rejectJoinRequest($group, Auth::user(), User::findOrFail($userId)));
    }

    public function promoteMember(int $userId): void
    {
        $this->runGovernanceAction(fn ($svc, $group) => $svc->promote($group, Auth::user(), User::findOrFail($userId)));
    }

    public function demoteMember(int $userId): void
    {
        $this->runGovernanceAction(fn ($svc, $group) => $svc->demote($group, Auth::user(), User::findOrFail($userId)));
    }

    public function removeMemberAction(int $userId): void
    {
        $this->runGovernanceAction(fn ($svc, $group) => $svc->removeMember($group, Auth::user(), User::findOrFail($userId)));
    }

    public function transferOwnershipTo(int $userId): void
    {
        $this->runGovernanceAction(fn ($svc, $group) => $svc->transferOwnership($group, Auth::user(), User::findOrFail($userId)));
    }

    public function leaveGroupAction()
    {
        try {
            app(GroupGovernanceService::class)->leaveGroup($this->group(), Auth::user());

            return $this->redirect(route('chat.index'), navigate: false);
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function deleteGroupAction()
    {
        try {
            app(GroupGovernanceService::class)->deleteGroup($this->group(), Auth::user());

            return $this->redirect(route('chat.index'), navigate: false);
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    private function runGovernanceAction(\Closure $action): void
    {
        $this->errorMessage = '';
        $this->successMessage = '';

        try {
            $action(app(GroupGovernanceService::class), $this->group());
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        $group = Conversation::where('type', 'group')->with('participants.user')->findOrFail($this->groupId);

        $isAdmin = $group->isGroupAdmin(Auth::user());
        $myRole = $group->participantRole(Auth::user());

        $roleOrder = ['main_admin' => 0, 'admin' => 1, 'member' => 2];
        $members = $group->participants
            ->where('status', 'active')
            ->sortBy(fn ($p) => [$roleOrder[$p->role] ?? 3, $p->user?->name])
            ->values();

        $pendingRequests = $group->participants->where('status', 'pending_approval')->values();

        $memberResults = collect();
        if (trim($this->memberSearch) !== '') {
            $term = $this->memberSearch;
            $existingIds = $group->participants->where('status', 'active')->pluck('user_id')->merge($this->selectedMemberIds);
            $memberResults = User::where('id', '!=', Auth::id())
                ->whereNotIn('id', $existingIds)
                ->where('registration_status', 'active')
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                })
                ->orderBy('name')
                ->limit(15)
                ->get();
        }

        $selectedMembers = empty($this->selectedMemberIds)
            ? collect()
            : User::whereIn('id', $this->selectedMemberIds)->get()->sortBy('name')->values();

        return view('livewire.chat.group-settings', [
            'group' => $group,
            'isAdmin' => $isAdmin,
            'myRole' => $myRole,
            'members' => $members,
            'pendingRequests' => $pendingRequests,
            'memberResults' => $memberResults,
            'selectedMembers' => $selectedMembers,
        ]);
    }
}
