<div class="space-y-5 font-outfit max-w-lg">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-border bg-white text-muted-foreground hover:text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-xs">
            <x-icon name="arrow-left" class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">{{ $categoryId ? 'Edit Category' : 'Add Category' }}</h1>
            <p class="text-xs text-muted">{{ $categoryId ? 'Update this business category' : 'Create a new business directory category' }}</p>
        </div>
    </div>

    <form wire:submit="save" class="glass-card p-6 space-y-4">
        <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-700">Category Name</label>
            <div class="relative">
                <x-icon name="tag" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                <input
                    type="text"
                    wire:model="name"
                    placeholder="e.g. Renewables & Solar"
                    class="w-full rounded-xl border border-border bg-slate-50/50 py-2.5 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none transition-all focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary/20"
                    autofocus
                />
            </div>
            @error('name')
                <p class="text-[12px] font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 disabled:opacity-50 transition-all cursor-pointer">
                <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-1.5"><x-icon name="{{ $categoryId ? 'pencil' : 'plus' }}" class="h-3.5 w-3.5" /> {{ $categoryId ? 'Save Changes' : 'Create Category' }}</span>
                <span wire:loading wire:target="save">{{ $categoryId ? 'Saving...' : 'Creating...' }}</span>
            </button>
        </div>
    </form>
</div>
