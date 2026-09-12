<?php

namespace App\Services;

use App\Events\ChatMessageSent;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Every group-membership/role mutation, in one place, so the main-admin
 * immunity rule and the admin-only checks can't drift between callers
 * (GroupForm, GroupSettings, and the admin moderation view). Mirrors
 * ChatService's shape: mutating methods throw \RuntimeException on a rule
 * violation, caught by the Livewire layer into a flash error.
 */
class GroupGovernanceService
{
    public function createGroup(
        User $creator,
        string $title,
        ?string $description,
        ?UploadedFile $avatar,
        string $joinSetting,
        array $initialMemberIds = [],
    ): Conversation {
        $data = [
            'type' => 'group',
            'title' => trim($title),
            'description' => $description ? trim($description) : null,
            'creator_id' => $creator->id,
            'join_setting' => in_array($joinSetting, ['direct_join', 'approval_required'], true) ? $joinSetting : 'direct_join',
        ];

        if ($avatar) {
            $data['avatar'] = $this->storeAvatar($avatar);
        }

        $group = Conversation::create($data);

        ConversationParticipant::create([
            'conversation_id' => $group->id,
            'user_id' => $creator->id,
            'role' => 'main_admin',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $initialMemberIds = array_values(array_diff(array_unique($initialMemberIds), [$creator->id]));
        if (! empty($initialMemberIds)) {
            $this->addMembers($group, $creator, $initialMemberIds);
        }

        return $group;
    }

    public function updateDetails(Conversation $group, User $actor, array $data, ?UploadedFile $avatar = null): void
    {
        $this->assertAdmin($group, $actor);

        $update = [];
        if (isset($data['title'])) {
            $update['title'] = trim($data['title']);
        }
        if (array_key_exists('description', $data)) {
            $update['description'] = trim((string) $data['description']) ?: null;
        }
        if (! empty($data['join_setting'])) {
            $update['join_setting'] = $data['join_setting'];
        }

        if ($avatar) {
            $update['avatar'] = $this->storeAvatar($avatar);
        }

        $group->update($update);
    }

    public function addMembers(Conversation $group, User $actor, array $userIds): void
    {
        $this->assertAdmin($group, $actor);

        foreach (array_unique($userIds) as $userId) {
            if ($userId == $actor->id) {
                continue;
            }

            $existing = ConversationParticipant::where('conversation_id', $group->id)
                ->where('user_id', $userId)
                ->first();

            if ($existing && $existing->status === 'active') {
                continue;
            }

            if ($existing) {
                $existing->update(['status' => 'active', 'joined_at' => now(), 'invited_by' => $actor->id]);
            } else {
                ConversationParticipant::create([
                    'conversation_id' => $group->id,
                    'user_id' => $userId,
                    'role' => 'member',
                    'status' => 'active',
                    'invited_by' => $actor->id,
                    'joined_at' => now(),
                ]);
            }

            $target = User::find($userId);
            $this->postSystemMessage($group, $actor, "{$actor->name} added {$target?->name} to the group");
        }

        $group->refresh();
    }

    public function requestToJoin(Conversation $group, User $user): void
    {
        if ($group->is_archived) {
            throw new \RuntimeException('This group no longer exists.');
        }

        $existing = ConversationParticipant::where('conversation_id', $group->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && in_array($existing->status, ['active', 'pending_approval'], true)) {
            return;
        }

        $status = $group->join_setting === 'approval_required' ? 'pending_approval' : 'active';

        if ($existing) {
            $existing->update(['status' => $status, 'joined_at' => $status === 'active' ? now() : null]);
        } else {
            ConversationParticipant::create([
                'conversation_id' => $group->id,
                'user_id' => $user->id,
                'role' => 'member',
                'status' => $status,
                'joined_at' => $status === 'active' ? now() : null,
            ]);
        }

        if ($status === 'active') {
            $this->postSystemMessage($group, $user, "{$user->name} joined the group");
        }
    }

    public function approveJoinRequest(Conversation $group, User $actor, User $applicant): void
    {
        $this->assertAdmin($group, $actor);

        $request = $this->findParticipant($group, $applicant);
        if (! $request || $request->status !== 'pending_approval') {
            throw new \RuntimeException('This join request is no longer pending.');
        }

        $request->update(['status' => 'active', 'joined_at' => now()]);
        $this->postSystemMessage($group, $actor, "{$applicant->name} joined the group");
    }

    public function rejectJoinRequest(Conversation $group, User $actor, User $applicant): void
    {
        $this->assertAdmin($group, $actor);

        $request = $this->findParticipant($group, $applicant);
        if (! $request || $request->status !== 'pending_approval') {
            throw new \RuntimeException('This join request is no longer pending.');
        }

        $request->update(['status' => 'rejected']);
    }

    public function promote(Conversation $group, User $actor, User $target): void
    {
        $this->assertAdmin($group, $actor);

        $participant = $this->findActiveParticipant($group, $target);
        if (! $participant) {
            throw new \RuntimeException('This member is not part of the group.');
        }
        if ($participant->role !== 'member') {
            throw new \RuntimeException('This member already has admin rights.');
        }

        $participant->update(['role' => 'admin']);
        $this->postSystemMessage($group, $actor, "{$actor->name} made {$target->name} a group admin");
    }

    public function demote(Conversation $group, User $actor, User $target): void
    {
        $this->assertAdmin($group, $actor);

        $participant = $this->findActiveParticipant($group, $target);
        if (! $participant) {
            throw new \RuntimeException('This member is not part of the group.');
        }
        if ($participant->role === 'main_admin') {
            throw new \RuntimeException('The main group admin cannot be demoted.');
        }
        if ($participant->role !== 'admin') {
            throw new \RuntimeException('This member is not an admin.');
        }

        $participant->update(['role' => 'member']);
        $this->postSystemMessage($group, $actor, "{$actor->name} removed {$target->name} as group admin");
    }

    public function removeMember(Conversation $group, User $actor, User $target): void
    {
        $this->assertAdmin($group, $actor);

        $participant = $this->findActiveParticipant($group, $target);
        if (! $participant) {
            throw new \RuntimeException('This member is not part of the group.');
        }
        if ($participant->role === 'main_admin') {
            throw new \RuntimeException('The main group admin cannot be removed.');
        }

        $participant->update(['status' => 'removed']);
        $this->postSystemMessage($group, $actor, "{$actor->name} removed {$target->name} from the group");
    }

    public function leaveGroup(Conversation $group, User $user): void
    {
        $participant = $this->findActiveParticipant($group, $user);
        if (! $participant) {
            throw new \RuntimeException('You are not part of this group.');
        }
        if ($participant->role === 'main_admin') {
            throw new \RuntimeException('Transfer group ownership to another member before leaving.');
        }

        $participant->update(['status' => 'left']);
        $this->postSystemMessage($group, $user, "{$user->name} left the group");
    }

    public function transferOwnership(Conversation $group, User $currentMainAdmin, User $newMainAdmin): void
    {
        $current = $this->findActiveParticipant($group, $currentMainAdmin);
        if (! $current || $current->role !== 'main_admin') {
            throw new \RuntimeException('Only the main group admin can transfer ownership.');
        }

        $target = $this->findActiveParticipant($group, $newMainAdmin);
        if (! $target) {
            throw new \RuntimeException('That member is not part of the group.');
        }

        $current->update(['role' => 'admin']);
        $target->update(['role' => 'main_admin']);
        $group->update(['creator_id' => $newMainAdmin->id]);

        $this->postSystemMessage($group, $currentMainAdmin, "{$currentMainAdmin->name} made {$newMainAdmin->name} the main group admin");
    }

    public function deleteGroup(Conversation $group, User $actor): void
    {
        $participant = $this->findActiveParticipant($group, $actor);
        if (! $participant || $participant->role !== 'main_admin') {
            throw new \RuntimeException('Only the main group admin can delete this group.');
        }

        $group->update(['is_archived' => true]);
    }

    private function assertAdmin(Conversation $group, User $user): void
    {
        if (! $group->isGroupAdmin($user)) {
            throw new \RuntimeException('Only group admins can do that.');
        }
    }

    private function findParticipant(Conversation $group, User $user): ?ConversationParticipant
    {
        return ConversationParticipant::where('conversation_id', $group->id)
            ->where('user_id', $user->id)
            ->first();
    }

    private function findActiveParticipant(Conversation $group, User $user): ?ConversationParticipant
    {
        return ConversationParticipant::where('conversation_id', $group->id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();
    }

    private function storeAvatar(UploadedFile $avatar): string
    {
        $fileName = time() . '_group_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
        $avatar->storeAs('chat-groups', $fileName, 'public');

        return '/storage/chat-groups/' . $fileName;
    }

    private function postSystemMessage(Conversation $group, User $actor, string $body): void
    {
        $message = ChatMessage::create([
            'conversation_id' => $group->id,
            'sender_id' => $actor->id,
            'message_type' => 'system_event',
            'body' => $body,
        ]);

        $message->setRelation('sender', $actor);

        try {
            broadcast(new ChatMessageSent($message));
        } catch (\Throwable $e) {
            Log::error('Chat system-message broadcast failed: ' . $e->getMessage());
        }
    }
}
