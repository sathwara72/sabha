<div
    class="bg-background font-outfit min-h-screen"
    x-data="{ singleMedia: null }"
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
                    @foreach ($common as $item)
                        @php
                            $isVideo = is_video_file($item->image_path);
                            $itemSrc = media_url($item->image_path);
                            $caption = $item->caption ?: ($isVideo ? null : 'Gallery image');
                        @endphp
                        <div
                            x-on:click="singleMedia = { src: {{ Illuminate\Support\Js::from($itemSrc) }}, caption: {{ Illuminate\Support\Js::from($item->caption) }}, isVideo: {{ $isVideo ? 'true' : 'false' }} }"
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

    {{-- Single Media Lightbox Modal --}}
    <div x-show="singleMedia" x-cloak class="fixed inset-0 z-50 bg-black/95 backdrop-blur-md flex flex-col justify-between p-6">
        <div class="flex items-center justify-between text-white border-b border-white/10 pb-4">
            <div>
                <h4 class="text-lg font-bold">{{ __('site.gallery.community_gallery') }}</h4>
                <p class="text-xs text-white/60">{{ __('site.gallery.common_upload') }}</p>
            </div>
            <button x-on:click="singleMedia = null" class="h-10 w-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white cursor-pointer transition-colors">
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>

        <div class="flex-1 flex items-center justify-center py-6">
            <div class="max-w-4xl w-full h-full max-h-[70vh] rounded-2xl overflow-hidden shadow-2xl flex items-center justify-center">
                <template x-if="singleMedia && !singleMedia.isVideo">
                    <img :src="singleMedia?.src" :alt="singleMedia?.caption" class="w-full h-full object-contain rounded-xl" />
                </template>
                <template x-if="singleMedia && singleMedia.isVideo">
                    <video :src="singleMedia?.src" controls autoplay class="w-full h-full max-h-[70vh] object-contain rounded-xl"></video>
                </template>
            </div>
        </div>

        <div class="text-center text-white py-4 max-w-3xl mx-auto border-t border-white/10 w-full">
            <p class="text-sm font-medium" x-text="singleMedia?.caption || '{{ __('site.gallery.no_description') }}'"></p>
        </div>
    </div>
</div>
