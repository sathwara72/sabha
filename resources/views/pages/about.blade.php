<x-layouts.app :title="__('site.about.title') . ' | Sabha'" :description="__('site.about.subtitle')">
    @php
        $values = [
            ['tKey' => 'value_1', 'icon' => 'shield-check', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
            ['tKey' => 'value_2', 'icon' => 'users', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
            ['tKey' => 'value_3', 'icon' => 'heart', 'color' => 'text-rose-600', 'bg' => 'bg-rose-50'],
            ['tKey' => 'value_4', 'icon' => 'trending-up', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50'],
        ];
        $milestones = [
            ['year' => '2024', 'tKey' => 'milestone_1'],
            ['year' => '2025', 'tKey' => 'milestone_2'],
            ['year' => '2026', 'tKey' => 'milestone_3'],
        ];
    @endphp

    <div class="bg-background">
        <x-page-header :kicker="__('site.about.kicker')" :title="__('site.about.title')" :subtitle="__('site.about.subtitle')" />

        {{-- Mission & Impact --}}
        <section class="mx-auto max-w-7xl px-6 py-20 lg:py-5">
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-8">
                <div class="group relative aspect-[4/3] overflow-hidden rounded-2xl border border-border shadow-lg animate-fade-in">
                    <img
                        src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=1000"
                        alt="SABHA Community Mixer"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-103"
                    />
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-t from-slate-900/80 via-slate-900/35 to-transparent p-10 text-center">
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 text-white backdrop-blur-md">
                            <x-icon name="users" class="h-6 w-6" />
                        </div>
                        <p class="text-base font-bold leading-relaxed text-white">{{ __('site.about.mixers_panels') }}</p>
                    </div>
                </div>

                <div class="space-y-6 animate-fade-in">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary">{{ __('site.about.mission_label') }}</span>
                    <h2 class="text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">{{ __('site.about.mission_title') }}</h2>
                    <p class="text-sm leading-relaxed text-muted font-medium">{{ __('site.about.mission_desc') }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-border/80">
                        <div x-data="animatedCounter('{{ $membersStat }}')" class="glass-card flex items-center gap-4 p-5 rounded-2xl border border-border bg-surface/50 shadow-sm transition-all hover:border-primary/20">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                                <x-icon name="users" class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-foreground sm:text-3xl leading-none" x-text="displayValue">{{ $membersStat }}</p>
                                <p class="mt-1.5 text-xs font-bold text-muted uppercase tracking-wider">{{ __('site.about.verified_members') }}</p>
                            </div>
                        </div>

                        <div x-data="animatedCounter('{{ $businessExchangedStat }}')" class="glass-card flex items-center gap-4 p-5 rounded-2xl border border-border bg-surface/50 shadow-sm transition-all hover:border-primary/20">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                                <x-icon name="trending-up" class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-foreground sm:text-3xl leading-none" x-text="displayValue">{{ $businessExchangedStat }}</p>
                                <p class="mt-1.5 text-xs font-bold text-muted uppercase tracking-wider">{{ __('site.about.business_exchanged') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Values Grid --}}
        <section class="border-y border-border bg-surface">
            <div class="mx-auto max-w-7xl px-3 py-20 lg:py-5">
                <div class="mb-3 text-center max-w-2xl mx-auto">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary">{{ __('site.about.values_label') }}</span>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">{{ __('site.about.values_title') }}</h2>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($values as $v)
                        <div class="glass-card p-7 flex flex-col items-start">
                            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl {{ $v['bg'] }} {{ $v['color'] }}">
                                <x-icon :name="$v['icon']" class="h-[22px] w-[22px]" />
                            </div>
                            <h3 class="text-base font-bold text-foreground">{{ __('site.about.' . $v['tKey'] . '_title') }}</h3>
                            <p class="mt-2.5 text-xs leading-relaxed text-muted flex-1">{{ __('site.about.' . $v['tKey'] . '_desc') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Evolution Timeline --}}
        <section class="mx-auto max-w-7xl px-6 py-20 lg:py-5 border-b border-border">
            <div class="mb-3 text-center max-w-2xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-wider text-primary">{{ __('site.about.timeline_label') }}</span>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">{{ __('site.about.timeline_title') }}</h2>
            </div>

            <div class="relative border-l border-primary/25 ml-4 md:ml-32 space-y-12">
                @foreach ($milestones as $m)
                    <div class="relative pl-8 md:pl-10">
                        <div class="hidden md:flex absolute right-full mr-10 top-0.5 text-right flex-col">
                            <span class="text-2xl font-extrabold text-primary">{{ $m['year'] }}</span>
                            <span class="text-[12px] font-bold text-muted uppercase">{{ __('site.about.milestone') }}</span>
                        </div>

                        <div class="absolute -left-1.5 top-2.5 h-3.5 w-3.5 rounded-full border-2 border-primary bg-white z-10"></div>

                        <div>
                            <span class="inline-block md:hidden text-lg font-extrabold text-primary mb-1">{{ $m['year'] }}</span>
                            <h3 class="text-base font-bold text-foreground">{{ __('site.about.' . $m['tKey'] . '_title') }}</h3>
                            <p class="mt-2 text-xs leading-relaxed text-muted font-medium max-w-3xl">{{ __('site.about.' . $m['tKey'] . '_desc') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Leadership Board --}}
        <section class="mx-auto max-w-7xl px-6 py-20 lg:py-5">
            <div class="mb-3 text-center max-w-2xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-wider text-primary">{{ __('site.about.leadership_label') }}</span>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">{{ __('site.about.leadership_title') }}</h2>
                <p class="mt-4 text-xs text-muted font-medium">{{ __('site.about.leadership_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @if ($trustees->isNotEmpty())
                    @foreach ($trustees as $trustee)
                        <div class="glass-card p-5 text-center flex flex-col items-center border border-slate-200 rounded-2xl bg-white shadow-2xs hover:shadow-md transition-all">
                            <div class="h-20 w-20 rounded-full overflow-hidden border-2 border-primary/20 shadow-sm mb-3">
                                <x-safe-image
                                    :src="media_url($trustee->user?->avatar)"
                                    :alt="$trustee->user?->name ?? 'Trustee'"
                                    :title="$trustee->user?->name ?? 'Trustee'"
                                    fallback-type="avatar"
                                    img-class="h-full w-full object-cover"
                                />
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900 line-clamp-1">{{ $trustee->user?->name ?: 'Community Leader' }}</h3>
                            <p class="text-xs font-bold text-primary mt-0.5">{{ $trustee->position ?: 'Trustee' }}</p>
                            <p class="text-xs text-slate-500 font-semibold mt-1 truncate max-w-full">
                                {{ $trustee->user?->business?->name ?: ($trustee->user?->city ?: 'SABHA Network') }}
                            </p>
                        </div>
                    @endforeach
                @else
                    @foreach ($fallbackTeam as $member)
                        @php
                            $hasKey = !empty($member['tKey'] ?? null);
                            $name = $hasKey ? __('site.about.' . $member['tKey'] . '_name') : ($member['name'] ?? '');
                            $role = $hasKey ? __('site.about.' . $member['tKey'] . '_role') : ($member['role'] ?? '');
                            $company = $hasKey ? __('site.about.' . $member['tKey'] . '_org') : ($member['company'] ?? $member['org'] ?? '');
                        @endphp
                        <div class="glass-card p-5 text-center flex flex-col items-center border border-slate-200 rounded-2xl bg-white shadow-2xs">
                            <img
                                src="{{ media_url($member['avatar'] ?? null) }}"
                                alt="{{ $name }}"
                                class="h-20 w-20 rounded-full object-cover border-2 border-primary/20 shadow-sm mb-3"
                            />
                            <h3 class="text-sm font-extrabold text-slate-900">{{ $name }}</h3>
                            <p class="text-xs font-bold text-primary mt-0.5">{{ $role }}</p>
                            <p class="text-xs text-slate-500 font-semibold mt-1 truncate max-w-full">{{ $company }}</p>
                        </div>
                    @endforeach
                @endif
            </div>
        </section>

        {{-- Interactive FAQ Section --}}
        <section class="mx-auto max-w-4xl px-6 py-12 border-t border-border" x-data="{ activeFaq: null }">
            <div class="text-center mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-primary">{{ __('site.about.faq_label') }}</span>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">{{ __('site.about.faq_title') }}</h2>
            </div>

            <div class="space-y-3.5">
                @foreach ([
                    ['q' => __('site.about.faq_1_q'), 'a' => __('site.about.faq_1_a')],
                    ['q' => __('site.about.faq_2_q'), 'a' => __('site.about.faq_2_a')],
                    ['q' => __('site.about.faq_3_q'), 'a' => __('site.about.faq_3_a')],
                    ['q' => __('site.about.faq_4_q'), 'a' => __('site.about.faq_4_a')],
                ] as $i => $item)
                    <div class="glass-card overflow-hidden border border-slate-200/90 rounded-2xl bg-white shadow-2xs transition-all hover:border-primary/30">
                        <button
                            type="button"
                            x-on:click="activeFaq = activeFaq === {{ $i }} ? null : {{ $i }}"
                            class="w-full flex items-center justify-between p-4 sm:p-5 text-left font-bold text-sm sm:text-base text-slate-900 outline-none cursor-pointer"
                        >
                            <span>{{ $item['q'] }}</span>
                            <span class="text-primary font-black text-xl transition-transform duration-200 shrink-0 ml-3" :class="activeFaq === {{ $i }} ? 'rotate-45' : ''">+</span>
                        </button>
                        <div x-show="activeFaq === {{ $i }}" x-collapse x-cloak>
                            <div class="p-4 sm:p-5 pt-0 border-t border-slate-100 text-xs sm:text-sm leading-relaxed text-slate-600 font-medium bg-slate-50/40">
                                {{ $item['a'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- CTA --}}
        <section class="mx-auto max-w-7xl px-6 pb-5">
            <div class="rounded-2xl border border-border bg-primary px-8 py-14 text-center text-white lg:px-16 shadow-lg">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ __('site.about.cta_title') }}</h2>
                <p class="mx-auto mt-3 max-w-xl text-sm text-white/80 leading-relaxed">{{ __('site.about.cta_subtitle') }}</p>
                @auth
                    <a href="/profile" class="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-white px-7 py-3.5 text-sm font-semibold text-primary shadow-md transition-all hover:opacity-90 active:scale-[0.98]">
                        {{ __('site.nav.profile') }} <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                @else
                    <a href="/register" class="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-white px-7 py-3.5 text-sm font-semibold text-primary shadow-md transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer">
                        {{ __('site.about.cta_btn') }} <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                @endauth
            </div>
        </section>
    </div>
</x-layouts.app>
