<?php

namespace App\Livewire\Admin\Testimonials;

use App\Models\BusinessReferral;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleDisplay(int $id): void
    {
        admin_authorize('testimonials', 'can_edit');

        $referral = BusinessReferral::whereNotNull('testimonial')->findOrFail($id);
        $referral->update(['display_testimonial' => ! $referral->display_testimonial]);
    }

    public function render()
    {
        $query = BusinessReferral::whereNotNull('testimonial')->with(['giver', 'receiver'])->latest();

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('testimonial', 'like', "%{$search}%")
                    ->orWhereHas('giver', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('receiver', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $testimonials = $query->paginate(12);

        return view('livewire.admin.testimonials.index', [
            'testimonials' => $testimonials,
            'totalCount' => BusinessReferral::whereNotNull('testimonial')->count(),
            'visibleCount' => BusinessReferral::whereNotNull('testimonial')->where('display_testimonial', true)->count(),
        ]);
    }
}
