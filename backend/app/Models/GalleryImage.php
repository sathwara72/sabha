<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryImage extends Model
{
    protected $fillable = [
        'event_id',
        'image_path',
        'caption',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
