<?php

namespace App\Livewire\Gallery;

use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\Statistic;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    private const ITEMS_PER_PAGE = 36;

    public function render()
    {
        $eventFolders = Event::has('galleryImages')
            ->with('galleryImages')
            ->get()
            ->map(fn (Event $event) => (object) [
                'event' => $event,
                'media' => $event->galleryImages,
                'firstItem' => $event->galleryImages->first(),
            ]);

        $common = GalleryImage::whereNull('event_id')->latest()->paginate(self::ITEMS_PER_PAGE);

        $stats = Statistic::syncFromLiveCounts();
        $membersStat = $stats->first(function ($s) {
            $label = strtolower($s->label);
            return str_contains($label, 'member') || str_contains($label, 'professional');
        });

        return view('livewire.gallery.index', [
            'eventFolders' => $eventFolders,
            'common' => $common,
            'membersValue' => $membersStat->value ?? '500+',
        ]);
    }
}
