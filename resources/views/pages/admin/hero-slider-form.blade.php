<x-layouts.admin title="{{ ($id ?? null) ? 'Edit Hero Slide' : 'Add Hero Slide' }} | Sabha Admin">
    @livewire('admin.hero-slider.form', ['id' => $id ?? null])
</x-layouts.admin>
