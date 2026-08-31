<?php

namespace App\Services;

use App\Events\ChatMessageDeleted;
use App\Events\ChatMessageSent;
use App\Events\ChatMessageUpdated;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Single place for conversation/message mutations so the 1-hour edit/delete
 * window and participant checks can't drift between the callers that need
 * them (Thread, Inbox, and — once group chat lands — its own components).
 */
class ChatService
{
    public function findOrCreateDirect(User $a, User $b): Conversation
    {
        $existing = Conversation::where('type', 'direct')
            ->whereHas('participants', fn ($q) => $q->where('user_id', $a->id))
            ->whereHas('participants', fn ($q) => $q->where('user_id', $b->id))
            ->first();

        if ($existing) {
            // Re-opening a direct chat un-hides it for the user who had
            // hidden/cleared it, matching how the other party still sees it.
            ConversationParticipant::where('conversation_id', $existing->id)
                ->whereIn('user_id', [$a->id, $b->id])
                ->update(['is_hidden' => false]);

            return $existing;
        }

        $conversation = Conversation::create([
            'type' => 'direct',
            'creator_id' => $a->id,
        ]);

        foreach ([$a->id, $b->id] as $userId) {
            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $userId,
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now(),
            ]);
        }

        return $conversation;
    }

    public function send(Conversation $conversation, User $sender, string $body): ChatMessage
    {
        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'message_type' => 'text',
            'body' => trim($body),
        ]);

        $message->setRelation('sender', $sender);

        $this->broadcastSafely(new ChatMessageSent($message));

        return $message;
    }

    public function edit(ChatMessage $message, User $user, string $newBody): void
    {
        if (! $message->editableBy($user)) {
            throw new \RuntimeException('This message can no longer be edited.');
        }

        $message->update([
            'body' => trim($newBody),
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        $this->broadcastSafely(new ChatMessageUpdated($message));
    }

    public function delete(ChatMessage $message, User $user): void
    {
        if (! $message->deletableBy($user) && ! $message->deletableByModerator($user)) {
            throw new \RuntimeException('This message can no longer be deleted.');
        }

        $message->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);

        $this->broadcastSafely(new ChatMessageDeleted($message));
    }

    public function markRead(Conversation $conversation, User $user): void
    {
        $latestId = $conversation->messages()->max('id');

        if (! $latestId) {
            return;
        }

        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->update(['last_read_message_id' => $latestId]);
    }

    public function hideForUser(Conversation $conversation, User $user): void
    {
        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->update(['is_hidden' => true]);
    }

    /**
     * A Reverb hiccup should never fail the actual send/edit/delete — the
     * message is already persisted and will show up on next load/poll even
     * if the live push fails.
     */
    private function broadcastSafely(object $event): void
    {
        try {
            broadcast($event);
        } catch (\Throwable $e) {
            Log::error('Chat broadcast failed: ' . $e->getMessage());
        }
    }
}
