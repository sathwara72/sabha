<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        return view('pages.gallery');
    }

    public function event(int $id): View
    {
        $event = Event::with('galleryImages')->find($id);

        return view('pages.gallery-event', [
            'event' => $event,
            'photos' => $event?->galleryImages ?? collect(),
        ]);
    }
}
