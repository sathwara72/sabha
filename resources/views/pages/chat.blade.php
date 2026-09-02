<x-layouts.app title="Chat | Sabha" :noindex="true" :show-footer="false">
    @livewire('chat.inbox', ['activeId' => $id ?? null])
</x-layouts.app>
