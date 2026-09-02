<?php

namespace App\Livewire\Admin\Chat;

use App\Events\ChatMessageDeleted;
use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Show extends Component
{
    public int $conversationId;

    public function mount(int $id): void
    {
        $this->conversationId = $id;
    }

    public function toggleArchive(): void
    {
        admin_authorize('chat', 'can_edit');

        $conversation = Conversation::findOrFail($this->conversationId);
        $conversation->update(['is_archived' => ! $conversation->is_archived]);
    }

    public function deleteMessage(int $messageId): void
    {
        admin_authorize('chat', 'can_delete');

        $message = ChatMessage::where('conversation_id', $this->conversationId)->findOrFail($messageId);

        if ($message->is_deleted) {
            return;
        }

        $message->update(['is_deleted' => true, 'deleted_at' => now()]);

        try {
            broadcast(new ChatMessageDeleted($message));
        } catch (\Throwable $e) {
            Log::error('Admin moderation broadcast failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $conversation = Conversation::with(['participants.user', 'creator'])->findOrFail($this->conversationId);

        $messages = ChatMessage::where('conversation_id', $this->conversationId)
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        return view('livewire.admin.chat.show', [
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }
}
