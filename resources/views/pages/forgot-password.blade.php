@php
    $locale = app()->getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" class="h-full antialiased {{ $locale === 'gu' ? 'lang-gu' : '' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ __('site.forgot_view.meta_title') }} | SABHA Business Network</title>
    <meta name="description" content="{{ __('site.forgot_view.hero_desc') }}" />
    <meta name="robots" content="noindex, nofollow" />

    <link rel="icon" href="{{ asset('logo.png') }}" />
    <link rel="shortcut icon" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-screen w-screen overflow-hidden bg-slate-950 font-outfit text-foreground antialiased">
    <div
        class="h-full w-full flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden"
        x-data="{
            step: 'request',
            email: '',
            otp: '',
            password: '',
            confirmPassword: '',
            showPassword: false,
            showConfirmPassword: false,
            loading: false,
            resending: false,
            error: '',
            success: '',
            async sendOtp() {
                this.error = '';
                this.success = '';
                this.loading = true;
                try {
                    const res = await fetch('/forgot-password/send-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ email: this.email }),
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || '{{ __('site.auth.send_otp') }}');
                    this.success = data.message || '{{ __('site.auth.forgot_subtitle') }}';
                    this.step = 'verify';
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.loading = false;
                }
            },
            async resendOtp() {
                this.error = '';
                this.success = '';
                this.resending = true;
                try {
                    const res = await fetch('/forgot-password/send-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ email: this.email }),
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || '{{ __('site.auth.resend_otp') }}');
                    this.success = data.message || '{{ __('site.auth.resend_otp') }}';
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.resending = false;
                }
            },
            async resetPassword() {
                this.error = '';
                this.success = '';
                if (!this.otp || this.otp.trim().length !== 6) {
                    this.error = '{{ __('site.auth.invalid_otp') }}';
                    return;
                }
                if (this.password.length < 6) {
                    this.error = '{{ __('site.auth.short_password') }}';
                    return;
                }
                if (this.password !== this.confirmPassword) {
                    this.error = '{{ __('site.auth.passwords_not_match') }}';
                    return;
                }
                this.loading = true;
                try {
                    const res = await fetch('/forgot-password/reset', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ email: this.email, otp: this.otp.trim(), password: this.password }),
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || '{{ __('site.auth.reset_password_btn') }}');
                    this.success = data.message || '{{ __('site.auth.reset_success_desc') }}';
                    this.step = 'success';
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.loading = false;
                }
            },
        }"
    >
        {{-- ========================================================================= --}}
        {{-- LEFT COLUMN: Distinctive Account Recovery & Security Showcase --}}
        {{-- ========================================================================= --}}
        <div class="relative hidden lg:flex lg:w-[46%] xl:w-[48%] h-full flex-col justify-between overflow-hidden bg-[#071326] p-7 xl:p-10 text-white shrink-0">
            {{-- Ambient Decorative Lighting Orbs --}}
            <div class="pointer-events-none absolute -left-20 -top-20 h-[380px] w-[380px] rounded-full bg-gradient-to-tr from-blue-600/30 via-indigo-600/20 to-cyan-400/20 blur-[90px]"></div>
            <div class="pointer-events-none absolute bottom-0 right-0 h-[380px] w-[380px] rounded-full bg-gradient-to-br from-indigo-700/25 via-blue-800/20 to-transparent blur-[100px]"></div>

            {{-- Blueprint Grid Watermark --}}
            <div class="pointer-events-none absolute inset-0 opacity-[0.035]" style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>

            {{-- TOP: Brand Header --}}
            <div class="relative z-10">
                <a href="/" class="group inline-flex items-center gap-3.5">
                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 p-2 ring-1 ring-white/20 backdrop-blur-xl shadow-lg transition-all duration-300 group-hover:scale-105">
                        <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-9 w-9 rounded-full object-contain" />
                        <div class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 rounded-full bg-emerald-500 ring-2 ring-[#071326]"></div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-black tracking-tight text-white">SABHA</span>
                            <span class="rounded-md bg-blue-500/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-blue-300 border border-blue-400/30">{{ __('site.login_view.official_badge') }}</span>
                        </div>
                        <span class="block text-xs font-semibold text-blue-200/80 tracking-wide">{{ __('site.login_view.network_tag') }}</span>
                    </div>
                </a>
            </div>

            {{-- MIDDLE: Unique 3-Step Password Recovery Roadmap --}}
            <div class="relative z-10 my-auto py-4 max-w-lg space-y-5">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-blue-500/10 px-3.5 py-1 text-xs font-bold text-blue-300 border border-blue-400/25 backdrop-blur-sm mb-3">
                        <x-icon name="shield-check" class="h-3.5 w-3.5 text-cyan-400" />
                        <span>{{ __('site.forgot_view.badge') }}</span>
                    </div>

                    <h1 class="text-2xl xl:text-[32px] font-black tracking-tight text-white leading-tight">
                        {{ __('site.forgot_view.hero_title_1') }} <span class="bg-gradient-to-r from-blue-300 via-cyan-200 to-indigo-200 bg-clip-text text-transparent">{{ __('site.forgot_view.hero_title_2') }}</span>.
                    </h1>

                    <p class="mt-2 text-xs xl:text-sm text-blue-100/80 leading-relaxed font-normal">
                        {{ __('site.forgot_view.hero_desc') }}
                    </p>
                </div>

                {{-- Connected 3-Step Recovery Roadmap Card --}}
                <div class="rounded-2xl border border-white/15 bg-gradient-to-br from-white/[0.12] to-white/[0.03] p-4 xl:p-5 backdrop-blur-xl shadow-xl space-y-3.5">
                    {{-- Step 1 --}}
                    <div class="flex items-start gap-3.5 group">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-blue-500/25 text-blue-300 border border-blue-400/30 text-xs font-black shadow-sm" :class="step === 'request' ? 'ring-2 ring-blue-400 bg-blue-500/40 text-white' : ''">
                            1
                        </div>
                        <div>
                            <h4 class="text-xs xl:text-sm font-bold text-white">{{ __('site.forgot_view.step_1_title') }}</h4>
                            <p class="text-[11px] text-blue-200/80 mt-0.5 leading-relaxed">{{ __('site.forgot_view.step_1_desc') }}</p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="flex items-start gap-3.5 group">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-cyan-500/25 text-cyan-300 border border-cyan-400/30 text-xs font-black shadow-sm" :class="step === 'verify' ? 'ring-2 ring-cyan-400 bg-cyan-500/40 text-white' : ''">
                            2
                        </div>
                        <div>
                            <h4 class="text-xs xl:text-sm font-bold text-white">{{ __('site.forgot_view.step_2_title') }}</h4>
                            <p class="text-[11px] text-blue-200/80 mt-0.5 leading-relaxed">{{ __('site.forgot_view.step_2_desc') }}</p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="flex items-start gap-3.5 group">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-500/25 text-emerald-300 border border-emerald-400/30 text-xs font-black shadow-sm" :class="step === 'success' ? 'ring-2 ring-emerald-400 bg-emerald-500/40 text-white' : ''">
                            3
                        </div>
                        <div>
                            <h4 class="text-xs xl:text-sm font-bold text-white">{{ __('site.forgot_view.step_3_title') }}</h4>
                            <p class="text-[11px] text-blue-200/80 mt-0.5 leading-relaxed">{{ __('site.forgot_view.step_3_desc') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Security Trust Tags --}}
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/5 px-2.5 py-1 text-[10px] font-medium text-blue-200 border border-white/10">
                        <x-icon name="shield-check" class="h-3 w-3 text-emerald-400" />
                        <span>{{ __('site.forgot_view.security_badge') }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/5 px-2.5 py-1 text-[10px] font-medium text-blue-200 border border-white/10">
                        <x-icon name="lock" class="h-3 w-3 text-cyan-400" />
                        <span>Instant Password Update</span>
                    </span>
                </div>
            </div>

            {{-- BOTTOM: Motto & Copyright --}}
            <div class="relative z-10 flex items-center justify-between border-t border-white/10 pt-3.5 text-xs text-blue-200/70">
                <div class="flex items-center gap-2 font-medium">
                    <x-icon name="award" class="h-4 w-4 text-amber-400" />
                    <span>{{ __('site.login_view.motto') }}</span>
                </div>
                <span>&copy; {{ date('Y') }} SABHA • {{ __('site.login_view.rights') }}</span>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- RIGHT COLUMN: Password Reset Interactive Form Container --}}
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
                        <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-10 w-10 rounded-full object-contain shadow-md" />
                        <div class="text-left">
                            <span class="block text-xl font-black tracking-tight text-primary-dark">SABHA</span>
                            <span class="block text-[10px] font-bold text-muted uppercase tracking-wider">{{ __('site.login_view.network_tag') }}</span>
                        </div>
                    </a>
                </div>

                {{-- Card Dynamic Header --}}
                <div class="mb-4">
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-0.5 text-[11px] font-bold text-primary mb-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-primary animate-pulse"></span>
                        <span>{{ __('site.login_view.portal_badge') }}</span>
                    </div>
                    <h2 class="text-2xl xl:text-[26px] font-black tracking-tight text-slate-900">
                        <span x-show="step === 'request'">{{ __('site.auth.forgot_title') }}</span>
                        <span x-show="step === 'verify'">{{ __('site.auth.forgot_verify_title') }}</span>
                        <span x-show="step === 'success'">{{ __('site.auth.forgot_success_title') }}</span>
                    </h2>
                    <p class="mt-0.5 text-xs text-slate-500 leading-relaxed font-medium">
                        <span x-show="step === 'request'">{{ __('site.auth.forgot_subtitle') }}</span>
                        <span x-show="step === 'verify'">{{ __('site.auth.forgot_verify_subtitle') }} <strong class="text-slate-800" x-text="email"></strong></span>
                        <span x-show="step === 'success'">{{ __('site.auth.forgot_success_subtitle') }}</span>
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

                {{-- Success Banner for OTP Sent --}}
                <div
                    x-show="success && step === 'verify'"
                    x-cloak
                    x-transition
                    class="mb-3 flex items-start gap-2.5 rounded-xl bg-emerald-50 border border-emerald-200/80 p-2.5 text-xs font-semibold text-emerald-800 shadow-sm"
                >
                    <x-icon name="shield-check" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" />
                    <span x-text="success" class="leading-relaxed"></span>
                </div>

                {{-- Floating Form Card --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-5 sm:p-6 shadow-xl shadow-slate-200/40">
                    {{-- STEP 1: Request Email Form --}}
                    <form x-show="step === 'request'" x-on:submit.prevent="sendOtp" class="space-y-3.5">
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                {{ __('site.auth.email_address') }}
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

                        <button
                            type="submit"
                            :disabled="loading"
                            class="group relative inline-flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-[#00379D] via-[#082e6e] to-[#0F3459] px-5 py-3 text-xs sm:text-sm font-extrabold text-white shadow-md shadow-primary/25 transition-all duration-200 hover:shadow-lg hover:shadow-primary/30 hover:opacity-95 active:scale-[0.99] disabled:opacity-60 cursor-pointer mt-1"
                        >
                            <span x-text="loading ? '{{ __('site.auth.sending') }}' : '{{ __('site.auth.send_otp') }}'"></span>
                            <x-icon name="arrow-right" x-show="!loading" class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                            <span x-show="loading" class="h-4 w-4 rounded-full border-2 border-white/30 border-t-white animate-spin"></span>
                        </button>

                        <div class="pt-2 text-center border-t border-slate-100 mt-3">
                            <a href="/login" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-primary transition-colors">
                                <x-icon name="arrow-left" class="h-3.5 w-3.5" />
                                <span>{{ __('site.auth.back_to_login') }}</span>
                            </a>
                        </div>
                    </form>

                    {{-- STEP 2: Verify OTP and Set New Password --}}
                    <form x-show="step === 'verify'" x-cloak x-on:submit.prevent="resetPassword" class="space-y-3">
                        {{-- OTP Code Field --}}
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                    {{ __('site.auth.verify_code') }}
                                </label>
                                <button
                                    type="button"
                                    x-on:click="step = 'request'; error = ''; success = ''"
                                    class="text-[11px] font-bold text-primary hover:underline"
                                >
                                    {{ __('site.auth.change_email') }}
                                </button>
                            </div>
                            <div class="relative group">
                                <x-icon name="key" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 transition-colors group-focus-within:text-primary" />
                                <input
                                    type="text"
                                    required
                                    maxlength="6"
                                    placeholder="{{ __('site.auth.enter_otp_placeholder') }}"
                                    x-model="otp"
                                    x-on:input="otp = otp.replace(/\D/g, '')"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-xs sm:text-sm font-mono tracking-widest text-slate-900 placeholder:text-slate-400 placeholder:font-sans placeholder:tracking-normal outline-none transition-all focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10"
                                />
                            </div>
                            <div class="flex items-center justify-between pt-0.5">
                                <span class="text-slate-500 text-[11px]">{{ __('site.auth.didnt_receive') }}</span>
                                <button
                                    type="button"
                                    :disabled="resending"
                                    x-on:click="resendOtp"
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg border border-primary/20 bg-primary/5 text-[11px] font-bold text-primary hover:bg-primary/10 transition-colors disabled:opacity-50"
                                >
                                    <x-icon name="refresh-cw" class="h-3 w-3" x-bind:class="resending ? 'animate-spin' : ''" />
                                    <span x-text="resending ? '{{ __('site.auth.resending') }}' : '{{ __('site.auth.resend_otp') }}'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- New Password --}}
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                {{ __('site.auth.new_password') }}
                            </label>
                            <div class="relative group">
                                <x-icon name="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 transition-colors group-focus-within:text-primary" />
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    minlength="6"
                                    placeholder="{{ __('site.auth.min_chars') }}"
                                    x-model="password"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-10 text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none transition-all focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10"
                                />
                                <button
                                    type="button"
                                    x-on:click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1 text-slate-400 hover:text-slate-700 transition-colors"
                                    tabindex="-1"
                                >
                                    <x-icon name="eye" x-show="!showPassword" class="h-3.5 w-3.5" />
                                    <x-icon name="eye-off" x-show="showPassword" class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </div>

                        {{-- Confirm New Password --}}
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                {{ __('site.auth.confirm_new_password') }}
                            </label>
                            <div class="relative group">
                                <x-icon name="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 transition-colors group-focus-within:text-primary" />
                                <input
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    required
                                    minlength="6"
                                    placeholder="{{ __('site.auth.reenter_new_password') }}"
                                    x-model="confirmPassword"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-10 text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none transition-all focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10"
                                />
                                <button
                                    type="button"
                                    x-on:click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1 text-slate-400 hover:text-slate-700 transition-colors"
                                    tabindex="-1"
                                >
                                    <x-icon name="eye" x-show="!showConfirmPassword" class="h-3.5 w-3.5" />
                                    <x-icon name="eye-off" x-show="showConfirmPassword" class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="loading"
                            class="group relative inline-flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-[#00379D] via-[#082e6e] to-[#0F3459] px-5 py-3 text-xs sm:text-sm font-extrabold text-white shadow-md shadow-primary/25 transition-all duration-200 hover:shadow-lg hover:shadow-primary/30 hover:opacity-95 active:scale-[0.99] disabled:opacity-60 cursor-pointer mt-1"
                        >
                            <span x-text="loading ? '{{ __('site.auth.resetting') }}' : '{{ __('site.auth.reset_password_btn') }}'"></span>
                            <x-icon name="arrow-right" x-show="!loading" class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                            <span x-show="loading" class="h-4 w-4 rounded-full border-2 border-white/30 border-t-white animate-spin"></span>
                        </button>

                        <div class="pt-2 text-center border-t border-slate-100 mt-2">
                            <a href="/login" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-primary transition-colors">
                                <x-icon name="arrow-left" class="h-3.5 w-3.5" />
                                <span>{{ __('site.auth.back_to_login') }}</span>
                            </a>
                        </div>
                    </form>

                    {{-- STEP 3: Success Screen --}}
                    <div x-show="step === 'success'" x-cloak class="space-y-4 text-center py-3">
                        <div class="mx-auto h-16 w-16 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-sm">
                            <x-icon name="shield-check" class="h-8 w-8 animate-pulse" />
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900">{{ __('site.auth.reset_complete') }}</h3>
                            <p class="mt-1 text-xs text-slate-500 max-w-xs mx-auto leading-relaxed font-medium" x-text="success || '{{ __('site.auth.reset_success_desc') }}'"></p>
                        </div>
                        <a href="/login" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-6 py-3 text-xs sm:text-sm font-bold text-white shadow-md hover:opacity-95 transition-all">
                            <span>{{ __('site.auth.log_in') }}</span>
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
