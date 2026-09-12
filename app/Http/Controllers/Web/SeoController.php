<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Event;
use App\Models\GalleryImage;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('about'), 'priority' => '0.7'],
            ['loc' => route('contact'), 'priority' => '0.6'],
            ['loc' => route('businesses.index'), 'priority' => '0.9'],
            ['loc' => route('events.index'), 'priority' => '0.9'],
            ['loc' => route('gallery.index'), 'priority' => '0.6'],
            ['loc' => route('register'), 'priority' => '0.5'],
        ];

        Business::where('status', 'approved')->get(['id', 'updated_at'])->each(function ($business) use (&$urls) {
            $urls[] = [
                'loc' => route('businesses.show', $business->id),
                'lastmod' => $business->updated_at?->toAtomString(),
                'priority' => '0.8',
            ];
        });

        Event::get(['id', 'updated_at'])->each(function ($event) use (&$urls) {
            $urls[] = [
                'loc' => route('events.show', $event->id),
                'lastmod' => $event->updated_at?->toAtomString(),
                'priority' => '0.8',
            ];
        });

        GalleryImage::whereNotNull('event_id')
            ->select('event_id')
            ->distinct()
            ->pluck('event_id')
            ->each(function ($eventId) use (&$urls) {
                $urls[] = [
                    'loc' => route('gallery.event', $eventId),
                    'priority' => '0.5',
                ];
            });

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /profile',
            'Disallow: /forgot-password',
            '',
            'Sitemap: ' . route('sitemap'),
        ];

        return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
    }
}
