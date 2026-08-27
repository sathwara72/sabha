<div class="space-y-3" x-data="{ lightbox: null }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex flex-col">
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Gallery management</h1>
            <p class="text-xs text-muted">Upload and manage images and videos for the community gallery</p>
        </div>
        <button
            wire:click="openUploadModal"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] whitespace-nowrap self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" /> Add Gallery
        </button>
    </div>

    <div class="space-y-4 pt-2">
        <div class="flex items-center justify-between border-b border-border pb-2.5">
            <h2 class="text-sm font-bold text-foreground">Uploaded Media</h2>
            <span class="text-xs font-semibold text-muted">{{ $galleryTotal }} {{ $galleryTotal === 1 ? 'item' : 'items' }}</span>
        </div>

        @if ($gallery->isEmpty())
            <div class="glass-card py-20 text-center text-muted border border-dashed border-border rounded-xl">
                No gallery media uploaded yet. Click the "Add Gallery" button above to upload your first file.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($gallery as $item)
                    @php $isVideo = is_video_file($item->image_path); @endphp
                    <div
                        x-on:click="lightbox = { src: {{ Illuminate\Support\Js::from(media_url($item->image_path)) }}, caption: {{ Illuminate\Support\Js::from($item->caption) }}, isVideo: {{ $isVideo ? 'true' : 'false' }} }"
                        class="glass-card p-0 overflow-hidden group relative cursor-pointer rounded-2xl border border-border hover:shadow-md transition-all"
                    >
                        <div class="relative h-48 w-full bg-slate-900 overflow-hidden">
                            @if (media_url($item->image_path))
                                @if ($isVideo)
                                    <video src="{{ media_url($item->image_path) }}" class="h-full w-full object-cover" muted preload="metadata"></video>
                                @else
                                    <img src="{{ media_url($item->image_path) }}" alt="{{ $item->caption ?: 'Gallery image' }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                @endif
                            @endif

                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                                <x-icon name="zoom-in" class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 h-7 w-7" />
                            </div>

                            <div class="absolute top-2 left-2 z-10">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[12px] font-bold tracking-wider uppercase shadow-2xs backdrop-blur-md border {{ $isVideo ? 'bg-white/92 text-amber-700 border-amber-200/80' : 'bg-white/92 text-primary border-primary/20' }}">
                                    @if ($isVideo)
                                        <x-icon name="film" class="h-2.5 w-2.5 text-amber-500" /> Video
                                    @else
                                        <x-icon name="image" class="h-2.5 w-2.5 text-primary" /> Image
                                    @endif
                                </span>
                            </div>

                            <button
                                type="button"
                                x-on:click.stop="$wire.openDelete({{ $item->id }})"
                                class="absolute top-2.5 right-2.5 z-20 rounded-xl bg-red-50/90 border border-red-100 p-2 text-red-600 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all shadow-sm md:opacity-0 md:group-hover:opacity-100 duration-200"
                                title="Delete media"
                            >
                                <x-icon name="trash-2" class="h-3 w-3" />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <x-pagination :paginator="$gallery" item-label="items" />
        @endif
    </div>

    {{-- Lightbox --}}
    <div x-show="lightbox" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/90 backdrop-blur-md" x-on:click="lightbox = null"></div>
        <button x-on:click="lightbox = null" class="absolute top-4 right-4 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2 transition-colors">
            <x-icon name="x" class="h-5 w-5" />
        </button>
        <div class="relative z-40 max-w-5xl w-full mx-auto px-14 flex flex-col items-center gap-4">
            <template x-if="lightbox && !lightbox.isVideo">
                <img :src="lightbox?.src" :alt="lightbox?.caption" class="max-h-[75vh] max-w-full object-contain rounded-2xl shadow-2xl" />
            </template>
            <template x-if="lightbox && lightbox.isVideo">
                <video :src="lightbox?.src" controls autoplay class="max-h-[75vh] max-w-full rounded-2xl shadow-2xl"></video>
            </template>
            <div class="flex flex-col items-center gap-1 text-center">
                <p class="text-sm font-medium text-white/80 max-w-xl" x-show="lightbox?.caption" x-text="lightbox?.caption"></p>
            </div>
        </div>
    </div>

    {{-- Upload Modal --}}
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeUploadModal"></div>
            <div x-data x-show="true" x-transition class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl border border-border">
                <div class="flex items-start justify-between border-b border-border pb-3 mb-4">
                    <h2 class="text-base font-bold text-foreground flex items-center gap-2">
                        <x-icon name="upload" class="h-4 w-4 text-primary" /> Upload Media
                    </h2>
                    <button wire:click="closeUploadModal" class="rounded-lg p-1 text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

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
                        <button type="button" wire:click="closeUploadModal" class="rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground hover:bg-slate-50 active:scale-95 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="upload" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer transition-all">
                            <span wire:loading.remove wire:target="upload">Upload <x-icon name="plus" class="h-3.5 w-3.5 inline" /></span>
                            <span wire:loading wire:target="upload">Uploading...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <x-admin.confirm-modal
        :show="$deleteId !== null"
        title="Delete Gallery Media"
        message="Are you sure you want to delete this gallery media? This action cannot be undone."
        confirm-label="Delete"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
