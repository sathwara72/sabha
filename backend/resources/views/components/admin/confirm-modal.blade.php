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
        'danger' => ['icon' => 'trash-2', 'iconBg' => 'bg-red-100 text-red-600', 'confirmBtn' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500', 'titleColor' => 'text-red-700'],
        'warning' => ['icon' => 'alert-circle', 'iconBg' => 'bg-amber-100 text-amber-600', 'confirmBtn' => 'bg-amber-500 hover:bg-amber-600 text-white focus:ring-amber-400', 'titleColor' => 'text-amber-700'],
        'info' => ['icon' => 'info', 'iconBg' => 'bg-blue-100 text-blue-600', 'confirmBtn' => 'bg-primary hover:opacity-90 text-white focus:ring-primary', 'titleColor' => 'text-foreground'],
        'success' => ['icon' => 'check-circle-2', 'iconBg' => 'bg-green-100 text-green-600', 'confirmBtn' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500', 'titleColor' => 'text-green-700'],
    ];
    $cfg = $variants[$variant] ?? $variants['danger'];
@endphp

@if ($show)
    <div
        x-data
        x-on:keydown.escape.window="@if($cancelAction) $wire.{{ $cancelAction }}() @endif"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
    >
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @if($cancelAction) wire:click="{{ $cancelAction }}" @endif></div>

        <div x-show="true" x-transition class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl border border-border">
            <button
                @if($cancelAction) wire:click="{{ $cancelAction }}" @endif
                class="absolute top-3 right-3 rounded-lg p-1.5 text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors"
                aria-label="Close"
            >
                <x-icon name="x" class="h-4 w-4" />
            </button>

            <div class="flex flex-col items-center text-center gap-3 mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full {{ $cfg['iconBg'] }}">
                    <x-icon name="{{ $cfg['icon'] }}" class="h-5 w-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold {{ $cfg['titleColor'] }}">{{ $title }}</h2>
                    <p class="mt-1 text-xs text-muted font-medium leading-relaxed">{{ $message }}</p>
                </div>
            </div>

            <div class="border-t border-border my-4"></div>

            <div class="flex gap-2">
                <button
                    @if($cancelAction) wire:click="{{ $cancelAction }}" @endif
                    wire:loading.attr="disabled"
                    wire:target="{{ $confirmAction }}"
                    class="flex-1 rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground hover:bg-slate-50 active:scale-95 transition-all disabled:opacity-50"
                >
                    {{ $cancelLabel }}
                </button>
                <button
                    @if($confirmAction) wire:click="{{ $confirmAction }}" @endif
                    wire:loading.attr="disabled"
                    wire:target="{{ $confirmAction }}"
                    class="flex-1 rounded-xl px-4 py-2 text-xs font-bold active:scale-95 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 {{ $cfg['confirmBtn'] }}"
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
@endif
