<?php

namespace App\Livewire\Admin\HeroSlider;

use App\Models\HeroImage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public $mediaFile = null;

    public bool $isModalOpen = false;

    public bool $success = false;

    public string $uploadError = '';

    public ?int $deleteId = null;

    public function openUploadModal(): void
    {
        $this->mediaFile = null;
        $this->success = false;
        $this->uploadError = '';
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function closeUploadModal(): void
    {
        $this->isModalOpen = false;
        $this->mediaFile = null;
        $this->uploadError = '';
    }

    public function upload(): void
    {
        $this->uploadError = '';
        $this->success = false;

        if (! $this->mediaFile) {
            $this->uploadError = 'Please select an image file to upload.';

            return;
        }

        $this->validate(['mediaFile' => 'image|max:10240']);

        $fileName = time() . '_' . uniqid() . '.' . $this->mediaFile->getClientOriginalExtension();
        $this->mediaFile->storeAs('hero', $fileName, 'public');

        HeroImage::create(['image_path' => '/storage/hero/' . $fileName]);

        $this->success = true;
        $this->mediaFile = null;
        $this->closeUploadModal();
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
        $image = HeroImage::find($this->deleteId);

        if ($image) {
            $path = public_path($image->image_path);
            if (file_exists($path)) {
                @unlink($path);
            }
            $image->delete();
        }

        $this->deleteId = null;
    }

    public function render()
    {
        return view('livewire.admin.hero-slider.index', [
            'heroImages' => HeroImage::latest()->get(),
        ]);
    }
}
