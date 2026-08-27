@php
    $navigation = [
        ['href' => '/', 'label' => __('site.nav.home'), 'match' => '/'],
        ['href' => '/businesses', 'label' => __('site.nav.directory'), 'match' => 'businesses*'],
        ['href' => '/events', 'label' => __('site.nav.events'), 'match' => 'events*'],
        ['href' => '/gallery', 'label' => __('site.nav.gallery'), 'match' => 'gallery*'],
        ['href' => '/about', 'label' => __('site.nav.about'), 'match' => 'about'],
        ['href' => '/contact', 'label' => __('site.nav.contact'), 'match' => 'contact'],
    ];
    $locale = app()->getLocale();
@endphp

<header
    x-data="{ isScrolled: false, mobileMenuOpen: false }"
    x-init="window.addEventListener('scroll', () => { isScrolled = window.scrollY > 16 })"
    :class="isScrolled ? 'glass border-border shadow-sm' : 'bg-background/80 backdrop-blur-sm border-transparent'"
    class="fixed inset-x-0 top-0 z-50 transition-all duration-300 border-b"
>
    <div class="mx-auto max-w-7xl px-6">
        <nav class="flex h-16 items-center justify-between" aria-label="Global">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2.5 group">
                <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-10 w-10 rounded-full object-contain transition-transform group-hover:scale-105" />
                <span class="text-xl font-bold tracking-tight text-primary-dark">SABHA</span>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden lg:flex lg:items-center lg:gap-1">
                @foreach ($navigation as $item)
                    @php $isActive = request()->is($item['match']); @endphp
                    <a
                        href="{{ $item['href'] }}"
                        class="relative rounded-lg px-3.5 py-2 text-sm font-medium transition-colors {{ $isActive ? 'text-primary' : 'text-muted hover:text-foreground hover:bg-surface' }}"
                    >
                        {{ $item['label'] }}
                        @if ($isActive)
                            <span class="absolute inset-x-3.5 -bottom-px h-0.5 rounded-full bg-primary"></span>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- Desktop actions --}}
            <div class="hidden lg:flex lg:items-center lg:gap-2">
                @auth
                    <a href="/profile" class="rounded-lg px-3.5 py-2 text-sm font-medium text-muted hover:text-foreground hover:bg-surface">
                        {{ __('site.nav.profile') }}
                    </a>
                    @if (auth()->user()->role === 'admin')
                        <a href="/admin" class="inline-flex items-center gap-1.5 rounded-lg bg-primary-soft text-primary border border-primary/15 px-4 py-2 text-sm font-semibold hover:opacity-95">
                            {{ __('site.nav.admin') }}
                        </a>
                    @endif
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-border px-4 py-2 text-sm font-medium text-muted transition-colors hover:bg-surface hover:text-foreground cursor-pointer">
                            <x-icon name="log-out" class="h-[15px] w-[15px]" /> {{ __('site.nav.logout') }}
                        </button>
                    </form>
                @else
                    <button type="button" x-on:click="$store.auth.openLogin()" class="rounded-lg px-3.5 py-2 text-sm font-medium text-muted transition-colors hover:text-foreground hover:bg-surface cursor-pointer">
                        {{ __('site.nav.login') }}
                    </button>
                    <button type="button" x-on:click="$store.auth.openRegister()" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer">
                        {{ __('site.nav.register') }} <x-icon name="arrow-right" class="h-[15px] w-[15px]" />
                    </button>
                @endauth

                {{-- Language Switcher --}}
                <div class="ml-4 border-l border-border pl-4 flex items-center">
                    <div class="flex items-center gap-0.5 bg-slate-100 border border-slate-200/50 p-1 rounded-full shadow-inner">
                        <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 text-xs font-bold rounded-full cursor-pointer transition-all duration-200 select-none {{ $locale === 'en' ? 'bg-primary text-white shadow-sm' : 'text-muted hover:text-foreground' }}">EN</a>
                        <a href="{{ route('lang.switch', 'gu') }}" class="px-3 py-1 text-xs font-bold rounded-full cursor-pointer transition-all duration-200 select-none {{ $locale === 'gu' ? 'bg-primary text-white shadow-sm' : 'text-muted hover:text-foreground' }}">GU</a>
                    </div>
                </div>
            </div>

            {{-- Mobile toggle --}}
            <button type="button" class="lg:hidden rounded-lg p-2 text-foreground hover:bg-surface" x-on:click="mobileMenuOpen = true" aria-label="Open menu">
                <x-icon name="menu" class="h-6 w-6" />
            </button>
        </nav>
    </div>

    {{-- Mobile menu --}}
    <div
        class="lg:hidden fixed inset-0 z-50 transition-opacity duration-200"
        :class="mobileMenuOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
    >
        <div class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm" x-on:click="mobileMenuOpen = false"></div>
        <div class="fixed inset-y-0 right-0 w-full max-w-sm overflow-y-auto bg-background px-6 py-6 shadow-xl">
            <div class="mb-8 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2.5" x-on:click="mobileMenuOpen = false">
                    <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-10 w-10 rounded-full object-contain" />
                    <span class="text-xl font-bold tracking-tight text-primary-dark">SABHA</span>
                </a>
                <button type="button" class="rounded-lg p-2 text-muted hover:bg-surface" x-on:click="mobileMenuOpen = false" aria-label="Close menu">
                    <x-icon name="x" class="h-6 w-6" />
                </button>
            </div>

            <div class="flex flex-col gap-1">
                @foreach ($navigation as $item)
                    @php $isActive = request()->is($item['match']); @endphp
                    <a
                        href="{{ $item['href'] }}"
                        x-on:click="mobileMenuOpen = false"
                        class="rounded-lg px-4 py-3 text-base font-medium transition-colors {{ $isActive ? 'bg-primary-soft text-primary' : 'text-foreground hover:bg-surface' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            <hr class="my-6 border-border" />

            <div class="flex flex-col gap-3">
                @auth
                    <a href="/profile" x-on:click="mobileMenuOpen = false" class="inline-flex items-center justify-center gap-2 rounded-lg border border-border px-4 py-3 text-sm font-semibold text-foreground hover:bg-surface">
                        {{ __('site.nav.profile') }}
                    </a>
                    @if (auth()->user()->role === 'admin')
                        <a href="/admin" x-on:click="mobileMenuOpen = false" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-soft text-primary border border-primary/15 px-4 py-3 text-sm font-semibold">
                            {{ __('site.nav.admin') }}
                        </a>
                    @endif
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-border px-4 py-3 text-sm font-semibold text-foreground hover:bg-surface cursor-pointer">
                            <x-icon name="log-out" class="h-4 w-4" /> {{ __('site.nav.logout') }}
                        </button>
                    </form>
                @else
                    <button type="button" x-on:click="mobileMenuOpen = false; $store.auth.openLogin()" class="rounded-lg border border-border px-4 py-3 text-center text-sm font-semibold text-foreground hover:bg-surface cursor-pointer">
                        {{ __('site.nav.login') }}
                    </button>
                    <button type="button" x-on:click="mobileMenuOpen = false; $store.auth.openRegister()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-3 text-sm font-semibold text-white cursor-pointer">
                        {{ __('site.nav.register') }} <x-icon name="arrow-right" class="h-4 w-4" />
                    </button>
                @endauth

                {{-- Mobile Language Switcher --}}
                <div class="mt-4 flex items-center justify-between border-t border-border pt-4">
                    <span class="text-xs font-semibold text-muted">Language / ભાષા</span>
                    <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-lg">
                        <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1.5 text-xs font-bold rounded cursor-pointer transition-colors select-none {{ $locale === 'en' ? 'bg-primary text-white' : 'text-muted hover:text-foreground' }}">EN</a>
                        <a href="{{ route('lang.switch', 'gu') }}" class="px-3 py-1.5 text-xs font-bold rounded cursor-pointer transition-colors select-none {{ $locale === 'gu' ? 'bg-primary text-white' : 'text-muted hover:text-foreground' }}">GU</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
