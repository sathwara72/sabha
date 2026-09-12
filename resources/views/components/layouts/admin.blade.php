@props(['title' => 'Admin | Sabha'])

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
    class="h-full scroll-smooth antialiased @if(app()->getLocale() === 'gu') lang-gu @endif">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="{{ asset('logo2.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-slate-50/60 font-outfit text-slate-900 antialiased" x-data="{ sidebarOpen: false }"
    x-init="$watch('sidebarOpen', (v) => document.body.style.overflow = v ? 'hidden' : '')">
    <div class="min-h-screen flex">

        @php
            $isFullAdmin = auth()->user()->role === 'admin';

            $visible = function (array $item) use ($isFullAdmin) {
                if ($isFullAdmin) {
                    return true;
                }
                if (!empty($item['fullAdminOnly'])) {
                    return false;
                }

                return $item['module'] === null || auth()->user()->hasModuleAbility($item['module'], 'can_view');
            };

            $menuGroups = collect([
                [
                    'label' => null,
                    'icon' => null,
                    'items' => [
                        ['name' => 'Overview', 'icon' => 'layout-dashboard', 'href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard'), 'module' => null],
                    ],
                ],
                [
                    'label' => 'Directory',
                    'icon' => 'folder',
                    'items' => [
                        ['name' => 'Businesses', 'icon' => 'briefcase', 'href' => route('admin.businesses.index'), 'active' => request()->routeIs('admin.businesses.*'), 'module' => 'businesses'],
                        ['name' => 'Categories', 'icon' => 'tag', 'href' => route('admin.categories.index'), 'active' => request()->routeIs('admin.categories.*'), 'module' => 'categories'],
                        ['name' => 'Cities & Areas', 'icon' => 'map-pin', 'href' => route('admin.locations.index'), 'active' => request()->routeIs('admin.locations.*'), 'module' => 'locations'],
                    ],
                ],
                [
                    'label' => 'Events & Bookings',
                    'icon' => 'calendar',
                    'items' => [
                        ['name' => 'Events', 'icon' => 'calendar', 'href' => route('admin.events.index'), 'active' => request()->routeIs('admin.events.*'), 'module' => 'events'],
                        ['name' => 'Bookings', 'icon' => 'shield-check', 'href' => route('admin.bookings.index'), 'active' => request()->routeIs('admin.bookings.*'), 'module' => 'bookings'],
                        ['name' => 'Gallery', 'icon' => 'image', 'href' => route('admin.gallery.index'), 'active' => request()->routeIs('admin.gallery.*'), 'module' => 'gallery'],
                        ['name' => 'Hero Slider', 'icon' => 'sliders', 'href' => route('admin.hero-slider.index'), 'active' => request()->routeIs('admin.hero-slider.*'), 'module' => 'hero-slider'],
                    ],
                ],
                [
                    'label' => 'Members',
                    'icon' => 'users',
                    'items' => [
                        ['name' => 'Registrations', 'icon' => 'user-check', 'href' => route('admin.registrations.index'), 'active' => request()->routeIs('admin.registrations.*'), 'module' => 'registrations'],
                        ['name' => 'Users', 'icon' => 'users', 'href' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*'), 'module' => 'users'],
                        ['name' => 'Member Titles', 'icon' => 'award', 'href' => route('admin.member-titles.index'), 'active' => request()->routeIs('admin.member-titles.*'), 'module' => 'member-titles'],
                        ['name' => 'Trustees', 'icon' => 'award', 'href' => route('admin.trustees.index'), 'active' => request()->routeIs('admin.trustees.*'), 'module' => 'trustees'],
                    ],
                ],
                [
                    'label' => 'Network Activity',
                    'icon' => 'activity',
                    'items' => [
                        ['name' => 'Chat', 'icon' => 'message-square', 'href' => route('admin.chat.index'), 'active' => request()->routeIs('admin.chat.*'), 'module' => 'chat'],
                        ['name' => '1-to-1 Meetings', 'icon' => 'users', 'href' => route('admin.meetings.index'), 'active' => request()->routeIs('admin.meetings.*'), 'module' => 'meetings'],
                        ['name' => 'Referrals', 'icon' => 'send', 'href' => route('admin.referrals.index'), 'active' => request()->routeIs('admin.referrals.*'), 'module' => 'referrals'],
                        ['name' => 'Testimonials', 'icon' => 'message-square', 'href' => route('admin.testimonials.index'), 'active' => request()->routeIs('admin.testimonials.*'), 'module' => 'testimonials'],
                    ],
                ],
                [
                    'label' => 'Administration',
                    'icon' => 'settings',
                    'items' => [
                        ['name' => 'Sub-Admins', 'icon' => 'key-round', 'href' => route('admin.sub-admins.index'), 'active' => request()->routeIs('admin.sub-admins.*'), 'module' => null, 'fullAdminOnly' => true],
                        ['name' => 'Site Settings', 'icon' => 'settings', 'href' => route('admin.settings.index'), 'active' => request()->routeIs('admin.settings.*'), 'module' => null, 'fullAdminOnly' => true],
                    ],
                ],
            ])->map(function ($group) use ($visible) {
                $group['items'] = collect($group['items'])->filter($visible)->values()->all();

                return $group;
            })->filter(fn($group) => !empty($group['items']))->values()->all();
        @endphp

        {{-- Desktop Sidebar --}}
        <aside
            class="hidden lg:flex w-64 shrink-0 border-r border-slate-200/90 bg-white p-4 flex-col sticky top-0 h-screen z-30 shadow-xs">
            @include('components.layouts._admin-sidebar-content', ['menuGroups' => $menuGroups])
        </aside>

        {{-- Mobile Drawer Backdrop --}}
        <div x-show="sidebarOpen" x-cloak x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden" x-on:click="sidebarOpen = false">
        </div>

        {{-- Mobile Drawer --}}
        <aside
            class="fixed top-0 left-0 h-full w-72 bg-white border-r border-slate-200 p-4 z-50 flex flex-col transition-transform duration-300 lg:hidden shadow-2xl"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" x-cloak>
            @include('components.layouts._admin-sidebar-content', ['menuGroups' => $menuGroups])
        </aside>

        {{-- Main Content Area --}}
        <main class="flex-1 min-w-0 min-h-screen flex flex-col bg-slate-50/60">
            {{-- Mobile-only Top Bar (Hidden on Desktop) --}}
            <div
                class="lg:hidden sticky top-0 z-20 flex h-14 items-center justify-between px-3 sm:px-4 border-b border-slate-200/90 bg-white/95 backdrop-blur-md shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <button type="button" x-on:click="sidebarOpen = true"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-2xs"
                        aria-label="Open sidebar">
                        <x-icon name="menu" class="h-4 w-4" />
                    </button>
                    <span class="text-xs font-bold text-slate-800">SABHA Admin</span>
                </div>

                <a href="/" target="_blank"
                    class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-2xs">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    <span>Live</span>
                    <x-icon name="arrow-right" class="h-3 w-3 -rotate-45 text-slate-400" />
                </a>
            </div>

            {{-- Main Page Slot --}}
            <div class="flex-1 p-3 sm:p-4 lg:p-6">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>

</html>