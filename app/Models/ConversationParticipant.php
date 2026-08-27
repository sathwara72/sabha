<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationParticipant extends Model
{
    protected $fillable = [
        'conversation_id',
        'user_id',
        'role',
        'status',
        'invited_by',
        'last_read_message_id',
        'is_hidden',
        'is_muted',
        'joined_at',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
        'is_muted' => 'boolean',
        'joined_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
