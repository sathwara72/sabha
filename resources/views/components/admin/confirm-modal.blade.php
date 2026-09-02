@props([
    'show' => false,
    'title' => '',
    'message' => '',
    'confirmLabel' => 'Confirm',
    'cancelLabel' => 'Cancel',
    'variant' => 'danger',
    'confirmAction' => '',
    'cancelAction' => '',
])

@php
    $variants = [
        'danger' => ['icon' => 'trash-2', 'iconBg' => 'bg-rose-50 text-rose-600 border border-rose-100', 'confirmBtn' => 'bg-gradient-to-r from-rose-600 to-red-700 hover:opacity-95 text-white shadow-md shadow-rose-600/20', 'titleColor' => 'text-slate-900'],
        'warning' => ['icon' => 'alert-circle', 'iconBg' => 'bg-amber-50 text-amber-600 border border-amber-100', 'confirmBtn' => 'bg-gradient-to-r from-amber-500 to-amber-600 hover:opacity-95 text-white shadow-md shadow-amber-500/20', 'titleColor' => 'text-slate-900'],
        'info' => ['icon' => 'info', 'iconBg' => 'bg-blue-50 text-primary border border-blue-100', 'confirmBtn' => 'bg-gradient-to-r from-[#00379D] to-[#082e6e] hover:opacity-95 text-white shadow-md shadow-primary/20', 'titleColor' => 'text-slate-900'],
        'success' => ['icon' => 'check-circle-2', 'iconBg' => 'bg-emerald-50 text-emerald-600 border border-emerald-100', 'confirmBtn' => 'bg-gradient-to-r from-emerald-600 to-teal-700 hover:opacity-95 text-white shadow-md shadow-emerald-600/20', 'titleColor' => 'text-slate-900'],
    ];
    $cfg = $variants[$variant] ?? $variants['danger'];
@endphp

@if ($show)
    <template x-teleport="body">
        <div
            x-data
            x-on:keydown.escape.window="@if($cancelAction) $wire.{{ $cancelAction }}() @endif"
            class="fixed inset-0 z-[99999] overflow-y-auto p-4 sm:p-6 flex min-h-full items-center justify-center font-outfit"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" @if($cancelAction) wire:click="{{ $cancelAction }}" @endif></div>

            <div x-show="true" x-transition class="relative z-10 w-full max-w-sm rounded-3xl bg-white p-5 sm:p-6 shadow-2xl border border-slate-200 my-auto flex flex-col space-y-4">
                <button
                    type="button"
                    @if($cancelAction) wire:click="{{ $cancelAction }}" @endif
                    class="absolute top-4 right-4 flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer shadow-2xs"
                    aria-label="Close"
                >
                    <x-icon name="x" class="h-3.5 w-3.5" />
                </button>

                <div class="flex flex-col items-center text-center gap-2.5 pt-1">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $cfg['iconBg'] }} shadow-2xs">
                        <x-icon name="{{ $cfg['icon'] }}" class="h-5 w-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold {{ $cfg['titleColor'] }} leading-tight">{{ $title }}</h2>
                        <p class="mt-1 text-xs text-slate-500 font-medium leading-relaxed">{{ $message }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 pt-2 border-t border-slate-100">
                    <button
                        type="button"
                        @if($cancelAction) wire:click="{{ $cancelAction }}" @endif
                        wire:loading.attr="disabled"
                        wire:target="{{ $confirmAction }}"
                        class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 active:scale-[0.98] transition-all disabled:opacity-50 cursor-pointer shadow-xs"
                    >
                        {{ $cancelLabel }}
                    </button>
                    <button
                        type="button"
                        @if($confirmAction) wire:click="{{ $confirmAction }}" @endif
                        wire:loading.attr="disabled"
                        wire:target="{{ $confirmAction }}"
                        class="flex-1 rounded-xl px-4 py-2.5 text-xs font-bold active:scale-[0.98] transition-all disabled:opacity-60 cursor-pointer {{ $cfg['confirmBtn'] }}"
                    >
                        <span wire:loading.remove wire:target="{{ $confirmAction }}">{{ $confirmLabel }}</span>
                        <span wire:loading wire:target="{{ $confirmAction }}" class="flex items-center justify-center gap-1.5">
                            <span class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                            Please wait...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </template>
@endif
