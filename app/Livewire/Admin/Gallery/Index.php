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

    public ?int $deleteId = null;

    // Add Gallery Modal State
    public bool $isAddModalOpen = false;

    public array $mediaFiles = [];

    public string $uploadError = '';

    public bool $uploading = false;

    public string $successMsg = '';

    public function openAddModal(): void
    {
        admin_authorize('gallery', 'can_add');
        $this->isAddModalOpen = true;
        $this->mediaFiles = [];
        $this->uploadError = '';
        $this->uploading = false;
    }

    public function closeAddModal(): void
    {
        $this->isAddModalOpen = false;
        $this->mediaFiles = [];
        $this->uploadError = '';
        $this->uploading = false;
    }

    public function removeMediaFile(int $index): void
    {
        unset($this->mediaFiles[$index]);
        $this->mediaFiles = array_values($this->mediaFiles);
    }

    public function uploadMedia(GalleryMediaUploader $uploader): void
    {
        admin_authorize('gallery', 'can_add');

        $this->uploadError = '';

        if (empty($this->mediaFiles)) {
            $this->uploadError = 'Please select image(s), video(s), or ZIP file to upload.';
            return;
        }

        $this->uploading = true;

        try {
            $created = $uploader->upload($this->mediaFiles, null);

            if (empty($created)) {
                $this->uploadError = 'No valid media files were found to upload.';
                $this->uploading = false;
                return;
            }

            $count = count($created);
            $this->successMsg = "{$count} media item(s) uploaded to gallery successfully.";
            $this->closeAddModal();
            $this->resetPage();
        } catch (\Throwable $e) {
            $this->uploadError = 'Failed to process upload: ' . $e->getMessage();
            $this->uploading = false;
        }
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
        admin_authorize('gallery', 'can_delete');

        $image = GalleryImage::find($this->deleteId);

        if ($image) {
            $path = public_path($image->image_path);
            if (file_exists($path)) {
                @unlink($path);
            }
            $image->delete();
            $this->successMsg = 'Gallery media deleted successfully.';
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
