<div class="bg-background font-outfit">
    <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8 space-y-4">
        {{-- Title Row --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between border-b border-slate-200 pb-5">
            <div>
                <div class="mb-1.5 flex items-center gap-2">
                    <span class="h-3.5 w-1.5 rounded-full bg-primary"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-primary">{{ __('site.events.label') }}</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">{{ __('site.events.title') }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">{{ __('site.events.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-xl bg-blue-50 border border-blue-200 px-3 py-1 text-xs font-bold text-primary shadow-2xs">
                    {{ $events->total() }} {{ $events->total() === 1 ? __('site.events.event') : __('site.events.events_count') }}
                </span>
            </div>
        </div>

        {{-- Search & Status Filter Bar --}}
        <div class="glass-card p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs bg-white space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                {{-- Search Box --}}
                <div class="relative w-full lg:max-w-md">
                    <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        wire:model.live.debounce.350ms="search"
                        placeholder="{{ __('site.events.search_placeholder') }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-xs sm:text-sm font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:border-primary shadow-2xs"
                    />
                </div>

                {{-- Status Filter Pills --}}
                <div class="flex items-center gap-2 flex-wrap">
                    @foreach ([
                        ['key' => 'all', 'label' => __('site.events.all_events')],
                        ['key' => 'current', 'label' => '🟢 ' . __('site.events.booking_available')],
                        ['key' => 'upcoming', 'label' => '🟡 ' . __('site.events.booking_soon')],
                        ['key' => 'past', 'label' => '⚫ ' . __('site.events.past')],
                    ] as $opt)
                        <button
                            type="button"
                            wire:click="setFilter('{{ $opt['key'] }}')"
                            class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all cursor-pointer shadow-2xs {{ $filter === $opt['key'] ? 'bg-primary text-white shadow-sm' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                        >
                            {{ $opt['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Results Grid --}}
        <div class="relative">
            <x-loading-state target="search, filter, gotoPage, nextPage, previousPage" message="Searching events..." />

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($events as $event)
                    @php
                        $hasDiscount = $event->priceVerified && $event->priceVerified !== $event->priceNormal;
                        $displayPrice = $isVerifiedMember && $hasDiscount ? $event->priceVerified : $event->priceNormal;
                    @endphp
                    <a href="/events/{{ $event->model->id }}" class="block h-full cursor-pointer group">
                        <div class="glass-card flex h-full flex-col justify-between overflow-hidden p-0 rounded-2xl border border-slate-200 hover:border-primary/40 hover:shadow-md transition-all duration-300 bg-white">
                            {{-- Top Header: Full Uncropped Image with Ambient Backdrop & Badges --}}
                            <div>
                                <div class="relative h-36 sm:h-40 w-full overflow-hidden bg-slate-950 flex items-center justify-center">
                                    <x-safe-image
                                        :src="$event->image"
                                        :alt="$event->model->title"
                                        :title="$event->model->title"
                                        :date="$event->model->date"
                                        :blur-backdrop="true"
                                        fallback-type="event"
                                    />

                                    {{-- Type Badge --}}
                                    @if ($event->model->type)
                                        <div class="absolute left-2.5 top-2.5 z-20">
                                            <span class="inline-flex items-center rounded-lg bg-white/95 backdrop-blur-md px-2 py-0.5 text-[10px] font-black text-slate-800 shadow-md border border-white/40">
                                                {{ $event->model->type }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Clean Date Badge --}}
                                    <div class="absolute right-2.5 top-2.5 z-20">
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-950/80 backdrop-blur-md px-2 py-0.5 text-[10px] font-bold text-white shadow-md border border-white/20">
                                            <x-icon name="calendar" class="h-2.5 w-2.5 text-sky-400" />
                                            {{ $event->dateLabel }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Card Body Details --}}
                                <div class="p-3 space-y-2">
                                    {{-- Status & Location Bar --}}
                                    <div class="flex items-center justify-between gap-1.5 text-[10px]">
                                        @if ($event->status === 'current')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 font-black text-emerald-700 uppercase tracking-wide">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Booking Open
                                            </span>
                                        @elseif ($event->status === 'upcoming')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200 px-2 py-0.5 font-black text-amber-700 uppercase tracking-wide">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                Upcoming
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 border border-slate-200 px-2 py-0.5 font-bold text-slate-500 uppercase tracking-wide">
                                                Closed
                                            </span>
                                        @endif

                                        <div class="flex items-center gap-1 font-semibold text-slate-500 truncate max-w-[140px]">
                                            <x-icon name="map-pin" class="h-2.5 w-2.5 text-slate-400 shrink-0" />
                                            <span class="truncate">{{ $event->model->location ? explode(',', $event->model->location)[0] : 'Venue TBA' }}</span>
                                        </div>
                                    </div>

                                    {{-- Event Title --}}
                                    <div>
                                        <h3 class="text-sm font-black text-slate-900 tracking-tight transition-colors group-hover:text-primary truncate">
                                            {{ $event->model->title }}
                                        </h3>
                                    </div>

                                    {{-- Description Snippet --}}
                                    @if ($event->model->description)
                                        <p class="text-[11px] text-slate-500 font-normal line-clamp-1 leading-relaxed">
                                            {{ $event->model->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Footer: Ticket Prices & Action Button --}}
                            <div class="p-3 pt-0 space-y-2">
                                {{-- Ticket Price Box --}}
                                <div class="rounded-xl border border-slate-200/80 bg-slate-50/80 p-2 flex items-center justify-between gap-2 text-[11px]">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] uppercase font-bold text-slate-400">Regular</span>
                                        <span class="font-black text-slate-800 {{ $hasDiscount ? 'line-through text-slate-400 text-[10px]' : 'text-[11px]' }}">
                                            {{ format_price($event->priceNormal) }}
                                        </span>
                                    </div>

                                    @if ($hasDiscount)
                                        <div class="flex flex-col items-end">
                                            <span class="text-[9px] uppercase font-bold text-emerald-600 flex items-center gap-0.5">
                                                <x-icon name="shield-check" class="h-2 w-2 text-emerald-600" />
                                                SABHA Member
                                            </span>
                                            <span class="inline-flex items-center font-black text-emerald-700 bg-emerald-100/70 border border-emerald-200 px-1.5 py-0.2 rounded text-[10px]">
                                                {{ format_price($event->priceVerified) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Action Button --}}
                                @if ($event->status === 'current' || $event->status === 'upcoming')
                                    <div class="inline-flex w-full items-center justify-center gap-1 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-3 py-2 text-[11px] font-bold text-white shadow-sm group-hover:opacity-95 transition-all">
                                        <span>{{ __('site.events.book_ticket') }}</span>
                                        <x-icon name="arrow-right" class="h-3 w-3 transition-transform group-hover:translate-x-1" />
                                    </div>
                                @else
                                    <div class="w-full text-center py-2 bg-slate-100 border border-slate-200 rounded-xl text-slate-500 font-bold text-[11px]">
                                        {{ __('site.events.closed') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($events->total() === 0)
                <div class="rounded-2xl border border-dashed border-slate-200 py-20 text-center bg-slate-50/50">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                        <x-icon name="calendar" class="h-6 w-6" />
                    </div>
                    <h3 class="text-base font-bold text-slate-800">{{ __('site.events.no_events') }}</h3>
                    <p class="mx-auto mt-1 max-w-xs text-xs text-slate-500">{{ __('site.events.no_events_hint') }}</p>
                </div>
            @endif

            @if ($events->hasPages())
                <div class="mt-6">
                    <x-pagination :paginator="$events" item-label="events" />
                </div>
            @endif
        </div>
    </div>
</div>
