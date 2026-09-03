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
     * Returns real dynamic statistics computed directly from database records.
     */
    public static function syncFromLiveCounts(): \Illuminate\Support\Collection
    {
        $activeMembers = User::where('registration_status', 'active')->count() ?: User::count();
        $approvedBusinesses = Business::where('status', 'approved')->count() ?: Business::count();
        $eventsHosted = Event::count();

        static::updateOrCreate(
            ['id' => 1],
            ['label' => 'Active Members', 'value' => $activeMembers . '+']
        );
        static::updateOrCreate(
            ['id' => 2],
            ['label' => 'Businesses Registered', 'value' => $approvedBusinesses . '+']
        );
        static::updateOrCreate(
            ['id' => 3],
            ['label' => 'Events Hosted', 'value' => $eventsHosted . '+']
        );

        return static::orderBy('id')->get();
    }

    /**
     * Returns real monetary business exchanged amount from closed business referrals.
     */
    public static function realBusinessExchangedFormatted(): string
    {
        $referralTotal = (float) BusinessReferral::where('status', 'closed')->sum('amount');
        if ($referralTotal >= 10000000) {
            return '₹' . round($referralTotal / 10000000, 1) . 'Cr+';
        }
        if ($referralTotal >= 100000) {
            return '₹' . round($referralTotal / 100000, 1) . 'L+';
        }
        if ($referralTotal > 0) {
            return '₹' . number_format($referralTotal) . '+';
        }

        return '₹0+';
    }
}
