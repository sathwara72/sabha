<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'tagline',
        'location',
        'description',
        'logo',
        'cover_image',
        'website',
        'phone',
        'email',
        'linkedin',
        'instagram',
        'youtube',
        'twitter',
        'whatsapp',
        'hours',
        'founded',
        'team_size',
        'projects',
        'services',
        'status',
        'is_verified',
        'payment_screenshot',
        'rejection_reason',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'services' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
