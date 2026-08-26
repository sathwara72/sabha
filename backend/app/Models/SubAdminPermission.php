<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubAdminPermission extends Model
{
    /**
     * Canonical delegable admin modules (key => sidebar label). Settings and
     * Sub-Admin management themselves are deliberately excluded — those stay
     * full-admin-only regardless of any permission row.
     */
    public const MODULES = [
        'businesses' => 'Businesses',
        'events' => 'Events',
        'bookings' => 'Bookings',
        'users' => 'Users',
        'gallery' => 'Gallery',
        'hero-slider' => 'Hero Slider',
        'categories' => 'Categories',
        'locations' => 'Cities & Areas',
        'member-titles' => 'Member Titles',
        'meetings' => '1-to-1 Meetings',
        'referrals' => 'Referrals',
        'testimonials' => 'Testimonials',
        'statistics' => 'Statistics',
        'analytics' => 'Analytics',
    ];

    public const ABILITIES = ['can_view', 'can_add', 'can_edit', 'can_delete', 'can_approve'];

    protected $fillable = [
        'user_id',
        'module',
        'can_view',
        'can_add',
        'can_edit',
        'can_delete',
        'can_approve',
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_add' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
        'can_approve' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
