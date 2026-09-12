<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessReferral extends Model
{
    protected $fillable = [
        'giver_id',
        'receiver_id',
        'contact_name',
        'contact_number',
        'company_details',
        'business_requirement',
        'lead_rating',
        'giver_comments',
        'contact_status',
        'status',
        'amount',
        'receiver_comments',
        'testimonial',
        'display_testimonial',
    ];

    protected $casts = [
        'display_testimonial' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function giver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'giver_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
