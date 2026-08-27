<x-layouts.admin title="{{ ($id ?? null) ? 'Edit Trustee' : 'Add Trustee' }} | Sabha Admin">
    @livewire('admin.trustees.form', ['id' => $id ?? null])
</x-layouts.admin>
