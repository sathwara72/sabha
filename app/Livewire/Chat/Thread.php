<?php

namespace App\Livewire\Chat;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Services\ChatService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Thread extends Component
{
    public int $conversationId;

    public Conversation $conversation;

    public $otherUser = null;

    public string $body = '';

    public string $errorMessage = '';

    public function mount(int $id): void
    {
        $conversation = Conversation::with('participants.user')->findOrFail($id);

        $me = $conversation->participants->firstWhere('user_id', Auth::id());
        abort_unless($me && $me->status === 'active', 403);

        $this->conversationId = $conversation->id;
        $this->conversation = $conversation;
        $this->otherUser = $conversation->participants->firstWhere('user_id', '!=', Auth::id())?->user;

        app(ChatService::class)->markRead($conversation, Auth::user());
    }

    public function send(): void
    {
        $this->errorMessage = '';

        $body = trim($this->body);
        if ($body === '') {
            return;
        }

        app(ChatService::class)->send($this->conversation, Auth::user(), $body);
        app(ChatService::class)->markRead($this->conversation, Auth::user());

        $this->body = '';
    }

    public function saveEdit(int $messageId, string $newBody): void
    {
        $this->errorMessage = '';

        $message = ChatMessage::where('conversation_id', $this->conversationId)->findOrFail($messageId);

        try {
            app(ChatService::class)->edit($message, Auth::user(), $newBody);
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function deleteMessage(int $messageId): void
    {
        $this->errorMessage = '';

        $message = ChatMessage::where('conversation_id', $this->conversationId)->findOrFail($messageId);

        try {
            app(ChatService::class)->delete($message, Auth::user());
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function pollSync(): void
    {
        $this->dispatch('chat-messages-synced', messages: $this->recentMessagesPayload());
    }

    private function recentMessagesPayload(): array
    {
        $userId = Auth::id();

        return ChatMessage::where('conversation_id', $this->conversationId)
            ->with('sender')
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (ChatMessage $m) => [
                'id' => $m->id,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender->name,
                'sender_avatar' => media_url($m->sender->avatar),
                'body' => $m->is_deleted ? null : $m->body,
                'body_html' => $m->is_deleted ? null : linkify_text($m->body),
                'is_mine' => $m->sender_id === $userId,
                'is_edited' => $m->is_edited,
                'is_deleted' => $m->is_deleted,
                'editable' => $m->editableBy(Auth::user()),
                'deletable' => $m->deletableBy(Auth::user()),
                'created_at_human' => $m->created_at->format('g:i A'),
            ])
            ->all();
    }

    public function render()
    {
        return view('livewire.chat.thread', [
            'initialMessages' => $this->recentMessagesPayload(),
        ]);
    }
}
