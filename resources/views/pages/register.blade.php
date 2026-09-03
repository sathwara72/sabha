@php
    $locale = app()->getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" class="h-full antialiased {{ $locale === 'gu' ? 'lang-gu' : '' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ __('site.register_view.meta_title') }} | SABHA Business Network</title>
    <meta name="description" content="{{ __('site.register_view.hero_desc') }}" />
    <meta name="robots" content="noindex, nofollow" />

    <link rel="icon" href="{{ asset('logo2.png') }}" />
    <link rel="shortcut icon" href="{{ asset('logo2.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('logo2.png') }}" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-screen w-screen overflow-hidden bg-slate-950 font-outfit text-foreground antialiased">
    <div class="h-full w-full flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden">
        {{-- ========================================================================= --}}
        {{-- LEFT COLUMN: Distinctive Onboarding & 3-Step Journey Showcase --}}
        {{-- ========================================================================= --}}
        <div class="relative hidden lg:flex lg:w-[46%] xl:w-[48%] h-full flex-col justify-between overflow-hidden bg-[#061727] p-7 xl:p-10 text-white shrink-0">
            {{-- Ambient Decorative Lighting Orbs (Fresh Emerald & Cyan Tint) --}}
            <div class="pointer-events-none absolute -left-20 -top-20 h-[380px] w-[380px] rounded-full bg-gradient-to-tr from-emerald-600/25 via-teal-500/20 to-blue-600/20 blur-[90px]"></div>
            <div class="pointer-events-none absolute bottom-0 right-0 h-[380px] w-[380px] rounded-full bg-gradient-to-br from-blue-700/25 via-indigo-600/20 to-transparent blur-[100px]"></div>

            {{-- Blueprint Grid Watermark --}}
            <div class="pointer-events-none absolute inset-0 opacity-[0.035]" style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>

            {{-- TOP: Brand Header --}}
            <div class="relative z-10">
                <a href="/" class="group inline-flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-11 w-11 rounded-full object-contain transition-all duration-300 group-hover:scale-105 shadow-sm" />
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-black tracking-tight text-white">SABHA</span>
                            <span class="rounded-md bg-emerald-500/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-300 border border-emerald-400/30">{{ __('site.register_view.badge') }}</span>
                        </div>
                        <span class="block text-xs font-semibold text-blue-200/80 tracking-wide">{{ __('site.login_view.network_tag') }}</span>
                    </div>
                </a>
            </div>

            {{-- MIDDLE: Unique 3-Step Membership Journey Roadmap --}}
            <div class="relative z-10 my-auto py-4 max-w-lg space-y-5">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-500/10 px-3.5 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/25 backdrop-blur-sm mb-3">
                        <x-icon name="sparkles" class="h-3.5 w-3.5 text-amber-400" />
                        <span>{{ __('site.register_view.journey_title') }}</span>
                    </div>

                    <h1 class="text-2xl xl:text-[32px] font-black tracking-tight text-white leading-tight">
                        {{ __('site.register_view.hero_title_1') }} <span class="bg-gradient-to-r from-emerald-300 via-teal-200 to-cyan-200 bg-clip-text text-transparent">{{ __('site.register_view.hero_title_2') }}</span>.
                    </h1>

                    <p class="mt-2 text-xs xl:text-sm text-blue-100/80 leading-relaxed font-normal">
                        {{ __('site.register_view.hero_desc') }}
                    </p>
                </div>

                {{-- Connected 3-Step Journey Timeline Card --}}
                <div class="rounded-2xl border border-white/15 bg-gradient-to-br from-white/[0.12] to-white/[0.03] p-4 xl:p-5 backdrop-blur-xl shadow-xl space-y-3.5">
                    {{-- Step 1 --}}
                    <div class="flex items-start gap-3.5 group">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-500/25 text-emerald-300 border border-emerald-400/30 text-xs font-black shadow-sm">
                            1
                        </div>
                        <div>
                            <h4 class="text-xs xl:text-sm font-bold text-white">{{ __('site.register_view.step_1_title') }}</h4>
                            <p class="text-[11px] text-blue-200/80 mt-0.5 leading-relaxed">{{ __('site.register_view.step_1_desc') }}</p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="flex items-start gap-3.5 group">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-cyan-500/25 text-cyan-300 border border-cyan-400/30 text-xs font-black shadow-sm">
                            2
                        </div>
                        <div>
                            <h4 class="text-xs xl:text-sm font-bold text-white">{{ __('site.register_view.step_2_title') }}</h4>
                            <p class="text-[11px] text-blue-200/80 mt-0.5 leading-relaxed">{{ __('site.register_view.step_2_desc') }}</p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="flex items-start gap-3.5 group">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-500/25 text-indigo-300 border border-indigo-400/30 text-xs font-black shadow-sm">
                            3
                        </div>
                        <div>
                            <h4 class="text-xs xl:text-sm font-bold text-white">{{ __('site.register_view.step_3_title') }}</h4>
                            <p class="text-[11px] text-blue-200/80 mt-0.5 leading-relaxed">{{ __('site.register_view.step_3_desc') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Member Perks Badges --}}
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/5 px-2.5 py-1 text-[10px] font-medium text-emerald-200 border border-emerald-400/20">
                        <x-icon name="check-circle-2" class="h-3 w-3 text-emerald-400" />
                        <span>{{ __('site.register_view.perk_1') }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/5 px-2.5 py-1 text-[10px] font-medium text-cyan-200 border border-cyan-400/20">
                        <x-icon name="check-circle-2" class="h-3 w-3 text-cyan-400" />
                        <span>{{ __('site.register_view.perk_2') }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/5 px-2.5 py-1 text-[10px] font-medium text-indigo-200 border border-indigo-400/20">
                        <x-icon name="check-circle-2" class="h-3 w-3 text-indigo-400" />
                        <span>{{ __('site.register_view.perk_3') }}</span>
                    </span>
                </div>
            </div>

            {{-- BOTTOM: Trust Statement --}}
            <!-- <div class="relative z-10 flex items-center justify-between border-t border-white/10 pt-3.5 text-xs text-blue-200/70">
                <div class="flex items-center gap-2 font-medium">
                    <x-icon name="award" class="h-4 w-4 text-amber-400" />
                    <span>{{ __('site.login_view.motto') }}</span>
                </div>
                <span>&copy; {{ date('Y') }} SABHA • {{ __('site.login_view.rights') }}</span>
            </div> -->
        </div>

        {{-- ========================================================================= --}}
        {{-- RIGHT COLUMN: Register Flow Container (Scrollable Form on Demand) --}}
        {{-- ========================================================================= --}}
        <div class="relative flex flex-1 h-full flex-col justify-between overflow-y-auto bg-slate-50 px-5 py-5 sm:px-10 lg:px-8 xl:px-12">
            {{-- Top Navigation: Back to Home + Language Switcher --}}
            <div class="flex items-center justify-between pb-2">
                <a href="/" class="group inline-flex items-center gap-1.5 rounded-xl border border-slate-200/90 bg-white px-3 py-1.5 text-xs font-bold text-slate-600 transition-all hover:border-slate-300 hover:text-slate-900 shadow-sm">
                    <x-icon name="arrow-left" class="h-3.5 w-3.5 transition-transform group-hover:-translate-x-0.5" />
                    <span>{{ __('site.login_view.back_to_site') }}</span>
                </a>

                {{-- Modern Segmented Language Switcher --}}
                <div class="flex items-center gap-1 bg-slate-200/70 p-0.5 rounded-xl border border-slate-300/50 text-xs font-bold shadow-inner">
                    <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-0.5 rounded-lg transition-all duration-200 select-none {{ $locale === 'en' ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">English</a>
                    <a href="{{ route('lang.switch', 'gu') }}" class="px-2.5 py-0.5 rounded-lg transition-all duration-200 select-none {{ $locale === 'gu' ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">ગુજરાતી</a>
                </div>
            </div>

            {{-- Main Form Container --}}
            <div class="mx-auto w-full max-w-xl my-auto py-2">
                {{-- Mobile Brand Logo Header (shown only on mobile/tablet) --}}
                <div class="mb-4 text-center lg:hidden">
                    <a href="/" class="inline-flex items-center gap-2.5 mb-1 group">
                        <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-10 w-10 rounded-full object-contain shadow-xs" />
                        <div class="text-left">
                            <span class="block text-xl font-black tracking-tight text-primary-dark">SABHA</span>
                            <span class="block text-[10px] font-bold text-muted uppercase tracking-wider">{{ __('site.login_view.network_tag') }}</span>
                        </div>
                    </a>
                </div>

                {{-- Livewire Component --}}
                @livewire('auth.register')
            </div>

            {{-- Bottom Security Trust Stamp --}}
            <!-- <div class="pt-3 pb-1 text-center">
                <p class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-400">
                    <x-icon name="shield-check" class="h-3.5 w-3.5 text-emerald-600" />
                    <span>{{ __('site.login_view.ssl_secure') }}</span>
                </p>
            </div> -->
        </div>
    </div>

    @livewireScripts
</body>
</html>
