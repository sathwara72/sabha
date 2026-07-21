<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        'user_id',
        'business_category_id',
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

    protected $appends = ['rating', 'reviews_count'];

    public function getRatingAttribute()
    {
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            return $this->attributes['reviews_avg_rating'] !== null 
                ? round((float) $this->attributes['reviews_avg_rating'], 1) 
                : null;
        }
        $avg = $this->reviews()->avg('rating');
        return $avg !== null ? round((float) $avg, 1) : null;
    }

    public function getReviewsCountAttribute()
    {
        if (array_key_exists('reviews_count', $this->attributes)) {
            return (int) $this->attributes['reviews_count'];
        }
        return $this->reviews()->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businessCategory()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
