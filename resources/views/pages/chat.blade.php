<x-layouts.app title="Chat | Sabha" :noindex="true">
    @livewire('chat.inbox', ['activeId' => $id ?? null])
</x-layouts.app>
