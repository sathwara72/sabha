@props(['title' => 'Admin | Sabha'])

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full scroll-smooth antialiased @if(app()->getLocale() === 'gu') lang-gu @endif">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <meta name="robots" content="noindex, nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-background font-outfit text-foreground" x-data="{ sidebarOpen: false }" x-init="$watch('sidebarOpen', (v) => document.body.style.overflow = v ? 'hidden' : '')">
    <div class="min-h-screen bg-background font-outfit text-foreground flex">

        @php
            $isFullAdmin = auth()->user()->role === 'admin';

            $visible = function (array $item) use ($isFullAdmin) {
                if ($isFullAdmin) {
                    return true;
                }
                if (! empty($item['fullAdminOnly'])) {
                    return false;
                }

                return $item['module'] === null || auth()->user()->hasModuleAbility($item['module'], 'can_view');
            };

            $menuGroups = collect([
                [
                    'label' => null,
                    'items' => [
                        ['name' => 'Overview', 'icon' => 'layout-dashboard', 'href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard'), 'module' => null],
                    ],
                ],
                [
                    'label' => 'Directory',
                    'items' => [
                        ['name' => 'Businesses', 'icon' => 'briefcase', 'href' => route('admin.businesses.index'), 'active' => request()->routeIs('admin.businesses.*'), 'module' => 'businesses'],
                        ['name' => 'Categories', 'icon' => 'tag', 'href' => route('admin.categories.index'), 'active' => request()->routeIs('admin.categories.*'), 'module' => 'categories'],
                        ['name' => 'Cities & Areas', 'icon' => 'map-pin', 'href' => route('admin.locations.index'), 'active' => request()->routeIs('admin.locations.*'), 'module' => 'locations'],
                    ],
                ],
                [
                    'label' => 'Events & Bookings',
                    'items' => [
                        ['name' => 'Events', 'icon' => 'calendar', 'href' => route('admin.events.index'), 'active' => request()->routeIs('admin.events.*'), 'module' => 'events'],
                        ['name' => 'Bookings', 'icon' => 'shield-check', 'href' => route('admin.bookings.index'), 'active' => request()->routeIs('admin.bookings.*'), 'module' => 'bookings'],
                        ['name' => 'Gallery', 'icon' => 'image', 'href' => route('admin.gallery.index'), 'active' => request()->routeIs('admin.gallery.*'), 'module' => 'gallery'],
                        ['name' => 'Hero Slider', 'icon' => 'sliders', 'href' => route('admin.hero-slider.index'), 'active' => request()->routeIs('admin.hero-slider.*'), 'module' => 'hero-slider'],
                    ],
                ],
                [
                    'label' => 'Members',
                    'items' => [
                        ['name' => 'Users', 'icon' => 'users', 'href' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*'), 'module' => 'users'],
                        ['name' => 'Member Titles', 'icon' => 'award', 'href' => route('admin.member-titles.index'), 'active' => request()->routeIs('admin.member-titles.*'), 'module' => 'member-titles'],
                    ],
                ],
                [
                    'label' => 'Network Activity',
                    'items' => [
                        ['name' => '1-to-1 Meetings', 'icon' => 'users', 'href' => route('admin.meetings.index'), 'active' => request()->routeIs('admin.meetings.*'), 'module' => 'meetings'],
                        ['name' => 'Referrals', 'icon' => 'send', 'href' => route('admin.referrals.index'), 'active' => request()->routeIs('admin.referrals.*'), 'module' => 'referrals'],
                        ['name' => 'Testimonials', 'icon' => 'message-square', 'href' => route('admin.testimonials.index'), 'active' => request()->routeIs('admin.testimonials.*'), 'module' => 'testimonials'],
                    ],
                ],
                [
                    'label' => 'Insights',
                    'items' => [
                        ['name' => 'Statistics', 'icon' => 'bar-chart-3', 'href' => route('admin.statistics.index'), 'active' => request()->routeIs('admin.statistics.*'), 'module' => 'statistics'],
                        ['name' => 'Analytics', 'icon' => 'trending-up', 'href' => route('admin.analytics.index'), 'active' => request()->routeIs('admin.analytics.*'), 'module' => 'analytics'],
                    ],
                ],
                [
                    'label' => 'Administration',
                    'items' => [
                        ['name' => 'Sub-Admins', 'icon' => 'key-round', 'href' => route('admin.sub-admins.index'), 'active' => request()->routeIs('admin.sub-admins.*'), 'module' => null, 'fullAdminOnly' => true],
                        ['name' => 'Site Settings', 'icon' => 'settings', 'href' => route('admin.settings.index'), 'active' => request()->routeIs('admin.settings.*'), 'module' => null, 'fullAdminOnly' => true],
                    ],
                ],
            ])->map(function ($group) use ($visible) {
                $group['items'] = collect($group['items'])->filter($visible)->values()->all();

                return $group;
            })->filter(fn ($group) => ! empty($group['items']))->values()->all();
        @endphp

        {{-- Desktop Sidebar --}}
        <aside class="hidden lg:flex w-64 border-r border-border bg-white p-5 flex-col fixed h-full z-50">
            @include('components.layouts._admin-sidebar-content', ['menuGroups' => $menuGroups])
        </aside>

        {{-- Mobile Drawer Backdrop --}}
        <div x-show="sidebarOpen" x-cloak x-transition.opacity class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden" x-on:click="sidebarOpen = false"></div>

        {{-- Mobile Drawer --}}
        <aside
            class="fixed top-0 left-0 h-full w-72 bg-white border-r border-border p-5 z-50 flex flex-col transition-transform duration-300 lg:hidden"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            @include('components.layouts._admin-sidebar-content', ['menuGroups' => $menuGroups])
        </aside>

        {{-- Content Area --}}
        <main class="flex-1 lg:ml-64 min-h-screen flex flex-col bg-background">
            <header class="sticky top-0 z-40 flex items-center justify-between px-4 py-3 lg:px-6 border-b border-border bg-white/90 backdrop-blur-md">
                <div class="flex items-center gap-3">
                    <button
                        x-on:click="sidebarOpen = true"
                        class="lg:hidden inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-white text-muted-foreground hover:bg-surface transition-colors"
                    >
                        <x-icon name="menu" class="h-4 w-4" />
                    </button>

                    <div class="inline-flex items-center gap-1.5 rounded-full bg-primary-soft px-3 py-1 text-xs font-semibold text-primary">
                        <x-icon name="shield-check" class="h-3.5 w-3.5" />
                        <span>Admin</span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-semibold text-foreground leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[12px] text-muted">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-soft text-xs font-bold text-primary">
                        {{ auth()->user()->name ? mb_substr(auth()->user()->name, 0, 1) : 'A' }}
                    </div>
                </div>
            </header>

            <div class="flex-1 p-4 lg:p-5">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
