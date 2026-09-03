<x-layouts.app>
    @php
        $statLabelMap = [
            'active members' => 'site.stats.active_members',
            'businesses registered' => 'site.stats.businesses_registered',
            'events hosted' => 'site.stats.events_hosted',
            'connections made' => 'site.stats.connections_made',
            'verified members' => 'site.stats.verified_members',
            'business exchanged' => 'site.stats.business_exchanged',
            'monthly mixers' => 'site.stats.monthly_mixers',
            'members' => 'site.stats.members',
        ];
        $translateStatLabel = function (string $label) use ($statLabelMap) {
            $key = $statLabelMap[strtolower(trim($label))] ?? null;
            return $key ? __($key) : $label;
        };
    @endphp

    <x-event-popup :event="$popupEvent" />

    <div class="bg-background">
        {{-- Hero --}}
        <section class="hero-surface relative overflow-hidden border-b border-border">
            <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8 lg:py-8 grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-center">
                <div class="flex flex-col items-start text-left space-y-6">
                    <span class="inline-flex items-center gap-2 rounded-full bg-primary-soft px-4 py-1.5 text-xs sm:text-sm font-semibold text-primary">
                        <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                        {{ __('site.hero.badge') }}
                    </span>

                    <h1 class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl lg:text-6xl uppercase">
                        {{ __('site.hero.title') }}
                    </h1>

                    <p class="text-base leading-relaxed text-muted max-w-2xl font-medium">
                        {{ __('site.hero.subtitle') }}
                    </p>

                    <div class="flex flex-col items-center gap-3 sm:flex-row w-full sm:w-auto">
                        @auth
                            @if (auth()->user()->canAccessAdminArea())
                                <a href="/admin" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-7 py-3.5 text-base font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] sm:w-auto cursor-pointer">
                                    <span>Admin Panel</span> <x-icon name="arrow-right" class="h-[18px] w-[18px]" />
                                </a>
                            @else
                                <a href="/profile" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-7 py-3.5 text-base font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] sm:w-auto cursor-pointer">
                                    {{ __('site.nav.profile') }} <x-icon name="arrow-right" class="h-[18px] w-[18px]" />
                                </a>
                            @endif
                            <a href="/events" class="inline-flex w-full items-center justify-center rounded-xl border border-border bg-white px-7 py-3.5 text-base font-semibold text-foreground transition-colors hover:bg-surface sm:w-auto cursor-pointer">
                                {{ __('site.hero.cta_events') }}
                            </a>
                        @else
                            <a href="/register" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-7 py-3.5 text-base font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] sm:w-auto cursor-pointer">
                                {{ __('site.nav.register') }} <x-icon name="arrow-right" class="h-[18px] w-[18px]" />
                            </a>
                            <a href="/events" class="inline-flex w-full items-center justify-center rounded-xl border border-border bg-white px-7 py-3.5 text-base font-semibold text-foreground transition-colors hover:bg-surface sm:w-auto cursor-pointer">
                                {{ __('site.hero.cta_events') }}
                            </a>
                        @endauth
                    </div>
                </div>

                <div
                    x-data="{ current: 0, timer: null }"
                    x-init="timer = setInterval(() => { current = (current + 1) % {{ count($heroImages) }} }, 5000)"
                    class="relative h-[280px] w-full overflow-hidden rounded-2xl border border-border bg-slate-900 sm:h-[360px] lg:h-[420px] shadow-xl group"
                >
                    @foreach ($heroImages as $i => $slide)
                        @if ($slide['link'])
                            <a
                                href="{{ $slide['link'] }}"
                                @if ($slide['external']) target="_blank" rel="noopener noreferrer" @endif
                                x-show="current === {{ $i }}"
                                x-cloak
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 scale-105"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-500"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                :class="current === {{ $i }} ? 'z-10 pointer-events-auto' : 'z-0 pointer-events-none'"
                                class="absolute inset-0 block h-full w-full cursor-pointer overflow-hidden"
                                title="{{ $slide['title'] ?: 'Click to open link' }}"
                            >
                                <x-safe-image
                                    :src="$slide['url']"
                                    :alt="$slide['title'] ?: 'SABHA Highlight'"
                                    :title="$slide['title']"
                                    :blur-backdrop="true"
                                    fallback-type="banner"
                                />
                                
                                {{-- Subtle External / Event Link Badge in Top Corner --}}
                                <div class="absolute top-3.5 right-3.5 z-20 flex items-center gap-1.5 rounded-full bg-slate-900/75 backdrop-blur-md px-3 py-1 text-[11px] font-bold text-white shadow-lg border border-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <span>{{ $slide['external'] ? 'Visit Link' : 'View Event' }}</span>
                                    <x-icon name="arrow-right" class="h-3 w-3 -rotate-45" />
                                </div>

                                {{-- Optional Caption Overlay if Present --}}
                                @if (!empty($slide['title']) || !empty($slide['caption']))
                                    <div class="absolute inset-x-0 bottom-0 z-20 bg-gradient-to-t from-slate-950/85 via-slate-950/50 to-transparent p-5 pt-12 text-white">
                                        @if (!empty($slide['title']))
                                            <h3 class="text-base sm:text-lg font-black leading-tight">{{ $slide['title'] }}</h3>
                                        @endif
                                        @if (!empty($slide['caption']))
                                            <p class="text-xs sm:text-sm text-slate-200 mt-0.5 line-clamp-1 font-medium">{{ $slide['caption'] }}</p>
                                        @endif
                                    </div>
                                @endif
                            </a>
                        @else
                            <div
                                x-show="current === {{ $i }}"
                                x-cloak
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 scale-105"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-500"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                :class="current === {{ $i }} ? 'z-10 pointer-events-auto' : 'z-0 pointer-events-none'"
                                class="absolute inset-0 h-full w-full overflow-hidden"
                            >
                                <x-safe-image
                                    :src="$slide['url']"
                                    :alt="$slide['title'] ?: 'SABHA Highlight'"
                                    :title="$slide['title']"
                                    :blur-backdrop="true"
                                    fallback-type="banner"
                                />

                                @if (!empty($slide['title']) || !empty($slide['caption']))
                                    <div class="absolute inset-x-0 bottom-0 z-20 bg-gradient-to-t from-slate-950/85 via-slate-950/50 to-transparent p-5 pt-12 text-white">
                                        @if (!empty($slide['title']))
                                            <h3 class="text-base sm:text-lg font-black leading-tight">{{ $slide['title'] }}</h3>
                                        @endif
                                        @if (!empty($slide['caption']))
                                            <p class="text-xs sm:text-sm text-slate-200 mt-0.5 line-clamp-1 font-medium">{{ $slide['caption'] }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach

                    {{-- Navigation Dots --}}
                    <div class="absolute inset-x-0 bottom-4 flex justify-center items-center gap-2 z-30 pointer-events-auto">
                        @foreach ($heroImages as $i => $image)
                            <button
                                type="button"
                                x-on:click="current = {{ $i }}"
                                :class="current === {{ $i }} ? 'bg-white w-7 shadow-md' : 'bg-white/50 hover:bg-white/80 w-2'"
                                class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                                aria-label="Go to slide {{ $i + 1 }}"
                            ></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- Stats with Animated Counter --}}
        <section class="border-b border-border bg-surface font-outfit">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-8 px-6 py-14 text-center sm:grid-cols-3">
                @foreach ($stats as $stat)
                    <div x-data="animatedCounter('{{ $stat->value }}')" class="flex flex-col items-center">
                        <p
                            class="text-4xl sm:text-5xl font-black text-foreground tracking-tight"
                            x-text="displayValue"
                        >
                            {{ $stat->value }}
                        </p>
                        <p class="mt-2 text-xs sm:text-sm font-bold text-muted uppercase tracking-wider">
                            {{ $translateStatLabel($stat->label) }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Core Pillars --}}
        <section class="mx-auto max-w-7xl px-6 py-9 lg:py-8 border-b border-border">
            <div class="text-center max-w-3xl mx-auto mb-8">
                <p class="text-sm font-semibold text-primary uppercase tracking-wider">{{ __('site.home.pillars_label') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">{{ __('site.home.pillars_title') }}</h2>
                <p class="mt-4 text-sm text-muted leading-relaxed">{{ __('site.home.pillars_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                @foreach ([
                    ['title' => __('site.home.pillar_1_title'), 'desc' => __('site.home.pillar_1_desc'), 'icon' => 'briefcase', 'color' => 'from-blue-500 to-indigo-600'],
                    ['title' => __('site.home.pillar_2_title'), 'desc' => __('site.home.pillar_2_desc'), 'icon' => 'users', 'color' => 'from-emerald-500 to-teal-600'],
                    ['title' => __('site.home.pillar_3_title'), 'desc' => __('site.home.pillar_3_desc'), 'icon' => 'zap', 'color' => 'from-orange-500 to-amber-600'],
                ] as $pillar)
                    <div class="glass-card flex flex-col p-8 transition-all hover:border-primary/20 group hover:shadow-md">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br {{ $pillar['color'] }} text-white shadow-md mb-6 transition-transform group-hover:scale-105">
                            <x-icon :name="$pillar['icon']" class="h-[26px] w-[26px]" />
                        </div>
                        <h3 class="text-lg font-bold text-foreground group-hover:text-primary transition-colors">{{ $pillar['title'] }}</h3>
                        <p class="mt-3.5 text-xs leading-relaxed text-muted flex-1">{{ $pillar['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Events Section --}}
        <section class="border-y border-border bg-surface">
            <div class="mx-auto max-w-7xl px-6 py-6 lg:py-7">
                <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                    <div class="max-w-2xl">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-primary">
                            <x-icon name="calendar" class="h-3.5 w-3.5" />
                            {{ __('site.home.events_label') }}
                        </span>
                        <h2 class="mt-2 text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-foreground">{{ __('site.home.events_title') }}</h2>
                    </div>
                    <a href="/events" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-primary transition-colors hover:opacity-80 group">
                        {{ __('site.home.events_view_gallery') }}
                        <x-icon name="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($events as $event)
                        <div class="glass-card group overflow-hidden p-0 rounded-2xl border border-slate-200/80 hover:shadow-lg transition-all duration-300 flex flex-col justify-between bg-white">
                            <a href="/events/{{ $event->id }}" class="flex flex-col h-full">
                                {{-- Card Image / Safe Fallback with Blurred Ambient Backdrop --}}
                                <div class="relative h-48 w-full overflow-hidden bg-slate-950 flex items-center justify-center">
                                    <x-safe-image
                                        :src="media_url($event->image)"
                                        :alt="$event->title"
                                        :title="$event->title"
                                        :date="$event->date"
                                        :blur-backdrop="true"
                                        fallback-type="event"
                                    />
                                    <div class="absolute top-3 left-3 z-20">
                                        <span class="inline-flex items-center gap-1 rounded-xl bg-slate-950/80 backdrop-blur-md px-2.5 py-1 text-[11px] font-bold text-white shadow-md border border-white/20">
                                            <x-icon name="calendar" class="h-3 w-3 text-sky-400" />
                                            {{ $event->date ? $event->date->format('M j, Y') : 'Upcoming' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Card Content --}}
                                <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                                    <div class="space-y-1.5">
                                        <h4 class="text-sm sm:text-base font-bold text-foreground group-hover:text-primary transition-colors line-clamp-1">
                                            {{ $event->title }}
                                        </h4>
                                        <div class="flex items-center gap-1.5 text-xs text-muted-foreground font-medium">
                                            <x-icon name="map-pin" class="h-3.5 w-3.5 text-slate-400 shrink-0" />
                                            <span class="truncate">{{ $event->location ? explode(',', $event->location)[0] : 'Venue TBA' }}</span>
                                        </div>
                                    </div>

                                    {{-- Pricing Row --}}
                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 text-xs">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] uppercase font-bold text-slate-400">{{ __('site.home.events_std') }}</span>
                                            <span class="font-bold text-slate-800">{{ format_price($event->price_normal) }}</span>
                                        </div>

                                        @if ($event->price_verified && $event->price_verified !== $event->price_normal)
                                            <div class="flex flex-col items-end">
                                                <span class="text-[10px] uppercase font-bold text-emerald-600">{{ __('site.home.events_verified') }}</span>
                                                <span class="inline-flex items-center gap-1 font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded-lg text-xs">
                                                    {{ format_price($event->price_verified) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-border py-16 text-center text-muted col-span-full">
                            {{ __('site.home.events_none') }}
                        </div>
                    @endforelse
                </div>

                {{-- Join the Next Event Bottom CTA Banner --}}
                <div class="mt-8 rounded-2xl bg-gradient-to-r from-[#00379D] via-[#0F3459] to-[#082e6e] p-6 sm:p-8 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="space-y-1.5 text-center md:text-left relative z-10">
                        <h3 class="text-lg sm:text-xl font-black tracking-tight">{{ __('site.home.events_cta_title') }}</h3>
                        <p class="text-xs sm:text-sm text-slate-200 max-w-xl font-medium">{{ __('site.home.events_cta_desc') }}</p>
                    </div>
                    <a
                        href="/events"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-2.5 text-xs sm:text-sm font-extrabold text-primary shadow-md hover:bg-slate-50 active:scale-95 transition-all shrink-0 cursor-pointer relative z-10"
                    >
                        <span>{{ __('site.home.events_cta_btn') }}</span>
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>
            </div>
        </section>

        {{-- How it works --}}
        <section class="mx-auto max-w-7xl px-6 py-9 lg:py-8">
            <div class="mb-6 max-w-2xl">
                <p class="text-sm font-semibold text-primary">{{ __('site.home.how_label') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">{{ __('site.home.how_title') }}</h2>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                @foreach ([
                    ['step' => '1', 'title' => __('site.home.step1_title'), 'detail' => __('site.home.step1_desc')],
                    ['step' => '2', 'title' => __('site.home.step2_title'), 'detail' => __('site.home.step2_desc')],
                    ['step' => '3', 'title' => __('site.home.step3_title'), 'detail' => __('site.home.step3_desc')],
                ] as $item)
                    <div class="glass-card p-7">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-semibold text-white">{{ $item['step'] }}</div>
                        <h3 class="mt-5 text-lg font-semibold text-foreground">{{ $item['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $item['detail'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.app>
