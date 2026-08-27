<x-layouts.admin title="{{ ($id ?? null) ? 'Edit Category' : 'Add Category' }} | Sabha Admin">
    @livewire('admin.categories.form', ['id' => $id ?? null])
</x-layouts.admin>
