<div class="space-y-5 font-outfit max-w-lg">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.locations.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-border bg-white text-muted-foreground hover:text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-xs">
            <x-icon name="arrow-left" class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Areas in {{ $city->name }}</h1>
            <p class="text-xs text-muted">Manage the areas available under this city</p>
        </div>
    </div>

    <div class="glass-card p-6 space-y-4">
        <form wire:submit="addArea" class="flex gap-2">
            <input
                type="text"
                wire:model="addAreaName"
                placeholder="e.g. Vastrapur"
                class="flex-1 rounded-xl border border-border bg-slate-50/50 py-2 px-3 text-xs font-medium text-slate-900 outline-none transition-all focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary/20"
            />
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-3 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 transition-all cursor-pointer shrink-0">
                <x-icon name="plus" class="h-3.5 w-3.5" /> Add
            </button>
        </form>
        @error('addAreaName')
            <p class="text-[12px] font-semibold text-rose-600 -mt-2">{{ $message }}</p>
        @enderror

        <div class="space-y-1.5">
            @forelse ($areas as $area)
                <div class="flex items-center justify-between px-3 py-2 rounded-xl border border-slate-100 bg-slate-50/50">
                    <span class="text-xs font-semibold text-slate-800">{{ $area->name }}</span>
                    <button
                        wire:click="openDeleteArea({{ $area->id }}, {{ Illuminate\Support\Js::from($area->name) }})"
                        class="h-6 w-6 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center hover:bg-rose-100 transition-colors cursor-pointer shrink-0"
                        title="Delete Area"
                    >
                        <x-icon name="trash-2" class="h-3 w-3" />
                    </button>
                </div>
            @empty
                <p class="text-xs text-muted italic text-center py-6">No areas added yet for this city.</p>
            @endforelse
        </div>
    </div>

    <x-admin.confirm-modal
        :show="$deletingAreaId !== null"
        title="Delete Area"
        :message="'Are you sure you want to delete &quot;' . $deletingAreaName . '&quot;?'"
        confirm-label="Delete Area"
        variant="danger"
        confirm-action="confirmDeleteArea"
        cancel-action="cancelDeleteArea"
    />
</div>
