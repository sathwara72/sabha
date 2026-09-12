<x-layouts.app title="{{ __('site.chat.settings_title') }} | SABHA" :noindex="true">
    <div class="min-h-[calc(100vh-4rem)] bg-background font-outfit py-4 sm:py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @livewire('chat.group-settings', ['id' => $id])
        </div>
    </div>
</x-layouts.app>
