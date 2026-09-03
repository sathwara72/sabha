<div class="space-y-4 font-outfit" x-data="{ previewImage: null }">
    {{-- Top Header & Add Hero Image Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">Hero Slider Management</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Upload and manage rotating hero slider banner images displayed on the homepage</p>
        </div>
        <button
            type="button"
            wire:click="openCreateModal"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2 text-xs font-bold text-white shadow-sm shadow-primary/20 transition-all hover:opacity-95 active:scale-[0.98] whitespace-nowrap self-start sm:self-auto cursor-pointer"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" />
            <span>Add Hero Image</span>
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

    {{-- Slider Cards Container --}}
    <div class="space-y-4 pt-1">
        <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5">
            <h2 class="text-sm font-bold text-slate-900">Active Slider Banners</h2>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200">
                {{ $heroImages->count() }} {{ $heroImages->count() === 1 ? 'slide' : 'slides' }}
            </span>
        </div>

        @if ($heroImages->isEmpty())
            <div class="py-16 text-center text-slate-500 border-2 border-dashed border-slate-200 rounded-2xl bg-white space-y-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-primary mx-auto">
                    <x-icon name="sliders" class="h-6 w-6" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">No custom hero images uploaded</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">The homepage will display the default system banners until you upload your custom slider images.</p>
                </div>
                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:opacity-95 shadow-sm cursor-pointer"
                >
                    <x-icon name="plus" class="h-3.5 w-3.5" />
                    <span>Upload First Hero Slide</span>
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($heroImages as $index => $item)
                    <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden flex flex-col shadow-2xs hover:shadow-md transition-all group">
                        {{-- Slide Banner Preview --}}
                        <div class="relative aspect-[16/9] w-full bg-slate-900 overflow-hidden">
                            @if (media_url($item->image_path))
                                <img
                                    src="{{ media_url($item->image_path) }}"
                                    alt="{{ $item->title ?: 'Hero banner' }}"
                                    class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                            @endif

                            {{-- Top Left Link Destination Badge --}}
                            <div class="absolute top-3 left-3 z-10 max-w-[70%]">
                                @if ($item->link_type === 'event' && $item->event)
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-blue-800 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full shadow-2xs border border-blue-200/80 truncate">
                                        <x-icon name="calendar" class="h-3 w-3 text-primary shrink-0" />
                                        <span class="truncate">{{ $item->event->title }}</span>
                                    </span>
                                @elseif ($item->link_type === 'external' && $item->external_url)
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-purple-800 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full shadow-2xs border border-purple-200/80">
                                        <x-icon name="external-link" class="h-3 w-3 text-purple-600 shrink-0" />
                                        <span>Web Link</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-700 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full shadow-2xs border border-slate-200">
                                        <x-icon name="image" class="h-3 w-3 text-slate-400 shrink-0" />
                                        <span>Banner Only</span>
                                    </span>
                                @endif
                            </div>

                            {{-- Top Right Order Pill --}}
                            <div class="absolute top-3 right-3 z-10">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-slate-900/85 backdrop-blur-md text-white border border-white/20 shadow-2xs">
                                    #{{ $index + 1 }}
                                </span>
                            </div>

                            {{-- Bottom Right Zoom Preview Button --}}
                            <button
                                type="button"
                                x-on:click="previewImage = {{ Illuminate\Support\Js::from(media_url($item->image_path)) }}"
                                class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1 bg-black/60 hover:bg-black/85 backdrop-blur-md text-white text-[11px] font-bold px-2.5 py-1 rounded-xl shadow-2xs transition-all cursor-pointer hover:scale-105"
                                title="Zoom Preview"
                            >
                                <x-icon name="zoom-in" class="h-3 w-3" />
                                <span>Preview</span>
                            </button>
                        </div>

                        {{-- Card Details & Actions Body --}}
                        <div class="p-4 flex-1 flex flex-col justify-between gap-3 bg-white">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 line-clamp-1">
                                    {{ $item->title ?: 'Slide #' . ($index + 1) }}
                                </h3>
                                @if ($item->caption)
                                    <p class="text-xs text-slate-500 font-medium mt-1 line-clamp-2 leading-relaxed">
                                        {{ $item->caption }}
                                    </p>
                                @endif

                                @if ($item->link_type === 'external' && $item->external_url)
                                    <a
                                        href="{{ $item->external_url }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-primary hover:underline mt-1.5 truncate max-w-full"
                                    >
                                        <x-icon name="external-link" class="h-3 w-3 shrink-0" />
                                        <span class="truncate">{{ $item->external_url }}</span>
                                    </a>
                                @endif
                            </div>

                            {{-- Bottom Actions Row: Date/Order Info & Edit/Delete --}}
                            <div class="flex items-center justify-between pt-3 border-t border-slate-100 gap-2">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-400">
                                    <x-icon name="calendar" class="h-3 w-3 text-slate-400" />
                                    <span>{{ $item->created_at->format('M j, Y') }}</span>
                                </span>

                                {{-- Edit & Delete Actions --}}
                                <div class="flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        wire:click="openEditModal({{ $item->id }})"
                                        class="inline-flex items-center gap-1 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200/80 px-3 py-1.5 rounded-xl transition-all active:scale-95 cursor-pointer shadow-2xs"
                                    >
                                        <x-icon name="pencil" class="h-3.5 w-3.5 text-amber-600" />
                                        <span>Edit</span>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="openDelete({{ $item->id }})"
                                        class="inline-flex items-center gap-1 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-3 py-1.5 rounded-xl transition-all active:scale-95 cursor-pointer shadow-2xs"
                                    >
                                        <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Hero Slide Form Modal (Create & Edit) Teleported to <body> --}}
    @if ($isFormModalOpen)
        <template x-teleport="body">
            <div
                class="fixed inset-0 z-[99999] overflow-y-auto p-3 sm:p-6 flex min-h-full items-center justify-center font-outfit"
                x-on:keydown.escape.window="$wire.closeFormModal()"
            >
                {{-- Dark Blur Backdrop --}}
                <div
                    class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity"
                    wire:click="closeFormModal"
                ></div>

                {{-- Modal Box --}}
                <div class="relative bg-white rounded-3xl w-full max-w-xl overflow-hidden border border-slate-200 shadow-2xl z-10 my-auto flex flex-col max-h-[92vh]">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/75 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-primary shadow-2xs">
                                <x-icon name="sliders" class="h-4.5 w-4.5" />
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">
                                    {{ $editingId ? 'Edit Hero Slide' : 'Add Hero Image' }}
                                </h3>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">
                                    {{ $editingId ? 'Update slide banner image, link, and details' : 'Upload a banner image to appear in the homepage slider' }}
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="closeFormModal"
                            class="flex h-8 w-8 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer shadow-2xs shrink-0"
                            title="Close"
                            aria-label="Close"
                        >
                            <x-icon name="x" class="h-4 w-4" />
                        </button>
                    </div>

                    {{-- Modal Body Form --}}
                    <form wire:submit="saveSlide" class="flex flex-col flex-1 overflow-hidden">
                        <div class="p-6 space-y-4 overflow-y-auto flex-1">
                            @if ($formError)
                                <div class="rounded-xl bg-rose-50 border border-rose-200/80 p-2.5 text-xs font-semibold text-rose-800 flex items-start gap-2 shadow-2xs">
                                    <x-icon name="alert-circle" class="h-4 w-4 text-rose-600 shrink-0 mt-0.5" />
                                    <span>{{ $formError }}</span>
                                </div>
                            @endif

                            {{-- Image Dropzone & Preview --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center justify-between">
                                    <span>Banner Image <span class="text-rose-500">*</span></span>
                                    <span class="text-[10px] text-slate-400 font-medium">1920×800px landscape (Max 10MB)</span>
                                </label>

                                <div class="relative border-2 border-dashed border-slate-300 hover:border-primary rounded-2xl p-3 bg-slate-50/60 hover:bg-blue-50/20 transition-all flex flex-col items-center justify-center text-center cursor-pointer group">
                                    <input
                                        type="file"
                                        wire:model="mediaFile"
                                        accept="image/png,image/jpeg,image/webp,image/jpg"
                                        class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                                    />

                                    @if ($mediaFile)
                                        <div class="w-full h-32 sm:h-36 rounded-xl overflow-hidden bg-slate-900 border border-slate-200 relative shadow-sm">
                                            <img src="{{ $mediaFile->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover" />
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <span class="text-xs font-bold text-white bg-black/75 px-3 py-1 rounded-xl">Click to change image</span>
                                            </div>
                                        </div>
                                    @elseif ($imagePreviewUrl)
                                        <div class="w-full h-32 sm:h-36 rounded-xl overflow-hidden bg-slate-900 border border-slate-200 relative shadow-sm">
                                            <img src="{{ $imagePreviewUrl }}" alt="Current Slide" class="h-full w-full object-cover" />
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <span class="text-xs font-bold text-white bg-black/75 px-3 py-1 rounded-xl">Click to change image</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="py-4 flex flex-col items-center justify-center">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-primary mb-1.5 shadow-2xs group-hover:scale-105 transition-transform">
                                                <x-icon name="upload" class="h-5 w-5" />
                                            </div>
                                            <span class="text-xs font-bold text-slate-800">
                                                Click or drag banner image here
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium mt-0.5">
                                                Supports JPG, PNG, WebP (Landscape Recommended)
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                @error('mediaFile') <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            {{-- 2-Column: Title & Caption --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Title (Optional)</label>
                                    <input
                                        type="text"
                                        wire:model="title"
                                        placeholder="e.g. Annual Summit 2026"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs font-semibold text-slate-900 outline-none focus:border-primary focus:bg-white transition-colors"
                                    />
                                    @error('title') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Caption (Optional)</label>
                                    <input
                                        type="text"
                                        wire:model="caption"
                                        placeholder="e.g. Join us with family..."
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs font-semibold text-slate-900 outline-none focus:border-primary focus:bg-white transition-colors"
                                    />
                                    @error('caption') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Click Destination Action --}}
                            <div class="space-y-2 pt-1 border-t border-slate-100">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Clicking This Slide Opens...</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <label class="flex items-center justify-center gap-1.5 p-2.5 rounded-xl border cursor-pointer transition-all {{ $linkType === 'none' ? 'border-primary bg-blue-50/80 text-primary font-bold shadow-2xs' : 'border-slate-200 bg-slate-50/50 text-slate-600 font-semibold hover:bg-slate-50' }}">
                                        <input type="radio" wire:model.live="linkType" value="none" class="sr-only" />
                                        <x-icon name="image" class="h-4 w-4" />
                                        <span class="text-xs">Banner Only</span>
                                    </label>
                                    <label class="flex items-center justify-center gap-1.5 p-2.5 rounded-xl border cursor-pointer transition-all {{ $linkType === 'event' ? 'border-primary bg-blue-50/80 text-primary font-bold shadow-2xs' : 'border-slate-200 bg-slate-50/50 text-slate-600 font-semibold hover:bg-slate-50' }}">
                                        <input type="radio" wire:model.live="linkType" value="event" class="sr-only" />
                                        <x-icon name="calendar" class="h-4 w-4" />
                                        <span class="text-xs">Open Event</span>
                                    </label>
                                    <label class="flex items-center justify-center gap-1.5 p-2.5 rounded-xl border cursor-pointer transition-all {{ $linkType === 'external' ? 'border-primary bg-blue-50/80 text-primary font-bold shadow-2xs' : 'border-slate-200 bg-slate-50/50 text-slate-600 font-semibold hover:bg-slate-50' }}">
                                        <input type="radio" wire:model.live="linkType" value="external" class="sr-only" />
                                        <x-icon name="external-link" class="h-4 w-4" />
                                        <span class="text-xs">Web Link</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Dynamic Input: Event Selection --}}
                            @if ($linkType === 'event')
                                <div class="space-y-1.5 p-3 rounded-xl bg-blue-50/60 border border-blue-200/60">
                                    <label class="text-xs font-bold text-slate-800 uppercase tracking-wider">Select Linked Event <span class="text-rose-500">*</span></label>
                                    <select
                                        wire:model="eventId"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-900 outline-none focus:border-primary"
                                    >
                                        <option value="">-- Choose an Event --</option>
                                        @foreach ($events as $event)
                                            <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->date->format('M j, Y') }})</option>
                                        @endforeach
                                    </select>
                                    @error('eventId') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            {{-- Dynamic Input: External URL --}}
                            @if ($linkType === 'external')
                                <div class="space-y-1.5 p-3 rounded-xl bg-purple-50/60 border border-purple-200/60">
                                    <label class="text-xs font-bold text-slate-800 uppercase tracking-wider">Destination Website URL <span class="text-rose-500">*</span></label>
                                    <input
                                        type="url"
                                        wire:model="externalUrl"
                                        placeholder="https://example.com"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-900 outline-none focus:border-primary"
                                    />
                                    @error('externalUrl') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/80 shrink-0">
                            <button
                                type="button"
                                x-on:click="$wire.closeFormModal()"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-slate-900 active:scale-[0.98] transition-all shadow-xs cursor-pointer"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="saveSlide,mediaFile"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-primary/25 hover:opacity-95 active:scale-[0.98] disabled:opacity-50 cursor-pointer transition-all"
                            >
                                <span wire:loading.remove wire:target="saveSlide,mediaFile">
                                    {{ $editingId ? 'Update Slide' : 'Save Hero Slide' }}
                                </span>
                                <span wire:loading wire:target="saveSlide,mediaFile" class="flex items-center gap-1.5">
                                    <x-icon name="loader-2" class="h-3.5 w-3.5 animate-spin" />
                                    <span>Saving...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

    {{-- Preview Lightbox Teleported to <body> --}}
    <template x-teleport="body">
        <div
            x-show="previewImage"
            x-cloak
            x-on:click="previewImage = null"
            class="fixed inset-0 z-[99999] w-screen h-screen bg-black/95 backdrop-blur-md flex items-center justify-center p-4 sm:p-6"
        >
            <button
                type="button"
                x-on:click="previewImage = null"
                class="absolute top-4 right-4 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2.5 transition-colors cursor-pointer"
                aria-label="Close"
            >
                <x-icon name="x" class="h-6 w-6" />
            </button>
            <div class="relative z-40 max-w-5xl max-h-[85vh] w-full flex items-center justify-center" x-on:click.stop="">
                <img :src="previewImage" alt="Hero Preview" class="max-h-[80vh] max-w-full object-contain rounded-2xl shadow-2xl" />
            </div>
        </div>
    </template>

    {{-- Delete Modal --}}
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
