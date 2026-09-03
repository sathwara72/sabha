<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = [
        'type',
        'title',
        'description',
        'avatar',
        'creator_id',
        'join_setting',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    public function activeParticipants(): HasMany
    {
        return $this->participants()->where('status', 'active');
    }

    public function pendingParticipants(): HasMany
    {
        return $this->participants()->where('status', 'pending_approval');
    }

    public function mainAdmin(): ?ConversationParticipant
    {
        return $this->participants()->where('role', 'main_admin')->first();
    }

    /**
     * Always queries fresh rather than reading the (possibly stale, if this
     * Conversation instance is reused across several governance calls)
     * cached participants collection — role/status just changed underfoot
     * is exactly the case this needs to get right.
     */
    public function participantRole(User $user): ?string
    {
        return $this->participants()->where('user_id', $user->id)->first()?->role;
    }

    /**
     * Group governance actions (rename, add/remove members, delete group,
     * moderate messages) are gated on this — main_admin and admin both
     * qualify, regular members don't.
     */
    public function isGroupAdmin(User $user): bool
    {
        $me = $this->participants()->where('user_id', $user->id)->first();

        return $me
            && $me->status === 'active'
            && in_array($me->role, ['main_admin', 'admin'], true);
    }

    public function isMainAdmin(User $user): bool
    {
        $me = $this->participants()->where('user_id', $user->id)->first();

        return $me && $me->status === 'active' && $me->role === 'main_admin';
    }
}
