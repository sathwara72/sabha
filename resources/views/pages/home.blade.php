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
                            <a href="/profile" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-7 py-3.5 text-base font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] sm:w-auto cursor-pointer">
                                {{ __('site.nav.profile') }} <x-icon name="arrow-right" class="h-[18px] w-[18px]" />
                            </a>
                        @else
                            <button type="button" x-on:click="$store.auth.openRegister()" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-7 py-3.5 text-base font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] sm:w-auto cursor-pointer">
                                {{ __('site.nav.register') }} <x-icon name="arrow-right" class="h-[18px] w-[18px]" />
                            </button>
                        @endauth
                        <a href="/events" class="inline-flex w-full items-center justify-center rounded-xl border border-border bg-white px-7 py-3.5 text-base font-semibold text-foreground transition-colors hover:bg-surface sm:w-auto cursor-pointer">
                            {{ __('site.hero.cta_events') }}
                        </a>
                    </div>
                </div>

                <div
                    x-data="{ current: 0 }"
                    x-init="setInterval(() => { current = (current + 1) % {{ count($heroImages) }} }, 5000)"
                    class="relative h-[280px] w-full overflow-hidden rounded-2xl border border-border bg-muted sm:h-[360px] lg:h-[420px] shadow-lg"
                >
                    @foreach ($heroImages as $i => $image)
                        <img
                            src="{{ $image }}"
                            alt="Sabha Event"
                            x-show="current === {{ $i }}"
                            x-transition:enter="transition ease-out duration-[600ms]"
                            x-transition:enter-start="opacity-0 scale-105"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-[600ms]"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute inset-0 h-full w-full object-cover"
                        />
                    @endforeach

                    {{-- Navigation overlays --}}
                    <div class="absolute inset-x-0 bottom-4 flex justify-center gap-2 z-10">
                        @foreach ($heroImages as $i => $image)
                            <button
                                x-on:click="current = {{ $i }}"
                                :class="current === {{ $i }} ? 'bg-white w-6' : 'bg-white/50 w-2'"
                                class="h-2 rounded-full transition-all duration-300"
                                aria-label="Go to slide {{ $i + 1 }}"
                            ></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- Stats --}}
        <section class="border-b border-border bg-surface">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-8 px-6 py-14 text-center sm:grid-cols-3">
                @foreach ($stats as $stat)
                    <div>
                        <p class="text-4xl font-bold text-foreground sm:text-5xl">{{ $stat->value }}</p>
                        <p class="mt-2 text-sm font-medium text-muted">{{ $translateStatLabel($stat->label) }}</p>
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

        {{-- Events --}}
        <section class="border-y border-border bg-surface">
            <div class="mx-auto max-w-7xl px-6 py-9 lg:py-8">
                <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold text-primary">{{ __('site.home.events_label') }}</p>
                        <h2 class="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">{{ __('site.home.events_title') }}</h2>
                    </div>
                    <a href="/gallery" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary transition-colors hover:opacity-80">
                        {{ __('site.home.events_view_gallery') }} <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($events as $event)
                        <div class="glass-card group overflow-hidden p-0 hover:shadow-md transition-shadow cursor-pointer">
                            <a href="/events/{{ $event->id }}" class="block">
                                <div class="relative h-40 w-full overflow-hidden">
                                    <x-safe-image
                                        :src="media_url($event->image)"
                                        :alt="$event->title"
                                        :title="$event->title"
                                        :date="$event->date"
                                        fallback-type="event"
                                        img-class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    />
                                </div>
                                <div class="p-4.5 space-y-2">
                                    <h4 class="text-sm font-bold text-foreground group-hover:text-primary transition-colors line-clamp-1">{{ $event->title }}</h4>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted">
                                        <span class="inline-flex items-center gap-1">
                                            <x-icon name="calendar" class="h-3 w-3 text-primary" />
                                            {{ $event->date->format('M j, Y') }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 truncate max-w-[150px]">
                                            <x-icon name="map-pin" class="h-3 w-3 text-primary" />
                                            {{ explode(',', $event->location)[0] }}
                                        </span>
                                    </div>

                                    <div class="pt-2 border-t border-border flex items-center justify-between text-[12px] font-semibold">
                                        <span class="text-muted">
                                            {{ __('site.home.events_std') }}: <strong class="text-foreground">{{ $event->price_normal ?: '₹1,499' }}</strong>
                                        </span>
                                        @if ($event->price_verified && $event->price_verified !== $event->price_normal)
                                            <span class="text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded font-bold">
                                                {{ __('site.home.events_verified') }}: {{ $event->price_verified }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach

                    @if ($events->isEmpty())
                        <div class="rounded-xl border border-dashed border-border py-16 text-center text-muted md:col-span-2">
                            {{ __('site.home.events_none') }}
                        </div>
                    @endif

                    {{-- CTA card --}}
                    <div class="glass-card flex flex-col justify-between p-7">
                        <div>
                            <h3 class="text-xl font-bold text-foreground">{{ __('site.home.events_cta_title') }}</h3>
                            <p class="mt-3 text-sm leading-relaxed text-muted">{{ __('site.home.events_cta_desc') }}</p>
                        </div>
                        <a href="/events" class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white transition-all hover:opacity-90 active:scale-[0.98]">
                            {{ __('site.home.events_cta_btn') }} <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                    </div>
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

        {{-- Interactive FAQ Section --}}
        <section class="mx-auto max-w-4xl px-6 py-8 border-t border-border" x-data="{ activeFaq: null }">
            <div class="text-center mb-6">
                <p class="text-sm font-semibold text-primary uppercase tracking-wider">{{ __('site.home.faq_label') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">{{ __('site.home.faq_title') }}</h2>
            </div>

            <div class="space-y-4">
                @foreach ([
                    ['q' => __('site.home.faq_1_q'), 'a' => __('site.home.faq_1_a')],
                    ['q' => __('site.home.faq_2_q'), 'a' => __('site.home.faq_2_a')],
                    ['q' => __('site.home.faq_3_q'), 'a' => __('site.home.faq_3_a')],
                    ['q' => __('site.home.faq_4_q'), 'a' => __('site.home.faq_4_a')],
                ] as $i => $item)
                    <div class="glass-card overflow-hidden border border-border/80 transition-colors">
                        <button
                            x-on:click="activeFaq = activeFaq === {{ $i }} ? null : {{ $i }}"
                            class="w-full flex items-center justify-between p-5 text-left font-bold text-base text-foreground outline-none"
                        >
                            <span>{{ $item['q'] }}</span>
                            <span class="text-primary font-bold text-xl transition-transform duration-200" :class="activeFaq === {{ $i }} ? 'rotate-45' : ''">+</span>
                        </button>
                        <div x-show="activeFaq === {{ $i }}" x-collapse x-cloak>
                            <div class="p-5 pt-0 border-t border-border/10 text-sm leading-relaxed text-muted font-medium bg-surface/30">
                                {{ $item['a'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.app>
