<?php

namespace App\Livewire\Admin\Gallery;

use App\Models\GalleryImage;
use App\Services\GalleryMediaUploader;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public array $mediaFiles = [];

    public bool $isModalOpen = false;

    public string $uploadError = '';

    public bool $uploading = false;

    public ?int $deleteId = null;

    public function openUploadModal(): void
    {
        $this->mediaFiles = [];
        $this->uploadError = '';
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function closeUploadModal(): void
    {
        $this->isModalOpen = false;
        $this->mediaFiles = [];
        $this->uploadError = '';
    }

    public function upload(GalleryMediaUploader $uploader): void
    {
        $this->uploadError = '';

        if (empty($this->mediaFiles)) {
            $this->uploadError = 'Please select image(s), video(s), or ZIP file to upload.';

            return;
        }

        $this->uploading = true;

        $created = $uploader->upload($this->mediaFiles, null);

        if (empty($created)) {
            $this->uploadError = 'No valid media files were uploaded.';
            $this->uploading = false;

            return;
        }

        $this->uploading = false;
        $this->closeUploadModal();
        $this->resetPage();
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
        $image = GalleryImage::find($this->deleteId);

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
        $gallery = GalleryImage::whereNull('event_id')->latest()->paginate(12);

        return view('livewire.admin.gallery.index', [
            'gallery' => $gallery,
            'galleryTotal' => GalleryImage::whereNull('event_id')->count(),
        ]);
    }
}
