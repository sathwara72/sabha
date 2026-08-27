<x-layouts.app :title="($id ?? null) ? 'Edit Meeting | Sabha' : 'Log Meeting | Sabha'" :noindex="true">
    @livewire('profile.meeting-form', ['id' => $id ?? null])
</x-layouts.app>
