<x-layouts.admin title="{{ ($id ?? null) ? 'Edit Event' : 'Create Event' }} | Sabha Admin">
    @livewire('admin.events.form', ['id' => $id ?? null])
</x-layouts.admin>
