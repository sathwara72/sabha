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
     * Refresh member/event-count-derived stat values from live counts and
     * drop any stale city/mixer stats. Same behavior as the JSON API's
     * getStatistics() endpoint, kept here since Blade controllers query
     * Eloquent directly instead of hitting /api/statistics.
     */
    public static function syncFromLiveCounts(): \Illuminate\Support\Collection
    {
        try {
            $userCount = User::count();
            if ($userCount > 0) {
                static::where('label', 'like', '%Professional%')
                    ->orWhere('label', 'like', '%Member%')
                    ->update(['value' => $userCount . '+']);
            }

            $eventCount = Event::count();
            if ($eventCount > 0) {
                static::where('label', 'like', '%Event%')->update(['value' => $eventCount . '+']);
            }

            static::where('label', 'like', '%Cit%')->delete();
            static::where('label', 'like', '%Mixer%')->delete();
        } catch (\Exception $e) {
            Log::error('Failed to dynamically update statistics: ' . $e->getMessage());
        }

        return static::all();
    }
}
