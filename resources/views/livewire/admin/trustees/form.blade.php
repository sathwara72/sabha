<div class="space-y-5 font-outfit max-w-lg">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.trustees.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-border bg-white text-muted-foreground hover:text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-xs">
            <x-icon name="arrow-left" class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">{{ $trusteeId ? 'Edit Trustee' : 'Add Trustee' }}</h1>
            <p class="text-xs text-muted">{{ $trusteeId ? 'Update this trustee\'s details' : 'Feature a member on the public Trustees page' }}</p>
        </div>
    </div>

    <form wire:submit="save" class="glass-card p-6 space-y-4">
        @if ($trusteeId)
            <div>
                <label class="text-xs font-bold text-slate-700 mb-1 block">Member</label>
                <div class="w-full rounded-xl border border-border bg-slate-50 py-2.5 px-3.5 text-xs font-semibold text-slate-600">
                    {{ $trustee?->user?->name ?? 'Deleted user' }} <span class="font-normal text-slate-400">({{ $trustee?->user?->email }})</span>
                </div>
            </div>
        @else
            <div>
                <label class="text-xs font-bold text-slate-700 mb-1 block">Select Member</label>
                <x-searchable-select
                    wire-model="userId"
                    :options="$candidateLabels"
                    :value-map="$candidateValueMap"
                    :allow-custom="false"
                    placeholder="Search by name or email..."
                    leading-icon="user"
                    wire-key="add-trustee-member"
                />
                @error('userId') <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>
        @endif

        <div>
            <label class="text-xs font-bold text-slate-700 mb-1 block">Position</label>
            <input type="text" wire:model="position" placeholder="e.g. President, Secretary, Treasurer" class="w-full rounded-xl border border-border bg-slate-50/50 py-2.5 px-3.5 text-xs font-medium text-slate-900 outline-none focus:bg-white focus:border-primary" />
            @error('position') <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p> @enderror
        </div>

        @if ($trusteeId)
            <div>
                <label class="text-xs font-bold text-slate-700 mb-1 block">Display Order</label>
                <input type="number" min="0" wire:model="sortOrder" class="w-full rounded-xl border border-border bg-slate-50/50 py-2.5 px-3.5 text-xs font-medium text-slate-900 outline-none focus:bg-white focus:border-primary" />
                @error('sortOrder') <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                <input type="checkbox" wire:model="isActive" class="rounded border-border text-primary focus:ring-primary/30" />
                Visible on public Trustees page
            </label>
        @endif

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <a href="{{ route('admin.trustees.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 cursor-pointer">Cancel</a>
            <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 cursor-pointer">
                <span wire:loading.remove wire:target="save">{{ $trusteeId ? 'Save Changes' : 'Add Trustee' }}</span>
                <span wire:loading wire:target="save">{{ $trusteeId ? 'Saving...' : 'Adding...' }}</span>
            </button>
        </div>
    </form>
</div>
