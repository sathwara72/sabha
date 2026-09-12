<?php

namespace App\Livewire\Admin\MemberTitles;

use App\Models\MemberTitle;
use Livewire\Component;

class Form extends Component
{
    public const COLORS = ['primary', 'emerald', 'amber', 'rose', 'indigo', 'sky', 'violet', 'slate'];

    public ?int $titleId = null;

    public string $name = '';

    public string $color = 'primary';

    public function mount(?int $id = null): void
    {
        if (! $id) {
            return;
        }

        $title = MemberTitle::findOrFail($id);
        $this->titleId = $title->id;
        $this->name = $title->name;
        $this->color = $title->badge_color;
    }

    public function save()
    {
        admin_authorize('member-titles', $this->titleId ? 'can_edit' : 'can_add');

        $this->validate([
            'name' => 'required|string|max:100|unique:member_titles,name,' . $this->titleId,
            'color' => 'required|string|in:' . implode(',', self::COLORS),
        ]);

        if ($this->titleId) {
            MemberTitle::findOrFail($this->titleId)->update([
                'name' => $this->name,
                'badge_color' => $this->color,
            ]);
        } else {
            $maxOrder = MemberTitle::max('sort_order') ?? -1;
            MemberTitle::create([
                'name' => $this->name,
                'badge_color' => $this->color,
                'sort_order' => $maxOrder + 1,
                'is_active' => true,
            ]);
        }

        return $this->redirect(route('admin.member-titles.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.member-titles.form', ['colors' => self::COLORS]);
    }
}
