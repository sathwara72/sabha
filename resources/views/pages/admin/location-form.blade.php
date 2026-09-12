<x-layouts.admin title="{{ ($id ?? null) ? 'Edit City' : 'Add City' }} | Sabha Admin">
    @livewire('admin.locations.form', ['id' => $id ?? null])
</x-layouts.admin>
