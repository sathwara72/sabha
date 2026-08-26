<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OneToOneMeeting extends Model
{
    protected $fillable = [
        'user_id',
        'with_member_id',
        'meeting_at',
        'location',
        'points_of_discussion',
        'image',
        'comments',
    ];

    protected $casts = [
        'meeting_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function withMember(): BelongsTo
    {
        return $this->belongsTo(User::class, 'with_member_id');
    }
}
