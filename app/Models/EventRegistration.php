<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'purchased_by_user_id',
        'guest_name',
        'guest_mobile',
        'guest_email',
        'ticket_number',
        'status',
        'payment_screenshot',
        'ticket_type',
        'amount_paid',
        'rejection_reason',
        'is_attended',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'is_attended' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The member who bought this pass on behalf of a visitor (null for a
     * member's own registration).
     */
    public function purchasedBy()
    {
        return $this->belongsTo(User::class, 'purchased_by_user_id');
    }

    /**
     * Attendee display name — the visitor's name for a guest pass, the
     * registrant's own name otherwise.
     */
    public function attendeeName(): ?string
    {
        return $this->guest_name ?: $this->user?->name;
    }

    /**
     * Attendee email — where the ticket gets sent.
     */
    public function attendeeEmail(): ?string
    {
        return $this->guest_email ?: $this->user?->email;
    }
}
