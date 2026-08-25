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

        $stats = Statistic::syncFromLiveCounts();
        if ($stats->isEmpty()) {
            $stats = collect([
                (object) ['label' => 'Active Members', 'value' => '5000+'],
                (object) ['label' => 'Businesses Registered', 'value' => '1200+'],
                (object) ['label' => 'Events Hosted', 'value' => '150+'],
            ]);
        }

        $heroImages = HeroImage::latest()->get()->map(fn ($h) => media_url($h->image_path));
        if ($heroImages->isEmpty()) {
            $heroImages = collect([
                'https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=2000&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1515187029135-18ee286d815b?q=80&w=2000&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?q=80&w=2000&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=2000&auto=format&fit=crop',
            ]);
        }

        return view('pages.home', [
            'events' => $events,
            'stats' => $stats->take(3),
            'heroImages' => $heroImages,
        ]);
    }
}
