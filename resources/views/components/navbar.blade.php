@php
    $locale = app()->getLocale();
    $isAboutActive = request()->is('about') || request()->is('trustees') || request()->is('contact');
@endphp

<header
    x-data="{
        isScrolled: false,
        mobileMenuOpen: false,
        eventsDropdownOpen: false,
        aboutDropdownOpen: false,
        userDropdownOpen: false
    }"
    x-init="
        window.addEventListener('scroll', () => { isScrolled = window.scrollY > 12 });
        isScrolled = window.scrollY > 12;
    "
    :class="isScrolled ? 'bg-white/95 backdrop-blur-md border-slate-200/90 shadow-sm' : 'bg-white/80 backdrop-blur-sm border-slate-100'"
    class="fixed inset-x-0 top-0 z-50 transition-all duration-300 border-b select-none font-outfit"
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <nav class="flex h-16 items-center justify-between gap-4" aria-label="Global">
            {{-- ===================== LOGO ===================== --}}
            <div class="flex items-center gap-3 shrink-0">
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 p-1 ring-1 ring-slate-200/80 transition-all duration-300 group-hover:scale-105 group-hover:ring-primary/40 shadow-xs">
                        <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-8 w-8 rounded-full object-contain" />
                        <!-- <div class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></div> -->
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xl font-black tracking-tight text-primary-dark leading-none">SABHA</span>
                        </div>
                        <span class="text-[10px] font-bold text-muted uppercase tracking-wider leading-tight mt-0.5 hidden sm:block">Business Network</span>
                    </div>
                </a>
            </div>

            {{-- ===================== DESKTOP NAVIGATION LINKS ===================== --}}
            <div class="hidden lg:flex lg:items-center lg:gap-1 xl:gap-1.5">
                {{-- Home --}}
                <a
                    href="/"
                    class="relative rounded-xl px-3 py-2 text-xs xl:text-sm font-bold whitespace-nowrap transition-all duration-200 {{ request()->is('/') ? 'text-primary bg-primary/8' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}"
                >
                    {{ __('site.nav.home') }}
                </a>

                {{-- Businesses (Directory) --}}
                <a
                    href="/businesses"
                    class="relative rounded-xl px-3 py-2 text-xs xl:text-sm font-bold whitespace-nowrap transition-all duration-200 {{ request()->is('businesses*') ? 'text-primary bg-primary/8' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}"
                >
                    {{ __('site.nav.directory') }}
                </a>

                {{-- Events Dropdown --}}
                <div class="relative" x-on:click.outside="eventsDropdownOpen = false">
                    <button
                        type="button"
                        x-on:click="eventsDropdownOpen = !eventsDropdownOpen; aboutDropdownOpen = false; userDropdownOpen = false"
                        class="inline-flex items-center gap-1 rounded-xl px-3 py-2 text-xs xl:text-sm font-bold whitespace-nowrap transition-all duration-200 cursor-pointer {{ request()->is('events*') ? 'text-primary bg-primary/8' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}"
                    >
                        <span>{{ __('site.nav.events') }}</span>
                        <x-icon
                            name="chevron-down"
                            class="h-3.5 w-3.5 transition-transform duration-200"
                            x-bind:class="eventsDropdownOpen ? 'rotate-180 text-primary' : 'text-slate-400'"
                        />
                    </button>

                    {{-- Events Dropdown Menu --}}
                    <div
                        x-show="eventsDropdownOpen"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                        class="absolute left-0 mt-2 w-56 origin-top-left rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl shadow-slate-200/50 z-50 space-y-0.5"
                    >
                        <a
                            href="/events"
                            x-on:click="eventsDropdownOpen = false"
                            class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-bold transition-colors {{ request()->is('events') && !request()->has('filter') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
                        >
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <x-icon name="calendar" class="h-3.5 w-3.5" />
                            </div>
                            <div>
                                <span class="block leading-tight">{{ __('site.nav.all_events') }}</span>
                                <span class="text-[10px] font-normal text-slate-400">{{ __('site.nav.all_events_desc') }}</span>
                            </div>
                        </a>

                        <a
                            href="/events?filter=current"
                            x-on:click="eventsDropdownOpen = false"
                            class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-bold transition-colors {{ request()->get('filter') === 'current' ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
                        >
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            </div>
                            <div>
                                <span class="block leading-tight text-emerald-800">{{ __('site.nav.booking_available') }}</span>
                                <span class="text-[10px] font-normal text-slate-400">{{ __('site.nav.booking_available_desc') }}</span>
                            </div>
                        </a>

                        <a
                            href="/events?filter=upcoming"
                            x-on:click="eventsDropdownOpen = false"
                            class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-bold transition-colors {{ request()->get('filter') === 'upcoming' ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
                        >
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            </div>
                            <div>
                                <span class="block leading-tight text-amber-800">{{ __('site.nav.booking_soon') }}</span>
                                <span class="text-[10px] font-normal text-slate-400">{{ __('site.nav.booking_soon_desc') }}</span>
                            </div>
                        </a>

                        <a
                            href="/events?filter=past"
                            x-on:click="eventsDropdownOpen = false"
                            class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-bold transition-colors {{ request()->get('filter') === 'past' ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
                        >
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                                <span class="h-2 w-2 rounded-full bg-slate-500"></span>
                            </div>
                            <div>
                                <span class="block leading-tight text-slate-800">{{ __('site.nav.past_events') }}</span>
                                <span class="text-[10px] font-normal text-slate-400">{{ __('site.nav.past_events_desc') }}</span>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Gallery --}}
                <a
                    href="/gallery"
                    class="relative rounded-xl px-3 py-2 text-xs xl:text-sm font-bold whitespace-nowrap transition-all duration-200 {{ request()->is('gallery*') ? 'text-primary bg-primary/8' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}"
                >
                    {{ __('site.nav.gallery') }}
                </a>

                {{-- About Dropdown (Grouped to eliminate horizontal clutter) --}}
                <div class="relative" x-on:click.outside="aboutDropdownOpen = false">
                    <button
                        type="button"
                        x-on:click="aboutDropdownOpen = !aboutDropdownOpen; eventsDropdownOpen = false; userDropdownOpen = false"
                        class="inline-flex items-center gap-1 rounded-xl px-3 py-2 text-xs xl:text-sm font-bold whitespace-nowrap transition-all duration-200 cursor-pointer {{ $isAboutActive ? 'text-primary bg-primary/8' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}"
                    >
                        <span>{{ __('site.nav.about') }}</span>
                        <x-icon
                            name="chevron-down"
                            class="h-3.5 w-3.5 transition-transform duration-200"
                            x-bind:class="aboutDropdownOpen ? 'rotate-180 text-primary' : 'text-slate-400'"
                        />
                    </button>

                    {{-- About Dropdown Menu --}}
                    <div
                        x-show="aboutDropdownOpen"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                        class="absolute left-0 mt-2 w-52 origin-top-left rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl shadow-slate-200/50 z-50 space-y-0.5"
                    >
                        <a
                            href="/about"
                            x-on:click="aboutDropdownOpen = false"
                            class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-bold transition-colors {{ request()->is('about') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
                        >
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <x-icon name="info" class="h-3.5 w-3.5" />
                            </div>
                            <span>{{ __('site.nav.about') }}</span>
                        </a>

                        <a
                            href="/trustees"
                            x-on:click="aboutDropdownOpen = false"
                            class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-bold transition-colors {{ request()->is('trustees') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
                        >
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                <x-icon name="users" class="h-3.5 w-3.5" />
                            </div>
                            <span>{{ __('site.nav.trustees') }}</span>
                        </a>

                        <a
                            href="/contact"
                            x-on:click="aboutDropdownOpen = false"
                            class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-bold transition-colors {{ request()->is('contact') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
                        >
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                <x-icon name="phone" class="h-3.5 w-3.5" />
                            </div>
                            <span>{{ __('site.nav.contact') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ===================== DESKTOP RIGHT ACTIONS ===================== --}}
            <div class="hidden lg:flex lg:items-center lg:gap-2.5 xl:gap-3 shrink-0">
                @auth
                    @php $user = auth()->user(); @endphp

                    @if (!$user->canAccessAdminArea())
                        {{-- Chat Button with Icon (Members only) --}}
                        <a
                            href="/chat"
                            class="relative inline-flex items-center gap-1.5 rounded-xl border border-slate-200/90 bg-white px-3 py-2 text-xs xl:text-sm font-bold text-slate-700 transition-all hover:border-slate-300 hover:bg-slate-50 shadow-2xs whitespace-nowrap"
                        >
                            <x-icon name="message-square" class="h-3.5 w-3.5 text-primary" />
                            <span>{{ __('site.nav.chat') }}</span>
                        </a>
                    @endif

                    {{-- User Profile & Admin Dropdown --}}
                    <div class="relative" x-on:click.outside="userDropdownOpen = false">
                        <button
                            type="button"
                            x-on:click="userDropdownOpen = !userDropdownOpen; aboutDropdownOpen = false"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white p-1 pl-1.5 pr-2.5 text-xs font-bold text-slate-800 transition-all hover:border-slate-300 hover:bg-slate-50 shadow-2xs cursor-pointer"
                        >
                            {{-- User Avatar / Initials --}}
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-[#00379D] to-[#0F3459] text-white text-[11px] font-black uppercase shadow-xs">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="flex flex-col text-left max-w-[120px]">
                                <span class="truncate text-xs font-bold text-slate-900 leading-none">{{ $user->name }}</span>
                                <span class="text-[10px] font-medium text-slate-500 uppercase tracking-wider leading-tight mt-0.5">{{ $user->role === 'admin' ? __('site.nav.admin_role') : ($user->role === 'sub_admin' ? __('site.nav.sub_admin_role') : __('site.nav.member_role')) }}</span>
                            </div>
                            <x-icon
                                name="chevron-down"
                                class="h-3.5 w-3.5 text-slate-400 transition-transform duration-200"
                                x-bind:class="userDropdownOpen ? 'rotate-180 text-primary' : ''"
                            />
                        </button>

                        {{-- User Dropdown Menu --}}
                        <div
                            x-show="userDropdownOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                            class="absolute right-0 mt-2 w-56 origin-top-right rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl shadow-slate-200/50 z-50 space-y-1"
                        >
                            {{-- Header --}}
                            <div class="px-3 py-2 border-b border-slate-100 mb-1">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ $user->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate font-medium">{{ $user->email }}</p>
                            </div>

                            {{-- Admin Panel (if admin or sub-admin) --}}
                            @if ($user->canAccessAdminArea())
                                <a
                                    href="/admin"
                                    x-on:click="userDropdownOpen = false"
                                    class="flex items-center gap-2 rounded-xl bg-blue-50/90 border border-blue-100 px-3 py-2 text-xs font-bold text-primary hover:bg-blue-100 transition-colors"
                                >
                                    <x-icon name="shield-check" class="h-3.5 w-3.5 text-primary" />
                                    <span>Admin Panel</span>
                                </a>
                            @else
                                {{-- Profile (Members only) --}}
                                <a
                                    href="/profile"
                                    x-on:click="userDropdownOpen = false"
                                    class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors"
                                >
                                    <x-icon name="user" class="h-3.5 w-3.5 text-slate-500" />
                                    <span>{{ __('site.nav.profile') }}</span>
                                </a>
                            @endif

                            <div class="border-t border-slate-100 my-1"></div>

                            {{-- Logout --}}
                            <form method="POST" action="/logout" class="w-full">
                                @csrf
                                <button
                                    type="submit"
                                    class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                >
                                    <x-icon name="log-out" class="h-3.5 w-3.5 text-rose-500" />
                                    <span>{{ __('site.nav.logout') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- Log in --}}
                    <a
                        href="/login"
                        class="rounded-xl px-3.5 py-2 text-xs xl:text-sm font-bold text-slate-700 transition-all hover:bg-slate-100/80 hover:text-slate-950 whitespace-nowrap cursor-pointer"
                    >
                        {{ __('site.nav.login') }}
                    </a>

                    {{-- Join Now / Register --}}
                    <a
                        href="/register"
                        class="group inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2 text-xs xl:text-sm font-extrabold text-white shadow-md shadow-primary/20 transition-all duration-200 hover:shadow-lg hover:shadow-primary/25 hover:opacity-95 active:scale-[0.99] whitespace-nowrap cursor-pointer"
                    >
                        <span>{{ __('site.nav.register') }}</span>
                        <x-icon name="arrow-right" class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" />
                    </a>
                @endauth

                {{-- Modern Segmented Language Switcher --}}
                <div class="flex items-center gap-0.5 bg-slate-200/70 p-0.5 rounded-xl border border-slate-300/50 text-xs font-bold shadow-inner">
                    <a
                        href="{{ route('lang.switch', 'en') }}"
                        class="px-2.5 py-1 text-xs font-bold rounded-lg transition-all duration-200 select-none {{ $locale === 'en' ? 'bg-primary text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        EN
                    </a>
                    <a
                        href="{{ route('lang.switch', 'gu') }}"
                        class="px-2.5 py-1 text-xs font-bold rounded-lg transition-all duration-200 select-none {{ $locale === 'gu' ? 'bg-primary text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        GU
                    </a>
                </div>
            </div>

            {{-- ===================== MOBILE HAMBURGER TOGGLE ===================== --}}
            <div class="flex items-center gap-2 lg:hidden">
                {{-- Mobile Language Switcher Pill --}}
                <div class="flex items-center gap-0.5 bg-slate-200/70 p-0.5 rounded-lg text-xs font-bold">
                    <a href="{{ route('lang.switch', 'en') }}" class="px-2 py-0.5 rounded {{ $locale === 'en' ? 'bg-primary text-white' : 'text-slate-600' }}">EN</a>
                    <a href="{{ route('lang.switch', 'gu') }}" class="px-2 py-0.5 rounded {{ $locale === 'gu' ? 'bg-primary text-white' : 'text-slate-600' }}">GU</a>
                </div>

                <button
                    type="button"
                    class="rounded-xl border border-slate-200 bg-white p-2 text-slate-700 hover:bg-slate-50 transition-colors shadow-2xs cursor-pointer"
                    x-on:click="mobileMenuOpen = true"
                    aria-label="Open menu"
                >
                    <x-icon name="menu" class="h-5 w-5" />
                </button>
            </div>
        </nav>
    </div>

    {{-- ===================== MOBILE NAVIGATION DRAWER ===================== --}}
    <div
        class="lg:hidden fixed inset-0 z-50 transition-opacity duration-200"
        :class="mobileMenuOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
        x-cloak
    >
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" x-on:click="mobileMenuOpen = false"></div>

        {{-- Slide Drawer --}}
        <div
            class="fixed inset-y-0 right-0 w-full max-w-xs overflow-y-auto bg-white px-5 py-5 shadow-2xl transition-transform duration-300 flex flex-col justify-between"
            :class="mobileMenuOpen ? 'translate-x-0' : 'translate-x-full'"
        >
            <div>
                {{-- Top Header --}}
                <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                    <a href="/" class="flex items-center gap-2.5" x-on:click="mobileMenuOpen = false">
                        <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-9 w-9 rounded-full object-contain" />
                        <span class="text-xl font-black tracking-tight text-primary-dark">SABHA</span>
                    </a>
                    <button
                        type="button"
                        class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors"
                        x-on:click="mobileMenuOpen = false"
                        aria-label="Close menu"
                    >
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                {{-- User Info if Authenticated --}}
                @auth
                    @php $user = auth()->user(); @endphp
                    <div class="mb-5 rounded-2xl border border-slate-200/80 bg-slate-50 p-3.5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary text-white font-black text-xs uppercase">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ $user->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                @endauth

                {{-- Primary Nav Links --}}
                <div class="flex flex-col gap-1">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-1">Navigation</p>
                    <a
                        href="/"
                        x-on:click="mobileMenuOpen = false"
                        class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-xs font-bold transition-colors {{ request()->is('/') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50' }}"
                    >
                        <span>{{ __('site.nav.home') }}</span>
                    </a>
                    <a
                        href="/businesses"
                        x-on:click="mobileMenuOpen = false"
                        class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-xs font-bold transition-colors {{ request()->is('businesses*') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50' }}"
                    >
                        <span>{{ __('site.nav.directory') }}</span>
                    </a>
                    <div class="rounded-xl overflow-hidden {{ request()->is('events*') ? 'bg-primary/5' : '' }}">
                        <a
                            href="/events"
                            x-on:click="mobileMenuOpen = false"
                            class="flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-colors {{ request()->is('events*') ? 'text-primary' : 'text-slate-700 hover:bg-slate-50' }}"
                        >
                            <span>{{ __('site.nav.events') }}</span>
                            <span class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-bold">{{ __('site.nav.all_badge') }}</span>
                        </a>
                        <div class="pl-5 pr-2 pb-2 pt-0.5 space-y-1">
                            <a href="/events?filter=current" x-on:click="mobileMenuOpen = false" class="flex items-center gap-2 px-2 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:bg-slate-100/70">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                <span>{{ __('site.nav.booking_available') }}</span>
                            </a>
                            <a href="/events?filter=upcoming" x-on:click="mobileMenuOpen = false" class="flex items-center gap-2 px-2 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:bg-slate-100/70">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                <span>{{ __('site.nav.booking_soon') }}</span>
                            </a>
                            <a href="/events?filter=past" x-on:click="mobileMenuOpen = false" class="flex items-center gap-2 px-2 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:bg-slate-100/70">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                <span>{{ __('site.nav.past_events') }}</span>
                            </a>
                        </div>
                    </div>
                    <a
                        href="/gallery"
                        x-on:click="mobileMenuOpen = false"
                        class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-xs font-bold transition-colors {{ request()->is('gallery*') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50' }}"
                    >
                        <span>{{ __('site.nav.gallery') }}</span>
                    </a>

                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 mt-3 mb-1">{{ __('site.nav.about_sabha') }}</p>
                    <a
                        href="/about"
                        x-on:click="mobileMenuOpen = false"
                        class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-xs font-bold transition-colors {{ request()->is('about') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50' }}"
                    >
                        <span>{{ __('site.nav.about') }}</span>
                    </a>
                    <a
                        href="/trustees"
                        x-on:click="mobileMenuOpen = false"
                        class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-xs font-bold transition-colors {{ request()->is('trustees') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50' }}"
                    >
                        <span>{{ __('site.nav.trustees') }}</span>
                    </a>
                    <a
                        href="/contact"
                        x-on:click="mobileMenuOpen = false"
                        class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-xs font-bold transition-colors {{ request()->is('contact') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50' }}"
                    >
                        <span>{{ __('site.nav.contact') }}</span>
                    </a>
                </div>
            </div>

            {{-- Bottom Account Actions in Mobile Drawer --}}
            <div class="border-t border-slate-100 pt-4 mt-6">
                @auth
                    <div class="flex flex-col gap-2">
                        @if (!auth()->user()->canAccessAdminArea())
                            <a
                                href="/chat"
                                x-on:click="mobileMenuOpen = false"
                                class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-2.5 text-xs font-bold text-slate-700 shadow-2xs"
                            >
                                <x-icon name="message-square" class="h-3.5 w-3.5 text-primary" />
                                <span>{{ __('site.nav.chat') }}</span>
                            </a>
                            <a
                                href="/profile"
                                x-on:click="mobileMenuOpen = false"
                                class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-2.5 text-xs font-bold text-slate-700 shadow-2xs"
                            >
                                <x-icon name="user" class="h-3.5 w-3.5 text-slate-500" />
                                <span>{{ __('site.nav.profile') }}</span>
                            </a>
                        @endif
                        @if (auth()->user()->canAccessAdminArea())
                            <a
                                href="/admin"
                                x-on:click="mobileMenuOpen = false"
                                class="flex items-center justify-center gap-2 rounded-xl bg-blue-50 text-primary border border-primary/20 py-2.5 text-xs font-bold"
                            >
                                <x-icon name="shield-check" class="h-3.5 w-3.5" />
                                <span>{{ __('site.nav.admin') }}</span>
                            </a>
                        @endif
                        <form method="POST" action="/logout" class="w-full">
                            @csrf
                            <button
                                type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 py-2.5 text-xs font-bold text-rose-600"
                            >
                                <x-icon name="log-out" class="h-3.5 w-3.5 text-rose-500" />
                                <span>{{ __('site.nav.logout') }}</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex flex-col gap-2">
                        <a
                            href="/login"
                            x-on:click="mobileMenuOpen = false"
                            class="flex items-center justify-center rounded-xl border border-slate-200 bg-white py-2.5 text-xs font-bold text-slate-700 shadow-2xs"
                        >
                            {{ __('site.nav.login') }}
                        </a>
                        <a
                            href="/register"
                            x-on:click="mobileMenuOpen = false"
                            class="flex items-center justify-center gap-1.5 rounded-xl bg-primary py-2.5 text-xs font-bold text-white shadow-sm"
                        >
                            <span>{{ __('site.nav.register') }}</span>
                            <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>
