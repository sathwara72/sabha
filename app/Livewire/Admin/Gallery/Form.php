<?php

namespace App\Livewire\Admin\Gallery;

use App\Services\GalleryMediaUploader;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public array $mediaFiles = [];

    public string $uploadError = '';

    public bool $uploading = false;

    public function upload(GalleryMediaUploader $uploader)
    {
        admin_authorize('gallery', 'can_add');

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

        return $this->redirect(route('admin.gallery.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.gallery.form');
    }
}
