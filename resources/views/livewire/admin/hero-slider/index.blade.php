<div class="space-y-3" x-data="{ previewImage: null }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex flex-col">
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Hero Slider Management</h1>
            <p class="text-xs text-muted">Upload and manage rotating hero slider banner images displayed on the homepage</p>
        </div>
        <a
            href="{{ route('admin.hero-slider.create') }}"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] whitespace-nowrap self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" /> Add Hero Image
        </a>
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
                @foreach ($heroImages as $index => $item)
                    <div class="glass-card p-0 overflow-hidden flex flex-col group relative">
                        <div class="relative h-40 w-full bg-slate-900 overflow-hidden">
                            @if (media_url($item->image_path))
                                <img src="{{ media_url($item->image_path) }}" alt="{{ $item->title ?: 'Hero banner' }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            @endif

                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-2">
                                <button
                                    x-on:click="previewImage = {{ Illuminate\Support\Js::from(media_url($item->image_path)) }}"
                                    class="p-2 bg-white/20 hover:bg-white/35 text-white rounded-lg transition-colors cursor-pointer"
                                    title="Preview Image"
                                >
                                    <x-icon name="zoom-in" class="h-4.5 w-4.5" />
                                </button>
                                <a
                                    href="{{ route('admin.hero-slider.edit', $item->id) }}"
                                    class="p-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors cursor-pointer"
                                    title="Edit Slide"
                                >
                                    <x-icon name="pencil" class="h-4.5 w-4.5" />
                                </a>
                                <button
                                    wire:click="openDelete({{ $item->id }})"
                                    class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors cursor-pointer"
                                    title="Delete Image"
                                >
                                    <x-icon name="trash-2" class="h-4.5 w-4.5" />
                                </button>
                            </div>

                            <div class="absolute top-2 left-2 z-10">
                                @if ($item->link_type === 'event' && $item->event)
                                    <span class="flex items-center gap-1 text-[12px] font-bold text-white bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded-full max-w-[90%] truncate">
                                        <x-icon name="calendar" class="h-2.5 w-2.5 text-primary-soft shrink-0" /> <span class="truncate">{{ $item->event->title }}</span>
                                    </span>
                                @elseif ($item->link_type === 'external' && $item->external_url)
                                    <span class="flex items-center gap-1 text-[12px] font-bold text-white bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded-full max-w-[90%] truncate">
                                        <x-icon name="external-link" class="h-2.5 w-2.5 text-primary-soft shrink-0" /> <span class="truncate">{{ $item->external_url }}</span>
                                    </span>
                                @else
                                    <span class="flex items-center gap-1 text-[12px] font-bold text-white bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded-full">
                                        <x-icon name="image" class="h-2.5 w-2.5 text-primary-soft" /> Not clickable
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-3 flex-1 flex flex-col gap-2 bg-white">
                            <p class="text-[12px] text-muted">
                                {{ $item->title ?: 'Uploaded: ' . $item->created_at->format('M j, Y') }}
                            </p>
                            <div class="flex items-center gap-1.5">
                                <button
                                    wire:click="moveUp({{ $item->id }})"
                                    @disabled($index === 0)
                                    class="h-6 w-6 rounded-lg border border-border bg-slate-50 text-slate-600 flex items-center justify-center transition-all hover:bg-slate-100 active:scale-95 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                    title="Move up"
                                >
                                    <x-icon name="chevron-right" class="h-3.5 w-3.5 -rotate-90" />
                                </button>
                                <button
                                    wire:click="moveDown({{ $item->id }})"
                                    @disabled($index === $heroImages->count() - 1)
                                    class="h-6 w-6 rounded-lg border border-border bg-slate-50 text-slate-600 flex items-center justify-center transition-all hover:bg-slate-100 active:scale-95 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                                    title="Move down"
                                >
                                    <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-90" />
                                </button>
                                <span class="text-[12px] font-bold text-muted-foreground">Order #{{ $index + 1 }}</span>
                            </div>
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
