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
        'booking_start_date',
        'booking_end_date',
        'location',
        'google_map_link',
        'map_iframe',
        'type',
        'image',
        'youtube_urls',
        'is_popup',
        'price_normal',
        'price_verified',
        'event_code',
        'agenda',
        'speakers',
    ];

    protected $casts = [
        'date' => 'date',
        'booking_start_date' => 'date',
        'booking_end_date' => 'date',
        'agenda' => 'array',
        'speakers' => 'array',
        'youtube_urls' => 'array',
        'is_popup' => 'boolean',
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

    /**
     * Check whether booking/ticket reservation is currently open.
     */
    public function isBookingOpen(): bool
    {
        $today = today();

        // If event date has passed
        if ($this->date && $this->date->copy()->endOfDay()->isPast()) {
            return false;
        }

        // If booking start date is in the future
        if ($this->booking_start_date && $today->lt($this->booking_start_date->copy()->startOfDay())) {
            return false;
        }

        // If booking end date has passed
        if ($this->booking_end_date && $today->gt($this->booking_end_date->copy()->endOfDay())) {
            return false;
        }

        return true;
    }

    /**
     * Get booking status code: 'open' | 'upcoming' | 'closed' | 'past'
     */
    public function bookingStatus(): string
    {
        $today = today();

        if ($this->date && $this->date->copy()->endOfDay()->isPast()) {
            return 'past';
        }

        if ($this->booking_start_date && $today->lt($this->booking_start_date->copy()->startOfDay())) {
            return 'upcoming';
        }

        if ($this->booking_end_date && $today->gt($this->booking_end_date->copy()->endOfDay())) {
            return 'closed';
        }

        return 'open';
    }

    /**
     * Formatted booking window string (e.g. "Sep 1, 2026 - Sep 7, 2026")
     */
    public function bookingWindowFormatted(): string
    {
        if ($this->booking_start_date && $this->booking_end_date) {
            return $this->booking_start_date->format('M j, Y') . ' to ' . $this->booking_end_date->format('M j, Y');
        }

        if ($this->booking_start_date) {
            return 'Opens ' . $this->booking_start_date->format('M j, Y');
        }

        if ($this->booking_end_date) {
            return 'Closes ' . $this->booking_end_date->format('M j, Y');
        }

        return 'Open until event date';
    }
}
