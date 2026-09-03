<?php

namespace App\Livewire\Admin\Events;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public ?int $eventId = null;

    public string $title = '';

    public string $description = '';

    public string $date = '';

    public ?string $booking_start_date = '';

    public ?string $booking_end_date = '';

    public string $location = '';

    public string $google_map_link = '';

    public string $map_iframe = '';

    public string $type = 'Mixer';

    public string $price_normal = '';

    public string $price_verified = '';

    public array $youtube_urls = [];

    public array $agenda = [];

    public array $speakers = [];

    public $imageFile = null;

    public string $imagePreviewUrl = '';

    public function mount(?int $id = null): void
    {
        if (! $id) {
            return;
        }

        $event = Event::findOrFail($id);

        $this->eventId = $event->id;
        $this->title = $event->title;
        $this->description = $event->description;
        $this->date = $event->date ? $event->date->format('Y-m-d') : '';
        $this->booking_start_date = $event->booking_start_date ? $event->booking_start_date->format('Y-m-d') : '';
        $this->booking_end_date = $event->booking_end_date ? $event->booking_end_date->format('Y-m-d') : '';
        $this->location = $event->location;
        $this->google_map_link = $event->google_map_link ?? '';
        $this->map_iframe = $event->map_iframe ?: ($event->google_map_link ?? '');
        $this->type = $event->type ?: 'Mixer';
        $this->price_normal = $event->price_normal ?: '';
        $this->price_verified = $event->price_verified ?: '';
        $this->youtube_urls = $event->youtube_urls ?? [];
        $this->agenda = $event->agenda ?? [];
        $this->speakers = $event->speakers ?? [];
        $this->imagePreviewUrl = $event->image ? (media_url($event->image) ?? '') : '';
    }

    public function addAgendaItem(): void
    {
        $this->agenda[] = '';
    }

    public function removeAgendaItem(int $index): void
    {
        unset($this->agenda[$index]);
        $this->agenda = array_values($this->agenda);
    }

    public function addYoutubeUrl(): void
    {
        $this->youtube_urls[] = '';
    }

    public function removeYoutubeUrl(int $index): void
    {
        unset($this->youtube_urls[$index]);
        $this->youtube_urls = array_values($this->youtube_urls);
    }

    public function addSpeaker(): void
    {
        $this->speakers[] = ['name' => '', 'role' => '', 'bio' => ''];
    }

    public function removeSpeaker(int $index): void
    {
        unset($this->speakers[$index]);
        $this->speakers = array_values($this->speakers);
    }

    public function removeImage(): void
    {
        $this->imageFile = null;
        $this->imagePreviewUrl = '';
    }

    public function save()
    {
        admin_authorize('events', $this->eventId ? 'can_edit' : 'can_add');

        $validated = $this->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'booking_start_date' => 'nullable|date',
            'booking_end_date' => 'nullable|date|after_or_equal:booking_start_date',
            'location' => 'required|string',
            'google_map_link' => 'nullable|string',
            'map_iframe' => 'nullable|string',
            'type' => 'required|string',
            'price_normal' => 'required|string',
            'price_verified' => 'required|string',
            'youtube_urls' => 'array',
            'youtube_urls.*' => 'nullable|url',
            'imageFile' => 'nullable|image|max:10240',
        ]);

        $agenda = array_values(array_filter($this->agenda, fn ($item) => trim((string) $item) !== ''));
        $speakers = array_values(array_filter($this->speakers, fn ($s) => trim((string) ($s['name'] ?? '')) !== ''));
        $youtubeUrls = array_values(array_filter($this->youtube_urls, fn ($url) => trim((string) $url) !== ''));

        $mapIframeInput = trim((string) ($validated['map_iframe'] ?? ''));
        $mapLinkInput = trim((string) ($validated['google_map_link'] ?? ''));

        // If google_map_link wasn't explicitly given but map_iframe has a URL or iframe
        if ($mapLinkInput === '' && $mapIframeInput !== '') {
            if (preg_match('#^https?://#i', $mapIframeInput)) {
                $mapLinkInput = $mapIframeInput;
            } elseif (preg_match('/src=["\']([^"\']+)["\']/i', $mapIframeInput, $matches)) {
                $mapLinkInput = $matches[1];
            }
        }
        if ($mapLinkInput === '' && ! empty($validated['location'])) {
            $mapLinkInput = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($validated['location']);
        }

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'booking_start_date' => ! empty($validated['booking_start_date']) ? $validated['booking_start_date'] : null,
            'booking_end_date' => ! empty($validated['booking_end_date']) ? $validated['booking_end_date'] : null,
            'location' => $validated['location'],
            'google_map_link' => $mapLinkInput ?: null,
            'map_iframe' => $mapIframeInput ?: null,
            'type' => $validated['type'],
            'price_normal' => $validated['price_normal'],
            'price_verified' => $validated['price_verified'],
            'youtube_urls' => $youtubeUrls,
            'agenda' => $agenda,
            'speakers' => $speakers,
        ];

        if ($this->imageFile) {
            $fileName = time() . '_event_' . uniqid() . '.' . $this->imageFile->getClientOriginalExtension();
            $this->imageFile->storeAs('events', $fileName, 'public');
            $data['image'] = '/storage/events/' . $fileName;
        }

        if ($this->eventId) {
            Event::findOrFail($this->eventId)->update($data);
        } else {
            $data['event_code'] = $this->generateEventCode($data['title']);
            Event::create($data);
        }

        return $this->redirect(route('admin.events.index'), navigate: false);
    }

    private function generateEventCode(string $title): string
    {
        $cleanTitle = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
        $words = array_filter(explode(' ', trim($cleanTitle)));
        $code = '';

        if (count($words) >= 2) {
            foreach ($words as $word) {
                $code .= strtoupper(substr($word, 0, 1));
            }
        } else {
            $code = strtoupper(substr($cleanTitle, 0, 4));
        }

        $code = preg_replace('/[^A-Z0-9]/', '', $code);
        if (strlen($code) < 3) {
            $code .= mt_rand(100, 999);
        }

        $eventCode = substr($code, 0, 6);
        $originalCode = $eventCode;
        $counter = 1;

        while (Event::where('event_code', $eventCode)->exists()) {
            $eventCode = substr($originalCode, 0, 4) . $counter;
            $counter++;
        }

        return $eventCode;
    }

    public function render()
    {
        return view('livewire.admin.events.form');
    }
}
