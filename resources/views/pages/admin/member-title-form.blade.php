<x-layouts.admin title="{{ ($id ?? null) ? 'Edit Member Title' : 'Add Member Title' }} | Sabha Admin">
    @livewire('admin.member-titles.form', ['id' => $id ?? null])
</x-layouts.admin>
