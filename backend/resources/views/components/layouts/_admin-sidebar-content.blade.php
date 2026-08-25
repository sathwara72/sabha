{{-- expects: $menuItems --}}
<div class="flex flex-col h-full">
    {{-- Logo --}}
    <div class="flex items-center justify-between gap-3 mb-8 px-1">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary text-white shrink-0">
                <x-icon name="zap" class="h-4 w-4" />
            </div>
            <span class="text-lg font-bold tracking-tight text-foreground">Sabha Admin</span>
        </div>
        <button x-on:click="sidebarOpen = false" class="lg:hidden rounded-lg p-1.5 text-muted-foreground hover:bg-surface transition-colors">
            <x-icon name="x" class="h-4.5 w-4.5" />
        </button>
    </div>

    {{-- Nav --}}
    <nav class="space-y-0.5 flex-1">
        <p class="px-3 mb-2 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">Menu</p>
        @foreach ($menuItems as $item)
            <a
                href="{{ $item['href'] }}"
                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-colors {{ $item['active'] ? 'bg-primary text-white' : 'text-muted hover:bg-surface hover:text-foreground' }}"
            >
                <x-icon name="{{ $item['icon'] }}" class="h-4 w-4" />
                <span>{{ $item['name'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Logout --}}
    <div class="pt-3 border-t border-border">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-muted transition-colors hover:bg-surface hover:text-foreground">
                <x-icon name="log-out" class="h-4 w-4" />
                <span>Log out</span>
            </button>
        </form>
    </div>
</div>
