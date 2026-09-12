@props(['event'])

@if ($event)
    <div
        x-data="{
            open: false,
            storageKey: 'sabha_popup_dismissed_{{ $event->id }}',
            init() {
                if (! sessionStorage.getItem(this.storageKey)) {
                    // Small delay for smooth entry animation
                    setTimeout(() => { this.open = true; }, 400);
                }
            },
            dismiss() {
                this.open = false;
                sessionStorage.setItem(this.storageKey, '1');
            },
        }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 select-none font-outfit"
    >
        {{-- Backdrop --}}
        <div
            class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"
            x-on:click="dismiss"
        ></div>

        {{-- Modal Popup Card --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-md sm:max-w-lg z-10 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl flex flex-col"
        >
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-4 sm:px-5 py-3 border-b border-slate-100 bg-white">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-primary"></span>
                    </span>
                    <span class="text-xs font-black uppercase tracking-wider text-primary">{{ __('site.event_popup.upcoming_badge') }}</span>
                    {{-- Event Category: Commented Out
                    @if ($event->type)
                        <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700">
                            {{ $event->type }}
                        </span>
                    @endif
                    --}}
                </div>

                <button
                    type="button"
                    x-on:click="dismiss"
                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer shadow-2xs"
                    title="{{ __('site.event_popup.close') }}"
                >
                    <x-icon name="x" class="h-3.5 w-3.5" />
                </button>
            </div>

            {{-- Event Flyer / Photo Box --}}
            @if (has_media_file($event->image) && media_url($event->image))
                <div class="relative w-full h-56 sm:h-64 bg-slate-950 overflow-hidden flex items-center justify-center">
                    <x-safe-image
                        :src="media_url($event->image)"
                        :alt="$event->title"
                        :title="$event->title"
                        :date="$event->date"
                        :blur-backdrop="true"
                        fallback-type="event"
                    />

                    {{-- Date Badge Floating Top Left --}}
                    @if ($event->date)
                        <div class="absolute top-3 left-3 flex flex-col items-center justify-center rounded-lg bg-white/95 backdrop-blur-sm border border-white/70 shadow-sm px-2 py-1 min-w-[42px]">
                            <span class="text-[9px] font-extrabold uppercase tracking-wider text-primary">{{ $event->date->format('M') }}</span>
                            <span class="text-sm font-black text-slate-900 leading-none">{{ $event->date->format('d') }}</span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Event Details Body --}}
            <div class="p-4 sm:p-5 space-y-3 bg-white">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 leading-snug">
                        {{ $event->title }}
                    </h3>
                    @if ($event->description)
                        <p class="text-xs text-slate-600 line-clamp-2 mt-1 leading-relaxed">
                            {{ $event->description }}
                        </p>
                    @endif
                </div>

                {{-- Key Info Grid --}}
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="rounded-xl border border-slate-200/80 bg-slate-50/70 p-2.5 space-y-0.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                            <x-icon name="calendar" class="h-3 w-3 text-primary" /> {{ __('site.event_popup.date') }} & {{ __('site.event_popup.time') }}
                        </span>
                        <p class="text-[11px] font-bold text-slate-900 truncate">
                            {{ $event->date ? $event->date->format('M d, Y • g:i A') : 'TBA' }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200/80 bg-slate-50/70 p-2.5 space-y-0.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                            <x-icon name="map-pin" class="h-3 w-3 text-primary" /> {{ __('site.event_popup.location') }}
                        </span>
                        <p class="text-[11px] font-bold text-slate-900 truncate" title="{{ $event->location }}">
                            {{ $event->location ?: 'Gujarat' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Modal Footer CTA Buttons --}}
            <div class="flex items-center justify-end gap-2.5 px-4 sm:px-5 py-3 border-t border-slate-100 bg-slate-50/60">
                <button
                    type="button"
                    x-on:click="dismiss"
                    class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 active:scale-[0.98] transition-all cursor-pointer shadow-xs"
                >
                    {{ __('site.event_popup.close') }}
                </button>
                <a
                    href="{{ route('events.show', $event->id) }}"
                    x-on:click="dismiss"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer"
                >
                    <span>{{ __('site.event_popup.register_now') }}</span>
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>
        </div>
    </div>
@endif
