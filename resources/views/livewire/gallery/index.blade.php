@php
    $commonItemsJson = $common->map(function ($item, $idx) {
        $isVideo = is_video_file($item->image_path);
        return [
            'id' => $item->id,
            'src' => media_url($item->image_path),
            'caption' => $item->caption,
            'isVideo' => $isVideo,
            'index' => $idx,
        ];
    })->values();
@endphp

<div
    class="bg-background font-outfit min-h-screen"
    x-data="{
        items: {{ Illuminate\Support\Js::from($commonItemsJson) }},
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
    <x-page-header :kicker="__('site.gallery.label')" :title="__('site.gallery.title')" :subtitle="__('site.gallery.subtitle')" />

    {{-- Stats Section --}}
    <section class="border-b border-border bg-surface">
        <div class="mx-auto max-w-7xl px-6 py-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <div class="glass-card flex items-start gap-4 p-5 md:col-span-2">
                    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                        <x-icon name="camera" class="h-5 w-5" />
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-base font-semibold text-foreground">{{ __('site.gallery.visual_legacy_title') }}</h2>
                        <p class="text-xs leading-relaxed text-muted">{{ __('site.gallery.visual_legacy_desc') }}</p>
                    </div>
                </div>

                <div class="glass-card flex items-center gap-4 p-5">
                    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                        <x-icon name="users" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-foreground leading-none">{{ $membersValue }}</p>
                        <p class="mt-1.5 text-xs font-medium text-muted">{{ __('site.gallery.members') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Gallery Section --}}
    <section class="mx-auto max-w-7xl px-6 py-16 lg:py-10 space-y-10">
        {{-- 1. Event Folders --}}
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <div class="h-8 w-1 rounded-full bg-primary"></div>
                <h2 class="text-2xl font-bold text-foreground">{{ __('site.gallery.event_folders') }}</h2>
            </div>
            <p class="text-sm text-muted">{{ __('site.gallery.event_folders_desc') }}</p>

            @if ($eventFolders->isEmpty())
                <p class="text-sm text-muted italic">{{ __('site.gallery.no_folders') }}</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($eventFolders as $folder)
                        <a href="/gallery/event/{{ $folder->event->id }}" class="group relative cursor-pointer block">
                            <div class="group relative cursor-pointer transition-transform hover:-translate-y-1">
                                <div class="absolute inset-0 bg-primary/10 rounded-2xl translate-x-2 translate-y-2 group-hover:translate-x-3 group-hover:translate-y-3 transition-transform"></div>
                                <div class="absolute inset-0 bg-primary/5 rounded-2xl translate-x-1 translate-y-1"></div>

                                <div class="glass-card p-0 overflow-hidden relative border border-border bg-white rounded-2xl shadow-sm z-10 flex flex-col h-80">
                                    <div class="relative h-48 w-full bg-slate-900 overflow-hidden">
                                        @if ($folder->firstItem && media_url($folder->firstItem->image_path))
                                            @if (is_video_file($folder->firstItem->image_path))
                                                <video src="{{ media_url($folder->firstItem->image_path) }}" class="h-full w-full object-cover opacity-80" muted preload="metadata"></video>
                                            @else
                                                <x-safe-image :src="media_url($folder->firstItem->image_path)" :alt="$folder->event->title" :title="$folder->event->title" fallback-type="gallery" img-class="h-full w-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-500" />
                                            @endif
                                        @else
                                            <div class="h-full w-full bg-slate-200 flex items-center justify-center text-muted">{{ __('site.gallery.no_cover') }}</div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                        <div class="absolute top-4 left-4">
                                            <span class="flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1 text-xs font-semibold text-foreground shadow-sm">
                                                <x-icon name="folder" class="h-[13px] w-[13px] text-primary" /> {{ __('site.gallery.event_folder') }}
                                            </span>
                                        </div>
                                        <div class="absolute bottom-4 right-4">
                                            <span class="text-xs font-semibold text-white bg-primary px-2.5 py-1 rounded-md shadow-sm">{{ $folder->media->count() }} {{ __('site.gallery.items') }}</span>
                                        </div>
                                    </div>

                                    <div class="p-5 flex-1 flex flex-col justify-between">
                                        <div>
                                            <h3 class="text-lg font-bold text-foreground group-hover:text-primary transition-colors line-clamp-1">{{ $folder->event->title }}</h3>
                                            <p class="text-xs text-muted flex items-center gap-1.5 mt-1.5 font-medium">
                                                <x-icon name="calendar" class="h-3 w-3 text-primary" /> {{ $folder->event->date->format('M j, Y') }}
                                            </p>
                                        </div>
                                        <span class="text-xs font-bold text-primary group-hover:underline inline-flex items-center gap-1 mt-3">
                                            {{ __('site.gallery.view_all_photos') }} <x-icon name="eye" class="h-3 w-3" />
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 2. Common Gallery --}}
        <div class="space-y-3 pt-4 border-t border-border">
            <div class="flex items-center gap-3">
                <div class="h-8 w-1 rounded-full bg-accent"></div>
                <h2 class="text-2xl font-bold text-foreground">{{ __('site.gallery.common_gallery') }}</h2>
            </div>
            <p class="text-sm text-muted">{{ __('site.gallery.common_gallery_desc') }}</p>

            @if ($common->isEmpty())
                <p class="text-sm text-muted italic">{{ __('site.gallery.no_common') }}</p>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach ($common as $index => $item)
                        @php
                            $isVideo = is_video_file($item->image_path);
                            $itemSrc = media_url($item->image_path);
                            $caption = $item->caption ?: ($isVideo ? null : 'Gallery image');
                        @endphp
                        <div
                            x-on:click="openLightbox({{ $index }})"
                            class="group relative aspect-square overflow-hidden rounded-2xl border cursor-pointer {{ $isVideo ? 'border-accent/40 shadow-[0_0_15px_rgba(244,63,94,0.08)] bg-slate-950' : 'border-border bg-white shadow-sm' }}"
                        >
                            @if ($isVideo)
                                <div class="relative w-full h-full flex items-center justify-center bg-black">
                                    <video src="{{ $itemSrc }}" class="w-full h-full object-cover opacity-80" muted preload="metadata"></video>
                                    <div class="absolute inset-0 bg-black/30 group-hover:bg-black/50 transition-colors"></div>
                                    <div class="absolute h-10 w-10 rounded-full bg-accent text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                        <x-icon name="play" class="h-[18px] w-[18px] ml-0.5" />
                                    </div>
                                    <span class="absolute bottom-2.5 left-2.5 flex items-center gap-1 text-[12px] font-bold text-white bg-accent px-2 py-0.5 rounded-full z-10 uppercase tracking-wider">
                                        <x-icon name="film" class="h-[9px] w-[9px]" /> {{ __('site.gallery.video') }}
                                    </span>
                                    @if ($item->caption)
                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/35 to-transparent p-3 opacity-0 transition-opacity duration-300 group-hover:opacity-100 flex flex-col justify-end pt-8">
                                            <h3 class="text-[12px] font-semibold text-white line-clamp-2">{{ $item->caption }}</h3>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="relative w-full h-full">
                                    <x-safe-image :src="$itemSrc" img-class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" :alt="$caption" :title="$caption" fallback-type="gallery" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent p-3.5 opacity-0 transition-opacity duration-300 group-hover:opacity-100 flex flex-col justify-end">
                                        <span class="mb-1.5 w-fit rounded-full bg-white/15 px-2 py-0.5 text-[12px] font-bold text-white backdrop-blur-sm uppercase">{{ __('site.gallery.image') }}</span>
                                        @if ($item->caption)
                                            <h3 class="text-xs font-semibold text-white line-clamp-2">{{ $item->caption }}</h3>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <x-pagination :paginator="$common" item-label="items" />
            @endif
        </div>
    </section>

    {{-- Single Media Lightbox Modal with Next / Prev Navigation (Teleported to <body> for full screen) --}}
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
                    <h4 class="text-base sm:text-lg font-bold">{{ __('site.gallery.community_gallery') }}</h4>
                    <p class="text-xs text-white/60">
                        <span x-text="lightboxIndex !== null ? (lightboxIndex + 1) + ' of ' + items.length : ''"></span>
                    </p>
                </div>
                <button
                    type="button"
                    x-on:click="closeLightbox"
                    class="h-9 w-9 rounded-full bg-red-600 hover:bg-red-700 flex items-center justify-center text-white shadow-lg border border-white/20 active:scale-95 transition-all cursor-pointer"
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
                    class="shrink-0 h-12 w-12 rounded-full bg-slate-900/90 hover:bg-[#00379D] disabled:opacity-20 disabled:pointer-events-none flex items-center justify-center text-white shadow-2xl border-2 border-white/40 hover:border-white transition-all cursor-pointer active:scale-95"
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
                    class="shrink-0 h-12 w-12 rounded-full bg-slate-900/90 hover:bg-[#00379D] disabled:opacity-20 disabled:pointer-events-none flex items-center justify-center text-white shadow-2xl border-2 border-white/40 hover:border-white transition-all cursor-pointer active:scale-95"
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
</div>
