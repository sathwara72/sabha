@props([
    'target' => 'search',
    'message' => 'Searching...',
    'type' => 'overlay', // 'overlay', 'fullscreen', 'inline', 'block'
])

@if ($type === 'overlay' || $type === 'fullscreen')
    <div
        wire:loading.delay.shortest.flex
        wire:target="{{ $target }}"
        style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/35 backdrop-blur-xs transition-all"
    >
        <div class="flex items-center gap-3 rounded-2xl bg-white px-6 py-4 shadow-2xl border border-slate-200/90 animate-in fade-in zoom-in-95 duration-150">
            <svg class="h-5 w-5 animate-spin text-[#00379D] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-xs sm:text-sm font-bold text-slate-800 font-outfit tracking-wide">{{ $message }}</span>
        </div>
    </div>
@elseif ($type === 'block')
    <div
        wire:loading.delay.shortest
        wire:target="{{ $target }}"
        class="py-16 text-center text-slate-500 text-xs bg-white rounded-2xl border border-slate-200 shadow-xs flex flex-col items-center justify-center gap-2.5 font-outfit"
    >
        <svg class="h-7 w-7 animate-spin text-[#00379D] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="font-bold text-slate-600">{{ $message }}</span>
    </div>
@elseif ($type === 'inline')
    <div
        wire:loading.delay.shortest
        wire:target="{{ $target }}"
        class="inline-flex items-center gap-2 text-xs font-bold text-primary font-outfit"
    >
        <svg class="h-4 w-4 animate-spin text-[#00379D] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        @if ($message)
            <span>{{ $message }}</span>
        @endif
    </div>
@endif
