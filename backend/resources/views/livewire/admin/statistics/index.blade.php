<div class="space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex flex-col">
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Website statistics</h1>
            <p class="text-xs text-muted">Manage numbers and labels displayed across the website</p>
        </div>
        <button
            wire:click="refreshData"
            class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-border bg-white text-muted hover:bg-surface hover:text-foreground cursor-pointer transition-colors self-start sm:self-auto"
            title="Refresh Data"
        >
            <x-icon name="refresh-cw" class="h-3.5 w-3.5" />
        </button>
    </div>

    @if ($successMsg)
        <div wire:key="stat-success-{{ md5($successMsg) }}" x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 3000)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-800 flex items-center gap-2">
            <x-icon name="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0" />
            <span>{{ $successMsg }}</span>
        </div>
    @endif
    @if ($errorMsg)
        <div class="rounded-xl bg-red-50 border border-red-100 px-3 py-2 text-xs font-semibold text-red-800 flex items-center gap-2">
            <x-icon name="alert-circle" class="h-4 w-4 text-red-600 shrink-0" />
            <span>{{ $errorMsg }}</span>
        </div>
    @endif

    @if ($stats->isEmpty())
        <div class="glass-card py-20 text-center text-muted border border-dashed border-border rounded-xl text-xs">
            No statistics found. Run the database seeder to populate default values.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach ($stats as $stat)
                <div class="glass-card p-4 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1 text-[10px] font-bold text-primary bg-primary-soft px-2 py-0.5 rounded-md">
                            <x-icon name="layers" class="h-2.5 w-2.5" /> Stat #{{ $stat->id }}
                        </span>
                        <span class="text-[10px] text-muted-foreground font-semibold truncate max-w-[140px]">
                            Live: <span class="text-foreground">{{ $stat->value }}</span> — {{ $stat->label }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-0.5">
                            <label class="text-[10px] font-bold text-muted uppercase tracking-wider block">Label</label>
                            <input
                                type="text"
                                wire:model="editValues.{{ $stat->id }}.label"
                                class="w-full rounded-lg border border-border bg-white px-3 py-1.5 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold"
                                placeholder="e.g. Active Members"
                            />
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[10px] font-bold text-muted uppercase tracking-wider block">Value</label>
                            <input
                                type="text"
                                wire:model="editValues.{{ $stat->id }}.value"
                                class="w-full rounded-lg border border-border bg-white px-3 py-1.5 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold"
                                placeholder="e.g. 500+"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button
                            wire:click="update({{ $stat->id }})"
                            wire:loading.attr="disabled"
                            wire:target="update({{ $stat->id }})"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-[11px] font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                        >
                            <x-icon name="save" class="h-3 w-3" />
                            <span wire:loading.remove wire:target="update({{ $stat->id }})">Save</span>
                            <span wire:loading wire:target="update({{ $stat->id }})">Saving...</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
