<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Statistic extends Model
{
    protected $fillable = [
        'label',
        'value',
    ];

    /**
     * Returns statistics saved by admin without overwriting custom numbers.
     */
    public static function syncFromLiveCounts(): \Illuminate\Support\Collection
    {
        $stats = static::orderBy('id')->get();

        if ($stats->isEmpty()) {
            $userCount = User::count();
            $eventCount = Event::count();

            static::create(['label' => 'Active Members', 'value' => ($userCount > 0 ? $userCount . '+' : '500+')]);
            static::create(['label' => 'Businesses Registered', 'value' => '1200+']);
            static::create(['label' => 'Events Hosted', 'value' => ($eventCount > 0 ? $eventCount . '+' : '50+')]);

            $stats = static::orderBy('id')->get();
        }

        return $stats;
    }
}
