@props(['event'])

@if ($event && media_url($event->image))
    <div
        x-data="{
            open: false,
            storageKey: 'sabha_popup_dismissed_{{ $event->id }}',
            init() {
                if (! localStorage.getItem(this.storageKey)) {
                    this.open = true;
                }
            },
            dismiss() {
                this.open = false;
                localStorage.setItem(this.storageKey, '1');
            },
        }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    >
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" x-on:click="dismiss"></div>

        <div x-show="open" x-transition class="relative w-full max-w-md">
            <button
                type="button"
                x-on:click="dismiss"
                class="absolute -right-2 -top-2 z-10 rounded-full bg-white p-1.5 text-foreground shadow-lg transition-colors hover:bg-slate-100"
                aria-label="Close"
            >
                <x-icon name="x" class="h-4 w-4" />
            </button>

            <a href="{{ route('events.show', $event->id) }}" x-on:click="dismiss" class="block overflow-hidden rounded-2xl border border-border shadow-2xl">
                <img src="{{ media_url($event->image) }}" alt="{{ $event->title }}" class="w-full object-cover" />
            </a>
        </div>
    </div>
@endif
