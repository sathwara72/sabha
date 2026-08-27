@props([
    'show' => false,
    'title' => '',
    'message' => '',
    'placeholder' => 'Enter details...',
    'confirmLabel' => 'Submit',
    'cancelLabel' => 'Cancel',
    'model' => 'promptValue',
    'confirmAction' => '',
    'cancelAction' => '',
])

@if ($show)
    <div
        x-data
        x-on:keydown.escape.window="@if($cancelAction) $wire.{{ $cancelAction }}() @endif"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
    >
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @if($cancelAction) wire:click="{{ $cancelAction }}" @endif></div>

        <div x-show="true" x-transition class="relative w-full max-w-md transform rounded-2xl bg-white p-6 shadow-2xl transition-all border border-border">
            <button
                @if($cancelAction) wire:click="{{ $cancelAction }}" @endif
                class="absolute top-4 right-4 rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors"
            >
                <x-icon name="x" class="h-4.5 w-4.5" />
            </button>

            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 border border-rose-100">
                    <x-icon name="x-circle" class="h-5 w-5" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">{{ $title }}</h3>
                    <p class="text-xs text-slate-500">{{ $message }}</p>
                </div>
            </div>

            <div class="space-y-4 mt-4">
                <div>
                    <textarea
                        wire:model="{{ $model }}"
                        rows="3"
                        placeholder="{{ $placeholder }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-xs text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500"
                    ></textarea>
                    @error($model)
                        <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $errorMessage }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                    <button
                        @if($cancelAction) wire:click="{{ $cancelAction }}" @endif
                        wire:loading.attr="disabled"
                        wire:target="{{ $confirmAction }}"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition-colors hover:bg-slate-50 active:scale-95 disabled:opacity-50"
                    >
                        {{ $cancelLabel }}
                    </button>
                    <button
                        @if($confirmAction) wire:click="{{ $confirmAction }}" @endif
                        wire:loading.attr="disabled"
                        wire:target="{{ $confirmAction }}"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white transition-all hover:bg-rose-700 active:scale-95 disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="{{ $confirmAction }}">{{ $confirmLabel }}</span>
                        <span wire:loading wire:target="{{ $confirmAction }}">Submitting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
