<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message_type',
        'body',
        'is_edited',
        'edited_at',
        'is_deleted',
        'deleted_at',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'is_deleted' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Senders can edit/delete their own text messages within 1 hour of
     * sending — the single source of truth for that rule, checked both by
     * ChatService (server enforcement) and the message bubble UI (hiding
     * the actions once expired).
     */
    public function isWithinEditWindow(): bool
    {
        return $this->created_at->gt(now()->subHour());
    }

    public function editableBy(User $user): bool
    {
        return $this->sender_id === $user->id
            && $this->message_type === 'text'
            && ! $this->is_deleted
            && $this->isWithinEditWindow();
    }

    public function deletableBy(User $user): bool
    {
        return $this->sender_id === $user->id
            && $this->message_type === 'text'
            && ! $this->is_deleted
            && $this->isWithinEditWindow();
    }
}
