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
    ];

    protected $casts = [
        'date' => 'datetime',
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
