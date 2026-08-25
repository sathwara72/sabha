<div class="space-y-3" x-data="{ previewImage: null }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex flex-col">
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Hero Slider Management</h1>
            <p class="text-xs text-muted">Upload and manage rotating hero slider banner images displayed on the homepage</p>
        </div>
        <button
            wire:click="openUploadModal"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] whitespace-nowrap self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" /> Add Hero Image
        </button>
    </div>

    <div class="space-y-4 pt-2">
        <div class="flex items-center justify-between border-b border-border pb-2.5">
            <h2 class="text-sm font-bold text-foreground">Slider Images</h2>
            <span class="text-xs font-semibold text-muted">{{ $heroImages->count() }} {{ $heroImages->count() === 1 ? 'image' : 'images' }}</span>
        </div>

        @if ($heroImages->isEmpty())
            <div class="glass-card py-20 text-center text-muted border border-dashed border-border rounded-xl">
                No custom hero images uploaded yet. The homepage will display the default system images. Click "Add Hero Image" to upload your first slider image.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($heroImages as $item)
                    <div class="glass-card p-0 overflow-hidden flex flex-col group relative">
                        <div class="relative h-40 w-full bg-slate-900 overflow-hidden">
                            @if (media_url($item->image_path))
                                <img src="{{ media_url($item->image_path) }}" alt="{{ $item->title ?: 'Hero banner' }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            @endif

                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-3">
                                <button
                                    x-on:click="previewImage = {{ Illuminate\Support\Js::from(media_url($item->image_path)) }}"
                                    class="p-2 bg-white/20 hover:bg-white/35 text-white rounded-lg transition-colors cursor-pointer"
                                    title="Preview Image"
                                >
                                    <x-icon name="zoom-in" class="h-4.5 w-4.5" />
                                </button>
                                <button
                                    wire:click="openDelete({{ $item->id }})"
                                    class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors cursor-pointer"
                                    title="Delete Image"
                                >
                                    <x-icon name="trash-2" class="h-4.5 w-4.5" />
                                </button>
                            </div>

                            <div class="absolute top-2 left-2 z-10">
                                <span class="flex items-center gap-1 text-[10px] font-bold text-white bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded-full">
                                    <x-icon name="image" class="h-2.5 w-2.5 text-primary-soft" /> Slider Banner
                                </span>
                            </div>
                        </div>

                        <div class="p-3 flex-1 flex flex-col gap-1 bg-white">
                            <p class="text-[11px] text-muted">
                                Uploaded: {{ $item->created_at->format('M j, Y') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Preview Lightbox --}}
    <div x-show="previewImage" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/90 backdrop-blur-md" x-on:click="previewImage = null"></div>
        <button x-on:click="previewImage = null" class="absolute top-4 right-4 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2 transition-colors cursor-pointer">
            <x-icon name="x" class="h-5 w-5" />
        </button>
        <div class="relative z-40 max-w-5xl max-h-[85vh] w-full flex items-center justify-center">
            <img :src="previewImage" alt="Hero Preview" class="max-h-[80vh] max-w-full object-contain rounded-2xl shadow-2xl" />
        </div>
    </div>

    {{-- Upload Modal --}}
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeUploadModal"></div>
            <div x-data x-show="true" x-transition class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl border border-border">
                <div class="flex items-start justify-between border-b border-border pb-3 mb-4">
                    <h2 class="text-base font-bold text-foreground flex items-center gap-2">
                        <x-icon name="upload" class="h-4 w-4 text-primary" /> Upload Hero Image
                    </h2>
                    <button wire:click="closeUploadModal" class="rounded-lg p-1 text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                @if ($uploadError)
                    <div class="rounded-xl bg-red-50 border border-red-100 p-3 text-xs font-semibold text-red-600 flex items-center gap-2 mb-4">
                        <x-icon name="alert-circle" class="h-3.5 w-3.5 shrink-0" /><span>{{ $uploadError }}</span>
                    </div>
                @endif
                @error('mediaFile')
                    <div class="rounded-xl bg-red-50 border border-red-100 p-3 text-xs font-semibold text-red-600 flex items-center gap-2 mb-4">
                        <x-icon name="alert-circle" class="h-3.5 w-3.5 shrink-0" /><span>{{ $message }}</span>
                    </div>
                @enderror

                <form wire:submit="upload" class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-foreground">Select Slider Image</label>
                        <div class="relative border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer flex flex-col items-center justify-center">
                            <input type="file" wire:model="mediaFile" accept="image/*" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                            <x-icon name="upload" class="h-6 w-6 text-primary mb-2" />
                            <span class="text-xs font-semibold text-foreground text-center line-clamp-1 px-2">
                                {{ $mediaFile ? $mediaFile->getClientOriginalName() : 'Click to select banner image' }}
                            </span>
                            <span class="text-[10px] text-muted-foreground mt-1">Recommended aspect ratio: 21:9 or landscape</span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-border pt-4 mt-2">
                        <button type="button" wire:click="closeUploadModal" class="rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground hover:bg-slate-50 active:scale-95 transition-colors cursor-pointer">
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
        title="Delete Hero Slider Image"
        message="Are you sure you want to delete this hero slider image? This action cannot be undone."
        confirm-label="Delete"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
