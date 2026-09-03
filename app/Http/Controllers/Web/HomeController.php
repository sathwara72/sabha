<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\HeroImage;
use App\Models\Statistic;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $events = Event::orderBy('date', 'desc')->take(3)->get();

        $popupEvent = Event::where('is_popup', true)->first();

        $stats = Statistic::syncFromLiveCounts();
        if ($stats->isEmpty()) {
            $stats = collect([
                (object) ['label' => 'Active Members', 'value' => '5000+'],
                (object) ['label' => 'Businesses Registered', 'value' => '1200+'],
                (object) ['label' => 'Events Hosted', 'value' => '150+'],
            ]);
        }

        $heroImages = HeroImage::with('event')->orderBy('sort_order')->get()->map(function ($h) {
            $link = null;
            $external = false;

            if ($h->link_type === 'event' && $h->event_id) {
                $link = route('events.show', $h->event_id);
            } elseif ($h->link_type === 'external' && ! empty($h->external_url)) {
                $rawUrl = trim($h->external_url);
                if (str_starts_with($rawUrl, '/')) {
                    $link = $rawUrl;
                    $external = false;
                } elseif (str_starts_with(strtolower($rawUrl), 'http://') || str_starts_with(strtolower($rawUrl), 'https://')) {
                    $link = $rawUrl;
                    $external = true;
                } else {
                    $link = 'https://' . $rawUrl;
                    $external = true;
                }
            } elseif (! empty($h->external_url)) {
                $rawUrl = trim($h->external_url);
                $link = (str_starts_with(strtolower($rawUrl), 'http://') || str_starts_with(strtolower($rawUrl), 'https://')) ? $rawUrl : 'https://' . $rawUrl;
                $external = true;
            } elseif (! empty($h->event_id)) {
                $link = route('events.show', $h->event_id);
            }

            return [
                'title' => $h->title ?? '',
                'caption' => $h->caption ?? '',
                'url' => media_url($h->image_path),
                'link' => $link,
                'external' => $external,
                'is_default' => false,
            ];
        });
        if ($heroImages->isEmpty()) {
            $heroImages = collect([
                [
                    'title' => 'SABHA',
                    'caption' => 'Business Network',
                    'url' => asset('logo.png'),
                    'link' => null,
                    'external' => false,
                    'is_default' => true,
                ],
            ]);
        }

        return view('pages.home', [
            'events' => $events,
            'stats' => $stats->take(3),
            'heroImages' => $heroImages,
            'popupEvent' => $popupEvent,
        ]);
    }
}
