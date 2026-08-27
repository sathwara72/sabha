<?php

namespace App\Livewire\Admin\Gallery;

use App\Models\GalleryImage;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?int $deleteId = null;

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
