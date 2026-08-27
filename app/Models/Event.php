<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'google_map_link',
        'type',
        'image',
        'price_normal',
        'price_verified',
        'event_code',
        'agenda',
        'speakers',
    ];

    protected $casts = [
        'date' => 'datetime',
        'agenda' => 'array',
        'speakers' => 'array',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Approved attendees only (used for the public attendee list).
     */
    public function approvedRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class)->where('status', 'approved');
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(GalleryImage::class);
    }
}
