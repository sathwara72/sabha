<x-layouts.admin title="{{ ($id ?? null) ? 'Manage Permissions' : 'Add Sub-Admin' }} | Sabha Admin">
    @livewire('admin.sub-admins.form', ['id' => $id ?? null])
</x-layouts.admin>
