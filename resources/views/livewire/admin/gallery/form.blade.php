<div class="space-y-5 max-w-lg">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.gallery.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-border bg-white text-muted-foreground hover:text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-xs">
            <x-icon name="arrow-left" class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Add Gallery Media</h1>
            <p class="text-xs text-muted">Upload images, videos, or ZIP archives to the community gallery</p>
        </div>
    </div>

    <div class="glass-card p-5">
        @if ($uploadError)
            <div class="rounded-xl bg-red-50 border border-red-100 p-3 text-xs font-semibold text-red-600 flex items-center gap-2 mb-4">
                <x-icon name="alert-circle" class="h-3.5 w-3.5 shrink-0" /><span>{{ $uploadError }}</span>
            </div>
        @endif

        <form wire:submit="upload" class="space-y-4">
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-foreground flex items-center justify-between">
                    <span>Select Files (Images, Videos or ZIP Archives)</span>
                    <span class="text-[12px] text-primary font-bold bg-primary-soft px-1.5 py-0.5 rounded">Multi-Select Enabled</span>
                </label>
                <div class="relative border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer flex flex-col items-center justify-center">
                    <input
                        type="file"
                        multiple
                        wire:model="mediaFiles"
                        accept="image/*,video/*,.zip,application/zip,application/x-zip-compressed"
                        class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                    />
                    <x-icon name="upload" class="h-6 w-6 text-primary mb-2" />
                    <span class="text-xs font-semibold text-foreground text-center line-clamp-1 px-2">
                        @if (count($mediaFiles) > 0)
                            {{ count($mediaFiles) }} file{{ count($mediaFiles) > 1 ? 's' : '' }} selected ({{ collect($mediaFiles)->map(fn ($f) => $f->getClientOriginalName())->implode(', ') }})
                        @else
                            Click to select single or multiple files
                        @endif
                    </span>
                    <span class="text-[12px] text-muted-foreground mt-1">Select multiple images/videos or ZIP archives (up to 100MB)</span>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-border pt-4 mt-2">
                <a href="{{ route('admin.gallery.index') }}" class="rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground hover:bg-slate-50 active:scale-95 transition-colors">
                    Cancel
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:target="upload" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer transition-all">
                    <span wire:loading.remove wire:target="upload">Upload <x-icon name="plus" class="h-3.5 w-3.5 inline" /></span>
                    <span wire:loading wire:target="upload">Uploading...</span>
                </button>
            </div>
        </form>
    </div>
</div>
