{{-- expects: $menuGroups (array of ['label' => ?string, 'items' => [...]]) --}}
<div class="flex flex-col h-full overflow-hidden select-none font-outfit">
    {{-- Brand Header --}}
    <div class="flex items-center justify-between gap-2 mb-3 px-2 pb-3 border-b border-slate-100">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-50 p-1 ring-1 ring-slate-200 transition-all duration-200 group-hover:ring-primary/40 shadow-xs">
                <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-8 w-8 rounded-full object-contain" />
            </div>
            <div class="flex flex-col">
                <div class="flex items-center gap-1.5">
                    <span class="text-base font-black tracking-tight text-slate-900 leading-none">SABHA</span>
                    <span class="rounded bg-primary/10 px-1.5 py-0.5 text-[9px] font-black uppercase text-primary tracking-wider">Admin</span>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Control Panel</span>
            </div>
        </a>
        <button
            type="button"
            x-on:click="sidebarOpen = false"
            class="lg:hidden rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors"
        >
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>

    {{-- Live Website Button in Sidebar --}}
    <div class="px-2 mb-3">
        <a
            href="/"
            target="_blank"
            class="flex items-center justify-between gap-2 rounded-xl bg-slate-50 border border-slate-200/90 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-primary-soft hover:text-primary hover:border-primary/30 transition-all shadow-2xs group"
        >
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>Live Website</span>
            </div>
            <x-icon name="arrow-right" class="h-3.5 w-3.5 -rotate-45 text-slate-400 group-hover:text-primary transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
        </a>
    </div>

    {{-- Navigation Links with Smooth Custom Scrollbar --}}
    <nav class="flex-1 overflow-y-auto px-2 space-y-3.5 pr-1 scrollbar-thin scrollbar-thumb-slate-200">
        @foreach ($menuGroups as $group)
            <div class="space-y-1">
                @if ($group['label'])
                    <p class="px-3 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">{{ $group['label'] }}</p>
                @endif
                <div class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        <a
                            href="{{ $item['href'] }}"
                            class="group relative flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-bold transition-all duration-150 {{ $item['active'] ? 'bg-gradient-to-r from-[#00379D] to-[#082e6e] text-white shadow-sm shadow-primary/25' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-950' }}"
                        >
                            <x-icon
                                name="{{ $item['icon'] }}"
                                class="h-4 w-4 transition-colors {{ $item['active'] ? 'text-white' : 'text-slate-400 group-hover:text-primary' }}"
                            />
                            <span class="truncate">{{ $item['name'] }}</span>
                            @if ($item['active'])
                                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    {{-- Sidebar Footer: Admin Profile & Logout --}}
    <div class="pt-3 px-2 border-t border-slate-100 mt-2">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#00379D] to-[#0F3459] text-white text-xs font-black uppercase shadow-2xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex flex-col min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate leading-none">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-slate-400 truncate mt-0.5 font-medium">{{ auth()->user()->role === 'admin' ? 'Super Admin' : 'Sub-Admin' }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    title="Log out"
                    class="flex h-8 w-8 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors shadow-2xs cursor-pointer"
                >
                    <x-icon name="log-out" class="h-3.5 w-3.5" />
                </button>
            </form>
        </div>
    </div>
</div>
