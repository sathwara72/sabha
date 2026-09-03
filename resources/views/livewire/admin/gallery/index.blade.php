@php
    $adminGalleryJson = $gallery->map(fn ($item, $idx) => [
        'id' => $item->id,
        'src' => media_url($item->image_path),
        'caption' => $item->caption,
        'isVideo' => is_video_file($item->image_path),
        'index' => $idx,
    ])->values();
@endphp

<div
    class="space-y-4 font-outfit"
    x-data="{
        items: {{ Illuminate\Support\Js::from($adminGalleryJson) }},
        lightboxIndex: null,
        openLightbox(i) { this.lightboxIndex = i },
        closeLightbox() { this.lightboxIndex = null },
        prev() {
            if (this.lightboxIndex !== null && this.lightboxIndex > 0) {
                this.lightboxIndex--;
            }
        },
        next() {
            if (this.lightboxIndex !== null && this.lightboxIndex < this.items.length - 1) {
                this.lightboxIndex++;
            }
        }
    }"
    x-on:keydown.window="
        if (lightboxIndex === null) return;
        if ($event.key === 'ArrowRight') next();
        if ($event.key === 'ArrowLeft') prev();
        if ($event.key === 'Escape') closeLightbox();
    "
>
    {{-- Top Header & Add Gallery Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">Gallery Management</h1>
        </div>
        <button
            type="button"
            wire:click="openAddModal"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2 text-xs font-bold text-white shadow-sm shadow-primary/20 transition-all hover:opacity-95 active:scale-[0.98] whitespace-nowrap self-start sm:self-auto cursor-pointer"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" />
            <span>Add Gallery</span>
        </button>
    </div>

    {{-- Success Alert --}}
    @if ($successMsg)
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-2xs"
        >
            <div class="flex items-center gap-2">
                <x-icon name="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0" />
                <span>{{ $successMsg }}</span>
            </div>
            <button type="button" x-on:click="show = false" class="text-emerald-600 hover:text-emerald-800">
                <x-icon name="x" class="h-3.5 w-3.5" />
            </button>
        </div>
    @endif

    {{-- Media Grid Container --}}
    <div class="space-y-4 pt-1">
        <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5">
            <h2 class="text-sm font-bold text-slate-900">Uploaded Media Library</h2>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200">
                {{ $galleryTotal }} {{ $galleryTotal === 1 ? 'item' : 'items' }}
            </span>
        </div>

        @if ($gallery->isEmpty())
            <div class="py-16 text-center text-slate-500 border-2 border-dashed border-slate-200 rounded-2xl bg-white space-y-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-primary mx-auto">
                    <x-icon name="image" class="h-6 w-6" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">No gallery media uploaded yet</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Click the "Add Gallery" button to upload photos, videos, or bulk ZIP archives.</p>
                </div>
                <button
                    type="button"
                    wire:click="openAddModal"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:opacity-95 shadow-sm cursor-pointer"
                >
                    <x-icon name="plus" class="h-3.5 w-3.5" />
                    <span>Upload First Media</span>
                </button>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach ($gallery as $index => $item)
                    @php $isVideo = is_video_file($item->image_path); @endphp
                    <div
                        x-on:click="openLightbox({{ $index }})"
                        class="bg-white p-0 overflow-hidden group relative cursor-pointer rounded-2xl border border-slate-200/90 hover:shadow-md transition-all shadow-2xs"
                    >
                        <div class="relative aspect-[4/3] w-full bg-slate-950 overflow-hidden flex items-center justify-center p-0.5">
                            @if (media_url($item->image_path))
                                @if ($isVideo)
                                    <video src="{{ media_url($item->image_path) }}" class="h-full w-full object-contain" muted preload="metadata"></video>
                                @else
                                    <img
                                        src="{{ media_url($item->image_path) }}"
                                        alt=""
                                        class="h-full w-full object-contain group-hover:scale-105 transition-transform duration-300"
                                        onerror="this.style.opacity='0.25'"
                                    />
                                @endif
                            @endif

                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/35 transition-all duration-300 flex items-center justify-center">
                                <x-icon name="zoom-in" class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 h-6 w-6 drop-shadow-md" />
                            </div>

                            <div class="absolute top-2 left-2 z-10">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black tracking-wider uppercase shadow-md {{ $isVideo ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white ring-1 ring-white/30' : 'bg-slate-950/85 backdrop-blur-md text-sky-300 ring-1 ring-white/20' }}">
                                    @if ($isVideo)
                                        <x-icon name="film" class="h-2.5 w-2.5 text-white" />
                                        <span>VIDEO</span>
                                    @else
                                        <x-icon name="image" class="h-2.5 w-2.5 text-sky-300" />
                                        <span>IMAGE</span>
                                    @endif
                                </span>
                            </div>

                            <button
                                type="button"
                                x-on:click.stop="$wire.openDelete({{ $item->id }})"
                                class="absolute top-2 right-2 z-20 rounded-lg bg-black/60 backdrop-blur-sm border border-white/20 p-1.5 text-white hover:bg-rose-600 hover:border-rose-600 transition-all shadow-sm md:opacity-0 md:group-hover:opacity-100 duration-200 cursor-pointer"
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

    {{-- Add Gallery Modal Popup (Teleported to <body> for full screen coverage) --}}
    @if ($isAddModalOpen)
        <template x-teleport="body">
            <div
                class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 font-outfit"
                x-on:keydown.escape.window="$wire.closeAddModal()"
            >
                {{-- Dark Blur Backdrop --}}
                <div
                    class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity"
                    wire:click="closeAddModal"
                ></div>

                {{-- Modal Box --}}
                <div class="relative bg-white rounded-3xl w-full max-w-lg overflow-hidden border border-slate-200 shadow-2xl z-10 flex flex-col max-h-[90vh]">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/70 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-primary shadow-2xs">
                                <x-icon name="image" class="h-4.5 w-4.5" />
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900">Add Gallery Media</h3>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">Upload photos, videos, or bulk ZIP archives</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="closeAddModal"
                            class="flex h-8 w-8 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer shadow-2xs shrink-0"
                            title="Close"
                            aria-label="Close"
                        >
                            <x-icon name="x" class="h-4 w-4" />
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 space-y-4 overflow-y-auto flex-1">
                        {{-- Error Alert --}}
                        @if ($uploadError)
                            <div class="rounded-xl bg-rose-50 border border-rose-200/80 p-3 text-xs font-semibold text-rose-800 flex items-start gap-2 shadow-2xs">
                                <x-icon name="alert-circle" class="h-4 w-4 text-rose-600 shrink-0 mt-0.5" />
                                <span class="leading-relaxed">{{ $uploadError }}</span>
                            </div>
                        @endif

                        {{-- Upload Dropzone --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Select Files</label>
                                <span class="text-[10px] text-primary font-bold bg-blue-50 border border-blue-200/60 px-2.5 py-0.5 rounded-full">Multi-Select & Bulk ZIP</span>
                            </div>

                            <div class="relative border-2 border-dashed border-slate-300 hover:border-primary rounded-2xl py-7 px-5 bg-slate-50/50 hover:bg-blue-50/20 transition-all flex flex-col items-center justify-center text-center cursor-pointer group">
                                <input
                                    type="file"
                                    multiple
                                    wire:model="mediaFiles"
                                    accept="image/*,video/*,.zip,application/zip,application/x-zip-compressed"
                                    class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                                />
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-primary mb-2 shadow-2xs group-hover:scale-105 transition-transform">
                                    <x-icon name="upload" class="h-5 w-5" />
                                </div>
                                <span class="text-sm font-bold text-slate-800">
                                    Click or drag files to upload
                                </span>
                                <span class="text-xs text-slate-400 font-medium mt-1 max-w-xs">
                                    Supports JPG, PNG, WebP, MP4, WebM or ZIP archives (up to 100MB)
                                </span>
                            </div>
                        </div>

                        {{-- Livewire Upload Loading Indicator --}}
                        <div wire:loading wire:target="mediaFiles" class="w-full text-center py-1.5">
                            <div class="inline-flex items-center gap-2 rounded-xl bg-blue-50 px-3.5 py-1.5 text-xs font-bold text-primary border border-blue-200/70 shadow-2xs">
                                <x-icon name="loader-2" class="h-3.5 w-3.5 animate-spin" />
                                <span>Uploading files from your device...</span>
                            </div>
                        </div>

                        {{-- Selected Files Preview List --}}
                        @if (count($mediaFiles) > 0)
                            <div class="space-y-2 pt-2 border-t border-slate-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-600">
                                        Selected Files ({{ count($mediaFiles) }})
                                    </span>
                                    <button
                                        type="button"
                                        wire:click="$set('mediaFiles', [])"
                                        class="text-xs font-bold text-rose-600 hover:underline cursor-pointer"
                                    >
                                        Clear all
                                    </button>
                                </div>

                                <div class="max-h-48 overflow-y-auto space-y-2 pr-1">
                                    @foreach ($mediaFiles as $idx => $file)
                                        <div class="flex items-center justify-between gap-2.5 p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs shadow-2xs">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-white border border-slate-200 shrink-0 text-slate-500">
                                                    <x-icon name="file" class="h-3.5 w-3.5" />
                                                </div>
                                                <span class="font-bold text-slate-800 truncate" title="{{ $file->getClientOriginalName() }}">
                                                    {{ $file->getClientOriginalName() }}
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono shrink-0">
                                                    ({{ round($file->getSize() / 1024) }} KB)
                                                </span>
                                            </div>
                                            <button
                                                type="button"
                                                wire:click="removeMediaFile({{ $idx }})"
                                                class="h-7 w-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors shrink-0 cursor-pointer"
                                                title="Remove file"
                                            >
                                                <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer with Proper Cancel & Upload Buttons --}}
                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/80 shrink-0">
                        <button
                            type="button"
                            wire:click="closeAddModal"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-slate-900 active:scale-[0.98] transition-all shadow-xs cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            wire:click="uploadMedia"
                            wire:loading.attr="disabled"
                            wire:target="uploadMedia"
                            :disabled="{{ count($mediaFiles) === 0 ? 'true' : 'false' }}"
                            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-primary/25 hover:opacity-95 active:scale-[0.98] disabled:opacity-50 cursor-pointer transition-all"
                        >
                            <span wire:loading.remove wire:target="uploadMedia">
                                Upload Media <x-icon name="arrow-up-right" class="h-3.5 w-3.5 inline ml-0.5" />
                            </span>
                            <span wire:loading wire:target="uploadMedia" class="flex items-center gap-1.5">
                                <x-icon name="loader-2" class="h-3.5 w-3.5 animate-spin" />
                                <span>Processing...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    @endif

    {{-- Lightbox with Left & Right Arrows (Teleported directly to <body> to cover 100% viewport) --}}
    <template x-teleport="body">
        <div
            x-show="lightboxIndex !== null"
            x-cloak
            x-on:click="closeLightbox"
            class="fixed inset-0 z-[99999] w-screen h-screen bg-black/95 backdrop-blur-md flex flex-col justify-between p-4 sm:p-6"
        >
            {{-- Top Bar --}}
            <div class="flex items-center justify-between text-white border-b border-white/10 pb-4 shrink-0" x-on:click.stop="">
                <div>
                    <h4 class="text-base sm:text-lg font-bold">Gallery Media</h4>
                    <p class="text-xs text-white/60">
                        <span x-text="lightboxIndex !== null ? (lightboxIndex + 1) + ' of ' + items.length : ''"></span>
                    </p>
                </div>
                <button
                    type="button"
                    x-on:click="closeLightbox"
                    class="h-10 w-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white cursor-pointer transition-colors"
                    aria-label="Close"
                >
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            {{-- Center Content with Left & Right Arrows --}}
            <div class="flex-1 flex items-center justify-between gap-2 sm:gap-4 py-4 min-h-0" x-on:click.stop="">
                {{-- Left Arrow --}}
                <button
                    type="button"
                    x-on:click="prev"
                    :disabled="lightboxIndex === 0"
                    class="shrink-0 h-11 w-11 sm:h-13 sm:w-13 rounded-full bg-white/10 hover:bg-white/25 disabled:opacity-20 disabled:cursor-not-allowed flex items-center justify-center text-white transition-all cursor-pointer shadow-lg"
                    title="Previous"
                >
                    <x-icon name="chevron-left" class="h-6 w-6 sm:h-7 sm:w-7" />
                </button>

                {{-- Image / Video Display --}}
                <div class="flex-1 flex items-center justify-center min-h-0 max-h-[72vh] px-2">
                    <template x-if="lightboxIndex !== null && !items[lightboxIndex]?.isVideo">
                        <img
                            :src="items[lightboxIndex]?.src"
                            :alt="items[lightboxIndex]?.caption || 'Gallery image'"
                            class="max-w-full max-h-[72vh] object-contain rounded-2xl shadow-2xl"
                        />
                    </template>
                    <template x-if="lightboxIndex !== null && items[lightboxIndex]?.isVideo">
                        <video
                            :src="items[lightboxIndex]?.src"
                            controls
                            autoplay
                            class="max-w-full max-h-[72vh] object-contain rounded-2xl shadow-2xl"
                        ></video>
                    </template>
                </div>

                {{-- Right Arrow --}}
                <button
                    type="button"
                    x-on:click="next"
                    :disabled="lightboxIndex === items.length - 1"
                    class="shrink-0 h-11 w-11 sm:h-13 sm:w-13 rounded-full bg-white/10 hover:bg-white/25 disabled:opacity-20 disabled:cursor-not-allowed flex items-center justify-center text-white transition-all cursor-pointer shadow-lg"
                    title="Next"
                >
                    <x-icon name="chevron-right" class="h-6 w-6 sm:h-7 sm:w-7" />
                </button>
            </div>

            {{-- Bottom Caption (Only shown if caption exists) --}}
            <div
                x-show="lightboxIndex !== null && items[lightboxIndex]?.caption"
                x-cloak
                class="text-center text-white py-3 max-w-3xl mx-auto border-t border-white/10 w-full shrink-0"
                x-on:click.stop=""
            >
                <p class="text-sm font-medium text-white/90" x-text="items[lightboxIndex]?.caption"></p>
            </div>
        </div>
    </template>

    {{-- Delete Modal --}}
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
