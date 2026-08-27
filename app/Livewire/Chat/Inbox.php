<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\User;
use App\Services\ChatService;
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

    public function render()
    {
        $userId = Auth::id();

        $conversations = Conversation::where('type', 'direct')
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userId)->where('is_hidden', false))
            ->with(['participants.user', 'latestMessage'])
            ->get()
            ->sortByDesc(fn ($c) => $c->latestMessage?->created_at ?? $c->created_at)
            ->values()
            ->map(function ($conversation) use ($userId) {
                $me = $conversation->participants->firstWhere('user_id', $userId);
                $other = $conversation->participants->firstWhere('user_id', '!=', $userId);

                $unread = 0;
                if ($conversation->latestMessage) {
                    $unread = $conversation->messages()
                        ->where('id', '>', $me?->last_read_message_id ?? 0)
                        ->where('sender_id', '!=', $userId)
                        ->count();
                }

                return [
                    'id' => $conversation->id,
                    'other_user' => $other?->user,
                    'last_message' => $conversation->latestMessage,
                    'unread' => $unread,
                ];
            });

        $searchResults = collect();
        if (trim($this->search) !== '') {
            $term = $this->search;
            $searchResults = User::where('id', '!=', $userId)
                ->where('registration_status', 'active')
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                })
                ->orderBy('name')
                ->limit(15)
                ->get();
        }

        return view('livewire.chat.inbox', [
            'conversations' => $conversations,
            'searchResults' => $searchResults,
        ]);
    }
}
