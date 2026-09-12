@props(['src', 'alt' => 'Image', 'thumbClass' => 'h-32 w-full object-cover'])

<div x-data="{ open: false }">
    <button
        type="button"
        x-on:click="open = true"
        class="relative block w-full rounded-xl border border-border overflow-hidden bg-slate-900 group shadow-sm cursor-zoom-in"
    >
        <img src="{{ $src }}" alt="{{ $alt }}" class="{{ $thumbClass }} group-hover:scale-105 transition-transform duration-300" />
        <span class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-xs font-bold gap-1.5 backdrop-blur-[2px]">
            <x-icon name="zoom-in" class="h-4 w-4" /> Zoom
        </span>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        x-on:keydown.escape.window="open = false"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
    >
        <div class="absolute inset-0 bg-slate-900/90 backdrop-blur-sm" x-on:click="open = false"></div>
        <button type="button" x-on:click="open = false" class="absolute top-4 right-4 z-10 rounded-full bg-white/10 p-2 text-white hover:bg-white/20 transition-colors">
            <x-icon name="x" class="h-5 w-5" />
        </button>
        <img src="{{ $src }}" alt="{{ $alt }}" class="relative z-[1] max-h-[90vh] max-w-[90vw] rounded-xl shadow-2xl object-contain" x-on:click.stop />
    </div>
</div>
