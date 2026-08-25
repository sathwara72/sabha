<div class="space-y-3">
    <div class="flex flex-col">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">Dashboard</h1>
        <p class="text-sm text-muted">Overview of your community</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
        @foreach ($tiles as $tile)
            <div class="glass-card p-3 flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $tile['soft'] }}">
                    <x-icon name="{{ $tile['icon'] }}" class="h-4.5 w-4.5" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-lg font-bold text-foreground leading-none">{{ $tile['value'] }}</p>
                    <p class="mt-1 text-[11px] font-semibold text-muted truncate">{{ $tile['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
        <div class="glass-card p-5">
            <h3 class="mb-4 flex items-center gap-2 text-base font-bold text-foreground">
                <x-icon name="star" class="h-4.5 w-4.5 text-accent" />
                Alerts
            </h3>
            <div class="space-y-2">
                @foreach ($alerts as $msg)
                    <div class="flex items-center gap-3 rounded-xl bg-surface p-3 transition-colors hover:bg-primary-soft">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                            <x-icon name="shield-check" class="h-4 w-4" />
                        </div>
                        <p class="text-sm font-semibold text-foreground">{{ $msg }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="glass-card p-5">
            <h3 class="mb-4 flex items-center gap-2 text-base font-bold text-foreground">
                <x-icon name="clock" class="h-4.5 w-4.5 text-primary" />
                Recent activity
            </h3>
            <div class="space-y-2">
                @foreach ($recentActivities as $log)
                    <div class="flex items-center justify-between rounded-xl bg-surface p-3">
                        <div class="flex flex-col gap-0.5">
                            <p class="text-sm font-bold text-foreground">{{ $log['user'] }}</p>
                            <p class="text-xs text-muted font-semibold">{{ $log['action'] }}</p>
                        </div>
                        <p class="text-xs font-semibold text-muted-foreground">{{ $log['time'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
