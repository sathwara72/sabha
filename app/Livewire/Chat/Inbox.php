<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\User;
use App\Services\ChatService;
use App\Services\GroupGovernanceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Inbox extends Component
{
    public ?int $activeId = null;

    public string $search = '';

    public function startChat(int $userId): void
    {
        $other = User::findOrFail($userId);

        $conversation = app(ChatService::class)->findOrCreateDirect(Auth::user(), $other);

        $this->redirect(route('chat.show', $conversation->id), navigate: false);
    }

    public function deleteConversation(int $convId): void
    {
        $conversation = Conversation::findOrFail($convId);
        $userId = Auth::id();
        $me = $conversation->participants->firstWhere('user_id', $userId);
        if (! $me || $me->status !== 'active') {
            return;
        }

        if ($conversation->type === 'direct') {
            app(ChatService::class)->hideForUser($conversation, Auth::user());
        } else {
            if ($conversation->isMainAdmin(Auth::user())) {
                app(GroupGovernanceService::class)->deleteGroup($conversation, Auth::user());
            } else {
                app(GroupGovernanceService::class)->leaveGroup($conversation, Auth::user());
            }
        }

        if ((int) $this->activeId === (int) $convId) {
            $this->activeId = null;
            $this->redirect(route('chat.index'), navigate: false);
        }
    }

    public function render()
    {
        $userId = Auth::id();

        $conversations = Conversation::where('is_archived', false)
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userId)->where('status', 'active')->where('is_hidden', false))
            ->with(['participants.user', 'latestMessage.sender'])
            ->get()
            ->sortByDesc(fn ($c) => $c->latestMessage?->created_at ?? $c->created_at)
            ->values()
            ->map(function ($conversation) use ($userId) {
                $me = $conversation->participants->firstWhere('user_id', $userId);
                $other = $conversation->type === 'direct'
                    ? $conversation->participants->firstWhere('user_id', '!=', $userId)?->user
                    : null;

                $unread = 0;
                if ($conversation->latestMessage) {
                    $unread = $conversation->messages()
                        ->where('id', '>', $me?->last_read_message_id ?? 0)
                        ->where('sender_id', '!=', $userId)
                        ->count();
                }

                return [
                    'id' => $conversation->id,
                    'type' => $conversation->type,
                    'title' => $conversation->type === 'group' ? $conversation->title : $other?->name,
                    'avatar' => $conversation->type === 'group' ? $conversation->avatar : $other?->avatar,
                    'other_user' => $other,
                    'last_message' => $conversation->latestMessage,
                    'unread' => $unread,
                ];
            });

        $searchResults = collect();
        $groupResults = collect();
        if (trim($this->search) !== '') {
            $term = $this->search;
            $searchResults = User::where('id', '!=', $userId)
                ->where('registration_status', 'active')
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                })
                ->orderBy('name')
                ->limit(10)
                ->get();

            $groupResults = Conversation::where('type', 'group')
                ->where('is_archived', false)
                ->where('title', 'like', "%{$term}%")
                ->whereDoesntHave('participants', fn ($q) => $q->where('user_id', $userId)->whereIn('status', ['active', 'pending_approval']))
                ->orderBy('title')
                ->limit(10)
                ->get();
        }

        return view('livewire.chat.inbox', [
            'conversations' => $conversations,
            'searchResults' => $searchResults,
            'groupResults' => $groupResults,
        ]);
    }
}
