<?php

namespace App\Livewire\Chat;

use App\Models\User;
use App\Services\GroupGovernanceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class GroupForm extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $description = '';

    public string $joinSetting = 'direct_join';

    public $avatarFile = null;

    public string $avatarPreview = '';

    public string $memberSearch = '';

    public array $selectedMemberIds = [];

    public function addMember(int $userId): void
    {
        if (! in_array($userId, $this->selectedMemberIds, true)) {
            $this->selectedMemberIds[] = $userId;
        }
        $this->memberSearch = '';
    }

    public function removeMember(int $userId): void
    {
        $this->selectedMemberIds = array_values(array_diff($this->selectedMemberIds, [$userId]));
    }

    public function removeAvatar(): void
    {
        $this->avatarFile = null;
        $this->avatarPreview = '';
    }

    public function save()
    {
        $validated = $this->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'joinSetting' => 'required|in:direct_join,approval_required',
            'avatarFile' => 'nullable|image|max:5120',
        ]);

        $group = app(GroupGovernanceService::class)->createGroup(
            Auth::user(),
            $validated['title'],
            $validated['description'] ?: null,
            $this->avatarFile,
            $validated['joinSetting'],
            $this->selectedMemberIds,
        );

        return $this->redirect(route('chat.show', $group->id), navigate: false);
    }

    public function render()
    {
        $memberResults = collect();
        if (trim($this->memberSearch) !== '') {
            $term = $this->memberSearch;
            $memberResults = User::where('id', '!=', Auth::id())
                ->whereNotIn('id', $this->selectedMemberIds)
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

        return view('livewire.chat.group-form', [
            'memberResults' => $memberResults,
            'selectedMembers' => $selectedMembers,
        ]);
    }
}
