<?php

namespace App\Livewire\Admin\HeroSlider;

use App\Models\Event;
use App\Models\HeroImage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public ?int $deleteId = null;

    // Modal State (Create / Edit)
    public bool $isFormModalOpen = false;

    public ?int $editingId = null;

    public $mediaFile = null;

    public string $imagePreviewUrl = '';

    public string $title = '';

    public string $caption = '';

    public string $linkType = 'none';

    public ?int $eventId = null;

    public string $externalUrl = '';

    public string $formError = '';

    public string $successMsg = '';

    public function openCreateModal(): void
    {
        admin_authorize('hero-slider', 'can_add');
        $this->resetForm();
        $this->isFormModalOpen = true;
    }

    public function openEditModal(int $id): void
    {
        admin_authorize('hero-slider', 'can_edit');
        $this->resetForm();

        $hero = HeroImage::findOrFail($id);
        $this->editingId = $hero->id;
        $this->imagePreviewUrl = media_url($hero->image_path) ?? '';
        $this->title = $hero->title ?? '';
        $this->caption = $hero->caption ?? '';
        $this->linkType = $hero->link_type ?? 'none';
        $this->eventId = $hero->event_id;
        $this->externalUrl = $hero->external_url ?? '';

        $this->isFormModalOpen = true;
    }

    public function closeFormModal(): void
    {
        $this->resetForm();
        $this->isFormModalOpen = false;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->mediaFile = null;
        $this->imagePreviewUrl = '';
        $this->title = '';
        $this->caption = '';
        $this->linkType = 'none';
        $this->eventId = null;
        $this->externalUrl = '';
        $this->formError = '';
        $this->resetErrorBag();
    }

    public function saveSlide(): void
    {
        admin_authorize('hero-slider', $this->editingId ? 'can_edit' : 'can_add');

        $this->formError = '';

        $validated = $this->validate([
            'mediaFile' => ($this->editingId ? 'nullable' : 'required') . '|image|max:10240',
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

        if ($this->editingId) {
            HeroImage::findOrFail($this->editingId)->update($data);
            $this->successMsg = 'Hero slide updated successfully.';
        } else {
            $maxOrder = HeroImage::max('sort_order') ?? -1;
            $data['sort_order'] = $maxOrder + 1;
            HeroImage::create($data);
            $this->successMsg = 'New hero slide added successfully.';
        }

        $this->closeFormModal();
    }

    public function moveUp(int $id): void
    {
        admin_authorize('hero-slider', 'can_edit');
        $this->swap($id, 'up');
    }

    public function moveDown(int $id): void
    {
        admin_authorize('hero-slider', 'can_edit');
        $this->swap($id, 'down');
    }

    private function swap(int $id, string $direction): void
    {
        $images = HeroImage::orderBy('sort_order')->get(['id', 'sort_order']);
        $index = $images->search(fn ($img) => $img->id === $id);

        if ($index === false) {
            return;
        }

        $targetIndex = $direction === 'up' ? $index - 1 : $index + 1;

        if ($targetIndex < 0 || $targetIndex >= $images->count()) {
            return;
        }

        $current = $images[$index];
        $target = $images[$targetIndex];

        DB::transaction(function () use ($current, $target) {
            HeroImage::where('id', $current->id)->update(['sort_order' => $target->sort_order]);
            HeroImage::where('id', $target->id)->update(['sort_order' => $current->sort_order]);
        });
    }

    public function openDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
    }

    public function confirmDelete(): void
    {
        admin_authorize('hero-slider', 'can_delete');

        $image = HeroImage::find($this->deleteId);

        if ($image) {
            $path = public_path($image->image_path);
            if (file_exists($path)) {
                @unlink($path);
            }
            $image->delete();
            $this->successMsg = 'Hero slider image deleted successfully.';
        }

        $this->deleteId = null;
    }

    public function render()
    {
        return view('livewire.admin.hero-slider.index', [
            'heroImages' => HeroImage::with('event')->orderBy('sort_order')->get(),
            'events' => Event::orderByDesc('date')->get(),
        ]);
    }
}
