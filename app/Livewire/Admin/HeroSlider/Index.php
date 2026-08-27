<?php

namespace App\Livewire\Admin\HeroSlider;

use App\Models\HeroImage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public ?int $deleteId = null;

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
        }

        $this->deleteId = null;
    }

    public function render()
    {
        return view('livewire.admin.hero-slider.index', [
            'heroImages' => HeroImage::with('event')->orderBy('sort_order')->get(),
        ]);
    }
}
