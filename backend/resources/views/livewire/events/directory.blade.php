<div class="bg-background font-outfit">
    <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
        {{-- Compact title row --}}
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-2.5">
                    <span class="h-4 w-1.5 rounded-full bg-accent"></span>
                    <span class="text-sm font-semibold text-accent">{{ __('site.events.label') }}</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">{{ __('site.events.title') }}</h1>
                <p class="mt-1 text-sm text-muted font-medium">{{ __('site.events.subtitle') }}</p>
            </div>
            <p class="text-sm font-medium text-muted">
                {{ $events->total() }} {{ $events->total() === 1 ? __('site.events.event') : __('site.events.events_count') }}
            </p>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="mb-10 mt-6 flex flex-col gap-4">
            <div class="relative max-w-2xl">
                <x-icon name="search" class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="{{ __('site.events.search_placeholder') }}"
                    class="w-full rounded-xl border border-border bg-white py-3 pl-12 pr-4 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary font-semibold"
                />
            </div>

            <div class="flex flex-col gap-4 md:flex-row md:items-center">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-muted">
                        <x-icon name="filter" class="h-4 w-4 text-primary" />
                        {{ __('site.events.status') }}
                    </span>
                    <div class="flex flex-wrap gap-2">
                        @foreach ([
                            ['key' => 'all', 'label' => __('site.events.all_events')],
                            ['key' => 'current', 'label' => '🟢 ' . __('site.events.booking_available')],
                            ['key' => 'upcoming', 'label' => '🟡 ' . __('site.events.booking_soon')],
                            ['key' => 'past', 'label' => '⚫ ' . __('site.events.past')],
                        ] as $opt)
                            <button
                                wire:click="setFilter('{{ $opt['key'] }}')"
                                class="rounded-full px-4 py-1.5 text-xs font-semibold transition-all cursor-pointer {{ $filter === $opt['key'] ? 'bg-primary text-white shadow-sm' : 'border border-border bg-white text-muted hover:bg-surface hover:text-foreground' }}"
                            >{{ $opt['label'] }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-2 rounded-xl bg-primary-soft px-4 py-2.5 text-sm font-medium text-primary md:ml-auto">
                    <x-icon name="info" class="h-4 w-4" />
                    {{ __('site.events.verified_info') }}
                </div>
            </div>
        </div>

        {{-- Results Grid --}}
        <div wire:loading.delay class="py-20 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent"></div>
            <p class="mt-3 text-sm text-muted">{{ __('site.events.loading') }}</p>
        </div>

        <div wire:loading.remove.delay>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($events as $event)
                    @php
                        $hasDiscount = $event->priceVerified && $event->priceVerified !== $event->priceNormal;
                        $displayPrice = $isVerifiedMember && $hasDiscount ? $event->priceVerified : $event->priceNormal;
                        $isFree = in_array(strtolower($displayPrice), ['free', '₹0', '0'], true);
                    @endphp
                    <a href="/events/{{ $event->model->id }}" class="block h-full cursor-pointer">
                        <div class="glass-card group flex h-full flex-col overflow-hidden p-0 hover:shadow-md transition-shadow">
                            <div class="relative h-36 sm:h-40 w-full overflow-hidden flex items-center justify-center">
                                <x-safe-image
                                    :src="$event->image"
                                    :alt="$event->model->title"
                                    :title="$event->model->title"
                                    :date="$event->model->date"
                                    fallback-type="event"
                                    img-class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent pointer-events-none"></div>

                                <div class="absolute left-3 top-3 z-10">
                                    <span class="rounded-full bg-white/90 px-2.5 py-0.5 text-[10px] font-bold text-foreground backdrop-blur shadow-xs">{{ $event->model->type }}</span>
                                </div>

                                <div class="absolute right-3 top-3 z-10">
                                    <span class="rounded-full bg-black/70 border border-white/20 backdrop-blur-md px-2.5 py-0.5 text-[10px] font-black text-white shadow-md">
                                        {{ $event->monthShort }} {{ $event->dayNum }}, {{ $event->yearNum }}
                                    </span>
                                </div>

                                <div class="absolute bottom-3 left-3 right-3 z-10 flex items-center justify-between gap-2">
                                    <span class="rounded bg-black/75 border border-white/15 backdrop-blur-sm px-2.5 py-1 text-[11px] font-black text-white truncate max-w-[70%] shadow-md">{{ $event->model->title }}</span>
                                    <span class="rounded bg-primary border border-primary/20 backdrop-blur-sm px-2 py-1 text-[9px] font-black text-white shrink-0 shadow-md">{{ $event->dateLabel }}</span>
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col p-3">
                                @if ($event->status === 'upcoming' || $event->status === 'past')
                                    <div class="mb-2 flex items-center justify-between">
                                        @if ($event->status === 'upcoming')
                                            <span class="rounded-full bg-amber-50 border border-amber-100 px-2 py-0.5 text-[9px] font-bold text-amber-700 uppercase tracking-wider">{{ __('site.events.upcoming') }}</span>
                                        @else
                                            <span class="rounded-full bg-slate-100 border border-slate-200 px-2 py-0.5 text-[9px] font-bold text-slate-500 uppercase tracking-wider">{{ __('site.events.past') }}</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="flex items-start justify-between gap-3">
                                    <p class="line-clamp-2 text-xs leading-relaxed text-muted font-medium flex-1">{{ $event->model->description }}</p>
                                    <span class="inline-flex items-center gap-1 text-[11px] text-muted font-bold shrink-0 mt-0.5">
                                        <x-icon name="map-pin" class="h-[11px] w-[11px] text-primary" /> {{ $event->attendees }} {{ __('site.events.going') }}
                                    </span>
                                </div>

                                <div class="mt-2 border-t border-border pt-3">
                                    <div class="mb-2.5 flex flex-col justify-center rounded-xl border p-2.5 transition-colors {{ $isVerifiedMember && $hasDiscount ? 'border-emerald-200 bg-emerald-50/30' : 'border-border bg-slate-50/50' }}">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[9px] font-extrabold uppercase tracking-wider text-muted-foreground">{{ __('site.events.ticket_price') }}</span>
                                                <div class="flex items-baseline gap-1">
                                                    @if ($isVerifiedMember && $hasDiscount)
                                                        <span class="text-[10px] font-semibold text-muted-foreground line-through decoration-slate-400">{{ $event->priceNormal }}</span>
                                                    @endif
                                                    <span class="text-sm font-black leading-none {{ $isFree ? 'text-emerald-600 uppercase' : 'text-primary' }}">{{ $displayPrice }}</span>
                                                </div>
                                            </div>

                                            @if ($hasDiscount)
                                                <div class="shrink-0">
                                                    @if ($isVerifiedMember)
                                                        <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[9px] font-extrabold text-emerald-700 shadow-sm border border-emerald-100">
                                                            <x-icon name="shield-check" class="h-[10px] w-[10px] shrink-0 text-emerald-600" /> {{ __('site.events.verified_discount') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50/80 px-2 py-0.5 text-[9px] font-bold text-emerald-700 shadow-sm border border-emerald-100/60">
                                                            <x-icon name="shield-check" class="h-[10px] w-[10px] shrink-0 text-emerald-600" /> {{ __('site.events.verified') }}: {{ $event->priceVerified }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($event->status === 'current' || $event->status === 'upcoming')
                                        <div class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98]">
                                            {{ __('site.events.book_ticket') }} <x-icon name="arrow-right" class="h-4 w-4" />
                                        </div>
                                    @else
                                        <div class="w-full text-center py-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-500 font-semibold text-sm">{{ __('site.events.closed') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($events->total() === 0)
                <div class="rounded-2xl border border-dashed border-border py-24 text-center">
                    <h3 class="text-xl font-semibold text-foreground">{{ __('site.events.no_events') }}</h3>
                    <p class="mx-auto mt-2 max-w-xs text-sm text-muted">{{ __('site.events.no_events_hint') }}</p>
                </div>
            @endif

            <x-pagination :paginator="$events" item-label="events" />
        </div>
    </div>
</div>
