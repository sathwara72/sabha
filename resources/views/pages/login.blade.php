@php
    $locale = app()->getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" class="h-full antialiased {{ $locale === 'gu' ? 'lang-gu' : '' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ __('site.login_view.meta_title') }} | SABHA Business Network</title>
    <meta name="description" content="{{ __('site.login_view.hero_desc') }}" />
    <meta name="robots" content="noindex, nofollow" />

    <link rel="icon" href="{{ asset('logo2.png') }}" />
    <link rel="shortcut icon" href="{{ asset('logo2.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('logo2.png') }}" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-screen w-screen overflow-hidden bg-slate-950 font-outfit text-foreground antialiased">
    <div
        class="h-full w-full flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden"
        x-data="{
            email: '',
            password: '',
            remember: true,
            showPassword: false,
            error: '',
            loading: false,
            async submit() {
                this.error = '';
                this.loading = true;
                try {
                    const res = await fetch('/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({
                            email: this.email,
                            password: this.password,
                            remember: this.remember,
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || '{{ __('site.auth.login_btn') }} failed.');
                    }
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.href = '/';
                    }
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.loading = false;
                }
            },
        }"
    >
        {{-- ========================================================================= --}}
        {{-- LEFT COLUMN: Clean, Minimal & Fully Localized Community Showcase --}}
        {{-- ========================================================================= --}}
        <div class="relative hidden lg:flex lg:w-[48%] xl:w-[50%] h-full flex-col justify-between overflow-hidden bg-[#071326] p-8 xl:p-12 text-white">
            {{-- Ambient Decorative Lighting Orbs --}}
            <div class="pointer-events-none absolute -left-20 -top-20 h-[350px] w-[350px] rounded-full bg-gradient-to-tr from-blue-600/30 to-cyan-400/20 blur-[90px]"></div>
            <div class="pointer-events-none absolute bottom-0 right-0 h-[350px] w-[350px] rounded-full bg-gradient-to-br from-indigo-600/25 via-blue-700/20 to-transparent blur-[100px]"></div>

            {{-- Blueprint Grid Watermark --}}
            <div class="pointer-events-none absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 32px 32px;"></div>

            {{-- TOP: Brand Header --}}
            <div class="relative z-10">
                <a href="/" class="group inline-flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-11 w-11 rounded-full object-contain transition-all duration-300 group-hover:scale-105 shadow-sm" />
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-black tracking-tight text-white">SABHA</span>
                            <span class="rounded-md bg-blue-500/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-blue-300 border border-blue-400/30">{{ __('site.login_view.official_badge') }}</span>
                        </div>
                        <span class="block text-xs font-semibold text-blue-200/80 tracking-wide">{{ __('site.login_view.network_tag') }}</span>
                    </div>
                </a>
            </div>

            {{-- MIDDLE: Minimal & Impactful Localized Message --}}
            <div class="relative z-10 my-auto py-6 max-w-lg space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-blue-500/10 px-3.5 py-1 text-xs font-bold text-blue-300 border border-blue-400/20 backdrop-blur-sm mb-4">
                        <x-icon name="sparkles" class="h-3.5 w-3.5 text-amber-400" />
                        <span>{{ __('site.login_view.badge') }}</span>
                    </div>

                    <h1 class="text-3xl xl:text-4xl font-black tracking-tight text-white leading-tight">
                        {{ __('site.login_view.hero_title_1') }} <span class="bg-gradient-to-r from-blue-300 via-cyan-200 to-indigo-200 bg-clip-text text-transparent">{{ __('site.login_view.hero_title_2') }}</span>.
                    </h1>

                    <p class="mt-3 text-sm text-blue-100/80 leading-relaxed font-normal">
                        {{ __('site.login_view.hero_desc') }}
                    </p>
                </div>

                {{-- Clean 3-Item Feature List --}}
                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm transition-all hover:bg-white/10">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-500/20 text-blue-300">
                            <x-icon name="building" class="h-4 w-4" />
                        </div>
                        <span class="text-xs xl:text-sm font-medium text-blue-100">{{ __('site.login_view.feature_1_title') }}</span>
                    </div>

                    <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm transition-all hover:bg-white/10">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-300">
                            <x-icon name="users" class="h-4 w-4" />
                        </div>
                        <span class="text-xs xl:text-sm font-medium text-blue-100">{{ __('site.login_view.feature_2_title') }}</span>
                    </div>

                    <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm transition-all hover:bg-white/10">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-purple-500/20 text-purple-300">
                            <x-icon name="ticket" class="h-4 w-4" />
                        </div>
                        <span class="text-xs xl:text-sm font-medium text-blue-100">{{ __('site.login_view.feature_3_title') }}</span>
                    </div>
                </div>
            </div>

            {{-- BOTTOM: Trust Statement & Copyright --}}
            <!-- <div class="relative z-10 flex items-center justify-between border-t border-white/10 pt-4 text-xs text-blue-200/70">
                <div class="flex items-center gap-2 font-medium">
                    <x-icon name="award" class="h-4 w-4 text-amber-400" />
                    <span>{{ __('site.login_view.motto') }}</span>
                </div>
                <span>&copy; {{ date('Y') }} SABHA • {{ __('site.login_view.rights') }}</span>
            </div> -->
        </div>

        {{-- ========================================================================= --}}
        {{-- RIGHT COLUMN: Fully Localized Auth Form (100% Fit in Viewport) --}}
        {{-- ========================================================================= --}}
        <div class="relative flex flex-1 h-full flex-col justify-between overflow-y-auto lg:overflow-hidden bg-slate-50 px-6 py-5 sm:px-10 lg:px-8 xl:px-14">
            {{-- Top Navigation: Back to Home + Language Switcher --}}
            <div class="flex items-center justify-between">
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

            {{-- Main Form Card Container --}}
            <div class="mx-auto w-full max-w-md my-auto py-2">
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

                {{-- Card Header --}}
                <div class="mb-4">
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-0.5 text-[11px] font-bold text-primary mb-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-primary animate-pulse"></span>
                        <span>{{ __('site.login_view.portal_badge') }}</span>
                    </div>
                    <h2 class="text-2xl xl:text-[26px] font-black tracking-tight text-slate-900">
                        {{ __('site.login_view.welcome_title') }}
                    </h2>
                    <p class="mt-0.5 text-xs text-slate-500 leading-relaxed font-medium">
                        {{ __('site.login_view.welcome_subtitle') }}
                    </p>
                </div>

                {{-- Error Alert Banner --}}
                <div
                    x-show="error"
                    x-cloak
                    x-transition
                    class="mb-3 flex items-start gap-2.5 rounded-xl bg-red-50 border border-red-200/80 p-2.5 text-xs font-semibold text-red-700 shadow-sm"
                >
                    <x-icon name="alert-circle" class="h-4 w-4 text-red-600 shrink-0 mt-0.5" />
                    <span x-text="error" class="leading-relaxed"></span>
                </div>

                {{-- Floating Form Card --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-5 sm:p-6 shadow-xl shadow-slate-200/40">
                    <form x-on:submit.prevent="submit" class="space-y-3.5">
                        {{-- Email Field --}}
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                {{ __('site.auth.email') }}
                            </label>
                            <div class="relative group">
                                <x-icon name="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 transition-colors group-focus-within:text-primary" />
                                <input
                                    type="email"
                                    required
                                    autofocus
                                    x-model="email"
                                    placeholder="{{ __('site.auth.email_placeholder') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none transition-all focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10"
                                />
                            </div>
                        </div>

                        {{-- Password Field --}}
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                    {{ __('site.auth.password') }}
                                </label>
                                <a href="/forgot-password" class="text-[11px] font-bold text-primary hover:underline transition-colors">
                                    {{ __('site.auth.forgot_password') }}
                                </a>
                            </div>
                            <div class="relative group">
                                <x-icon name="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 transition-colors group-focus-within:text-primary" />
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    x-model="password"
                                    placeholder="{{ __('site.auth.password_placeholder') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-10 text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none transition-all focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10"
                                />
                                <button
                                    type="button"
                                    x-on:click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1 text-slate-400 hover:text-slate-700 transition-colors"
                                    tabindex="-1"
                                    aria-label="Toggle password visibility"
                                >
                                    <x-icon name="eye" x-show="!showPassword" class="h-3.5 w-3.5" />
                                    <x-icon name="eye-off" x-show="showPassword" class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </div>

                        {{-- Keep Me Logged In --}}
                        <div class="flex items-center justify-between pt-0.5">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input
                                    type="checkbox"
                                    x-model="remember"
                                    class="h-3.5 w-3.5 rounded border-slate-300 text-primary focus:ring-primary/20 accent-primary"
                                />
                                <span class="text-xs font-semibold text-slate-600">{{ __('site.login_view.keep_signed_in') }}</span>
                            </label>
                        </div>

                        {{-- Submit Button --}}
                        <button
                            type="submit"
                            :disabled="loading"
                            class="group relative inline-flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-[#00379D] via-[#082e6e] to-[#0F3459] px-5 py-3 text-xs sm:text-sm font-extrabold text-white shadow-md shadow-primary/25 transition-all duration-200 hover:shadow-lg hover:shadow-primary/30 hover:opacity-95 active:scale-[0.99] disabled:opacity-60 cursor-pointer mt-1"
                        >
                            <span x-text="loading ? '{{ __('site.auth.logging_in') }}' : '{{ __('site.auth.login_btn') }}'"></span>
                            <x-icon name="arrow-right" x-show="!loading" class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                            <span x-show="loading" class="h-4 w-4 rounded-full border-2 border-white/30 border-t-white animate-spin"></span>
                        </button>
                    </form>

                    {{-- Not Registered Prompt --}}
                    <div class="mt-4 pt-3.5 border-t border-slate-100 text-center">
                        <p class="text-xs font-medium text-slate-500">
                            {{ __('site.auth.no_account') }}
                            <a href="/register" class="font-bold text-primary hover:underline ml-1">
                                {{ __('site.login_view.apply_membership') }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Bottom Security Trust Stamp --}}
            <!-- <div class="pt-2 text-center">
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
