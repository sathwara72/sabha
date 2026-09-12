<?php

namespace App\Livewire\Admin\HeroSlider;

use App\Models\Event;
use App\Models\HeroImage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public ?int $heroImageId = null;

    public $mediaFile = null;

    public string $imagePreviewUrl = '';

    public string $title = '';

    public string $caption = '';

    public string $linkType = 'none';

    public ?int $eventId = null;

    public string $externalUrl = '';

    public string $uploadError = '';

    public function mount(?int $id = null): void
    {
        if (! $id) {
            return;
        }

        $hero = HeroImage::findOrFail($id);
        $this->heroImageId = $hero->id;
        $this->imagePreviewUrl = media_url($hero->image_path) ?? '';
        $this->title = $hero->title ?? '';
        $this->caption = $hero->caption ?? '';
        $this->linkType = $hero->link_type ?? 'none';
        $this->eventId = $hero->event_id;
        $this->externalUrl = $hero->external_url ?? '';
    }

    public function save()
    {
        admin_authorize('hero-slider', $this->heroImageId ? 'can_edit' : 'can_add');

        $this->uploadError = '';

        $validated = $this->validate([
            'mediaFile' => ($this->heroImageId ? 'nullable' : 'required') . '|image|max:10240',
            'title' => 'nullable|string|max:150',
            'caption' => 'nullable|string|max:255',
            'linkType' => 'required|in:none,event,external',
            'eventId' => 'required_if:linkType,event|nullable|integer|exists:events,id',
            'externalUrl' => 'required_if:linkType,external|nullable|url',
        ]);

        $data = [
            'title' => $validated['title'] ?: null,
            'caption' => $validated['caption'] ?: null,
            'link_type' => $validated['linkType'] === 'none' ? null : $validated['linkType'],
            'event_id' => $validated['linkType'] === 'event' ? $validated['eventId'] : null,
            'external_url' => $validated['linkType'] === 'external' ? $validated['externalUrl'] : null,
        ];

        if ($this->mediaFile) {
            $fileName = time() . '_' . uniqid() . '.' . $this->mediaFile->getClientOriginalExtension();
            $this->mediaFile->storeAs('hero', $fileName, 'public');
            $data['image_path'] = '/storage/hero/' . $fileName;
        }

        if ($this->heroImageId) {
            HeroImage::findOrFail($this->heroImageId)->update($data);
        } else {
            $maxOrder = HeroImage::max('sort_order') ?? -1;
            $data['sort_order'] = $maxOrder + 1;
            HeroImage::create($data);
        }

        return $this->redirect(route('admin.hero-slider.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.hero-slider.form', [
            'events' => Event::orderByDesc('date')->get(),
        ]);
    }
}
