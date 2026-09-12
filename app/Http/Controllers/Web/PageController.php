<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Statistic;
use App\Models\Trustee;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $statData = Statistic::syncFromLiveCounts();

        $members = $statData->first(function ($s) {
            $label = strtolower($s->label);
            return str_contains($label, 'member') || str_contains($label, 'professional');
        });

        $businessExchangedStat = Statistic::realBusinessExchangedFormatted();

        return view('pages.about', [
            'membersStat' => $members->value ?? '500+',
            'businessExchangedStat' => $businessExchangedStat,
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
