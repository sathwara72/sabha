<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Statistic;
use App\Models\Trustee;
use Illuminate\View\View;

class PageController extends Controller
{
    private const FALLBACK_TEAM = [
        ['tKey' => 'team_member_1', 'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=150&auto=format&fit=crop'],
        ['tKey' => 'team_member_2', 'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=150&auto=format&fit=crop'],
        ['tKey' => 'team_member_3', 'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=150&auto=format&fit=crop'],
        ['tKey' => 'team_member_4', 'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=150&auto=format&fit=crop'],
    ];

    public function about(): View
    {
        $statData = Statistic::syncFromLiveCounts();

        $members = $statData->first(function ($s) {
            $label = strtolower($s->label);
            return str_contains($label, 'member') || str_contains($label, 'professional');
        });

        $businessExchangedStat = Statistic::realBusinessExchangedFormatted();

        $trustees = Trustee::active()
            ->with('user.business')
            ->get();

        return view('pages.about', [
            'membersStat' => $members->value ?? '500+',
            'businessExchangedStat' => $businessExchangedStat,
            'trustees' => $trustees,
            'fallbackTeam' => self::FALLBACK_TEAM,
        ]);
    }

    public function trustees(): View
    {
        $trustees = Trustee::active()
            ->with('user.business')
            ->get();

        return view('pages.trustees', [
            'trustees' => $trustees,
        ]);
    }
}
