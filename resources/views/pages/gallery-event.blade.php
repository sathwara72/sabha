@unless ($event)
    <x-layouts.app :title="__('site.gallery.folder_not_exist') . ' | Sabha'" :noindex="true">
        <div class="min-h-screen flex flex-col items-center justify-center bg-background gap-4 text-center px-6">
            <div class="text-6xl">📁</div>
            <h2 class="text-xl font-bold text-foreground">{{ __('site.eventDetail.not_found_title') }}</h2>
            <p class="text-sm text-muted">{{ __('site.gallery.folder_not_exist') }}</p>
            <a href="/gallery" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white">
                <x-icon name="arrow-left" class="h-4 w-4" /> {{ __('site.gallery.back_to_gallery') }}
            </a>
        </div>
    </x-layouts.app>
@else
@php
    $galleryList = collect();

    // 1. YouTube Videos
    foreach ($event->youtube_urls ?? [] as $yIdx => $yUrl) {
        $embed = youtube_embed_url($yUrl);
        $thumb = youtube_thumbnail_url($yUrl);
        if ($embed) {
            $galleryList->push([
                'type' => 'youtube',
                'src' => $thumb ?: media_url($event->image),
                'embedUrl' => $embed,
                'caption' => 'YouTube Video ' . ($yIdx + 1),
                'isVideo' => true,
            ]);
        }
    }

    // 2. Uploaded Photos & Videos
    foreach ($photos as $pIdx => $p) {
        $galleryList->push([
            'type' => is_video_file($p->image_path) ? 'video' : 'image',
            'src' => media_url($p->image_path),
            'embedUrl' => null,
            'caption' => $p->caption,
            'isVideo' => is_video_file($p->image_path),
        ]);
    }

    $photosJson = $galleryList->values();
    $heroImage = media_url($event->image);
@endphp

<x-layouts.app
    :title="$event->title . ' | Gallery | Sabha'"
    :description="__('site.gallery.subtitle')"
    :image="$heroImage"
>
    <div
        class="min-h-screen bg-background font-outfit"
        x-data="{
            photos: {{ Illuminate\Support\Js::from($photosJson) }},
            lightboxIndex: null,
            openLightbox(i) { this.lightboxIndex = i },
            closeLightbox() { this.lightboxIndex = null },
            prev() { if (this.lightboxIndex !== null) this.lightboxIndex = Math.max(this.lightboxIndex - 1, 0) },
            next() { if (this.lightboxIndex !== null) this.lightboxIndex = Math.min(this.lightboxIndex + 1, this.photos.length - 1) },
            async download(src, idx) {
                try {
                    const res = await fetch(src);
                    const blob = await res.blob();
                    const blobUrl = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = blobUrl;
                    a.download = 'sabha-event-photo-' + (idx + 1) + '.jpg';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(blobUrl);
                } catch (e) {
                    window.open(src, '_blank');
                }
            },
        }"
        x-on:keydown.window="
            if (lightboxIndex === null) return;
            if ($event.key === 'ArrowRight') next();
            if ($event.key === 'ArrowLeft') prev();
            if ($event.key === 'Escape') closeLightbox();
        "
    >
        {{-- Sticky Top Bar --}}
        <div class="sticky top-0 z-30 border-b border-border bg-white/90 backdrop-blur-md">
            <div class="mx-auto max-w-7xl px-6 py-3.5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="/gallery" class="inline-flex items-center gap-1.5 text-sm font-semibold text-muted hover:text-primary transition-colors shrink-0">
                        <x-icon name="arrow-left" class="h-4 w-4" /> {{ __('site.gallery.label') }}
                    </a>
                    <span class="text-muted-foreground">/</span>
                    <span class="text-sm font-bold text-foreground line-clamp-1">{{ $event->title }}</span>
                </div>
                <span class="shrink-0 inline-flex items-center gap-1.5 text-xs font-semibold text-primary bg-primary-soft px-3 py-1 rounded-full border border-primary/10">
                    <x-icon name="grid-3x3" class="h-3 w-3" /> {{ $galleryList->count() }} Media
                </span>
            </div>
        </div>

        {{-- Hero Banner: Uncropped with Ambient Backdrop + Blue Fallback --}}
        <div class="relative min-h-[260px] sm:min-h-[320px] lg:min-h-[360px] overflow-hidden bg-slate-950 flex items-center justify-center">
            @if ($heroImage)
                {{-- Ambient blurred backdrop --}}
                <img
                    src="{{ $heroImage }}"
                    alt=""
                    aria-hidden="true"
                    class="absolute inset-0 h-full w-full object-cover blur-2xl scale-125 opacity-60 filter pointer-events-none"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/50 to-slate-950/30 pointer-events-none"></div>

                {{-- Full uncropped foreground image --}}
                <img
                    src="{{ $heroImage }}"
                    alt="{{ $event->title }}"
                    class="relative z-10 max-h-[360px] w-full object-contain drop-shadow-2xl pointer-events-none py-4"
                />
            @else
                {{-- Branded Blue Gradient Fallback --}}
                <div class="absolute inset-0 bg-gradient-to-br from-[#00379D] via-[#0F3459] to-[#091E36] flex items-center justify-center">
                    <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 16px 16px;"></div>
                    <div class="absolute -top-12 -right-12 w-64 h-64 bg-[#1d4ed8]/30 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-[#00379D]/40 rounded-full blur-3xl pointer-events-none"></div>
                </div>
            @endif

            <div class="absolute bottom-0 left-0 right-0 px-6 pb-6 z-20 pointer-events-none">
                <div class="mx-auto max-w-7xl">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/95 backdrop-blur-sm border border-white/20 px-3 py-1 text-xs font-bold text-white mb-2.5 shadow-md">
                        📁 {{ __('site.gallery.event_folder') }}
                    </span>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight drop-shadow-lg">{{ $event->title }}</h1>
                    <div class="flex flex-wrap items-center gap-3 mt-2 text-xs sm:text-sm text-slate-200 font-semibold drop-shadow">
                        <span class="inline-flex items-center gap-1.5 bg-black/40 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/10">
                            <x-icon name="calendar" class="h-3.5 w-3.5 text-sky-400" />
                            {{ $event->date->format('F j, Y') }}
                        </span>
                        @if ($event->location)
                            <span class="inline-flex items-center gap-1.5 bg-black/40 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/10">
                                <x-icon name="map-pin" class="h-3.5 w-3.5 text-sky-400" />
                                {{ $event->location }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Count Bar --}}
        <div class="border-b border-border bg-surface">
            <div class="mx-auto max-w-7xl px-6 py-3 flex items-center justify-between">
                <p class="text-sm font-semibold text-muted">
                    {{ __('site.gallery.showing') }} <span class="text-foreground font-bold">{{ $galleryList->count() }}</span> Media Items
                </p>
            </div>
        </div>

        {{-- Photo Grid --}}
        <section class="mx-auto max-w-7xl px-6 py-10">
            @if ($galleryList->isEmpty())
                <div class="flex flex-col items-center justify-center py-24 gap-4 text-center">
                    <x-icon name="image" class="h-10 w-10 text-muted-foreground/30" />
                    <p class="text-sm text-muted font-medium">{{ __('site.gallery.no_photos_folder') }}</p>
                </div>
            @else
                <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                    @foreach ($galleryList as $idx => $photo)
                        <div
                            x-on:click="openLightbox({{ $idx }})"
                            class="group relative break-inside-avoid overflow-hidden rounded-2xl border border-border bg-slate-900 shadow-sm cursor-pointer"
                        >
                            @if ($photo['type'] === 'youtube')
                                <img
                                    src="{{ $photo['src'] }}"
                                    alt="{{ $photo['caption'] }}"
                                    class="w-full object-cover transition-transform duration-500 group-hover:scale-105 opacity-90 group-hover:opacity-100"
                                    loading="lazy"
                                />
                                <div class="absolute inset-0 bg-black/25 group-hover:bg-black/40 transition-colors flex items-center justify-center">
                                    <div class="flex items-center justify-center h-11 w-11 rounded-full bg-red-600 text-white shadow-lg transition-transform group-hover:scale-110">
                                        <svg class="w-6 h-6 fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                    </div>
                                </div>
                                <div class="absolute top-3 right-3 inline-flex items-center gap-1 rounded-md bg-black/70 backdrop-blur-xs px-2 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">
                                    <x-icon name="video" class="h-3 w-3 text-red-400" />
                                    <span>YouTube</span>
                                </div>
                            @elseif ($photo['type'] === 'video')
                                <video src="{{ $photo['src'] }}" class="w-full object-cover" muted preload="metadata"></video>
                                <div class="absolute inset-0 bg-black/30 flex items-center justify-center group-hover:bg-black/50 transition-colors">
                                    <span class="p-2.5 bg-white/20 backdrop-blur rounded-full text-white">
                                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                    </span>
                                </div>
                            @else
                                <div class="relative w-full h-full min-h-[220px] flex items-center justify-center overflow-hidden">
                                    <x-safe-image
                                        :src="$photo['src']"
                                        :alt="$photo['caption'] ?: 'Photo ' . ($idx + 1)"
                                        :title="$photo['caption']"
                                        :blur-backdrop="true"
                                        fallback-type="gallery"
                                        img-class="h-full w-full object-contain"
                                    />
                                </div>
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-between p-4 pointer-events-none">
                                <div class="flex justify-end pointer-events-auto">
                                    @if ($photo['type'] !== 'youtube')
                                        <button
                                            x-on:click.stop="download({{ Illuminate\Support\Js::from($photo['src']) }}, {{ $idx }})"
                                            class="h-8 w-8 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-sm flex items-center justify-center text-white transition-colors cursor-pointer"
                                            title="Download photo"
                                        >
                                            <x-icon name="download" class="h-[14px] w-[14px]" />
                                        </button>
                                    @endif
                                </div>
                                <div>
                                    <span class="inline-flex items-center gap-1 mb-1.5 rounded-full bg-white/20 backdrop-blur-sm px-2.5 py-0.5 text-[12px] font-bold text-white uppercase">
                                        <x-icon name="zoom-in" class="h-[10px] w-[10px]" /> {{ $photo['type'] === 'youtube' ? 'Click to Play' : __('site.gallery.click_to_zoom') }}
                                    </span>
                                    @if (!empty($photo['caption']))
                                        <p class="text-xs font-semibold text-white line-clamp-2">{{ $photo['caption'] }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="absolute top-3 left-3 h-6 w-6 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center text-[12px] font-bold text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                {{ $idx + 1 }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Lightbox --}}
        <div x-show="lightboxIndex !== null" x-cloak x-on:click="closeLightbox" class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-2xl flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 shrink-0" x-on:click.stop="">
                <div>
                    <h4 class="text-base font-bold text-white">{{ $event->title }}</h4>
                    <p class="text-xs text-slate-300 font-semibold mt-0.5">
                        <span x-text="lightboxIndex !== null ? lightboxIndex + 1 : ''"></span> {{ __('site.gallery.of') }} <span x-text="photos.length"></span>
                    </p>
                </div>
                <div class="flex items-center gap-2.5">
                    <template x-if="photos[lightboxIndex]?.type !== 'youtube'">
                        <button
                            type="button"
                            x-on:click="download(photos[lightboxIndex]?.src, lightboxIndex)"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-[#00379D] hover:bg-[#002b80] px-3.5 py-2 text-xs font-bold text-white shadow-lg border border-white/20 active:scale-95 transition-all cursor-pointer"
                            title="Download"
                        >
                            <x-icon name="download" class="h-4 w-4" />
                            <span>{{ __('site.gallery.download') }}</span>
                        </button>
                    </template>
                    <button
                        type="button"
                        x-on:click="closeLightbox"
                        class="h-9 w-9 rounded-full bg-red-600 hover:bg-red-700 flex items-center justify-center text-white shadow-lg border border-white/20 active:scale-95 transition-all cursor-pointer"
                        title="Close preview"
                    >
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <div class="flex-1 flex items-center justify-between gap-4 px-4 py-4 min-h-0" x-on:click.stop="">
                <button
                    type="button"
                    x-on:click="prev"
                    :disabled="lightboxIndex === 0"
                    class="shrink-0 h-12 w-12 rounded-full bg-slate-900/90 hover:bg-[#00379D] disabled:opacity-20 disabled:pointer-events-none flex items-center justify-center text-white shadow-2xl border-2 border-white/40 hover:border-white transition-all cursor-pointer active:scale-95"
                    title="Previous"
                >
                    <x-icon name="chevron-left" class="h-6 w-6" />
                </button>

                <div class="flex-1 flex items-center justify-center min-h-0 max-h-[72vh]">
                    <template x-if="lightboxIndex !== null && photos[lightboxIndex]?.type === 'youtube'">
                        <div class="aspect-video w-full max-w-4xl max-h-[72vh] rounded-2xl overflow-hidden shadow-2xl border border-white/20 bg-black">
                            <iframe :src="photos[lightboxIndex]?.embedUrl + '?autoplay=1'" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                    </template>
                    <template x-if="lightboxIndex !== null && photos[lightboxIndex]?.type === 'image'">
                        <img :src="photos[lightboxIndex]?.src" :alt="photos[lightboxIndex]?.caption" class="max-w-full max-h-[72vh] object-contain rounded-xl shadow-2xl" />
                    </template>
                    <template x-if="lightboxIndex !== null && photos[lightboxIndex]?.type === 'video'">
                        <video :src="photos[lightboxIndex]?.src" controls autoplay class="max-w-full max-h-[72vh] object-contain rounded-xl shadow-2xl"></video>
                    </template>
                </div>

                <button
                    type="button"
                    x-on:click="next"
                    :disabled="lightboxIndex === photos.length - 1"
                    class="shrink-0 h-12 w-12 rounded-full bg-slate-900/90 hover:bg-[#00379D] disabled:opacity-20 disabled:pointer-events-none flex items-center justify-center text-white shadow-2xl border-2 border-white/40 hover:border-white transition-all cursor-pointer active:scale-95"
                    title="Next"
                >
                    <x-icon name="chevron-right" class="h-6 w-6" />
                </button>
            </div>

            <div class="shrink-0 px-6 py-4" x-on:click.stop="">
                <p x-show="lightboxIndex !== null && photos[lightboxIndex]?.caption" x-cloak class="text-center text-sm font-medium text-white/80 mb-3" x-text="lightboxIndex !== null ? photos[lightboxIndex]?.caption : ''"></p>
                <div class="flex items-center justify-center gap-2 overflow-x-auto pb-1 max-w-3xl mx-auto">
                    <template x-for="(p, i) in photos" :key="i">
                        <button
                            x-on:click="lightboxIndex = i"
                            :class="i === lightboxIndex ? 'border-primary scale-110 shadow-lg shadow-primary/30' : 'border-white/20 opacity-50 hover:opacity-80'"
                            class="shrink-0 h-12 w-16 rounded-lg overflow-hidden border-2 transition-all relative bg-black cursor-pointer"
                        >
                            <img :src="p.src" alt="" class="h-full w-full object-cover" />
                            <template x-if="p.type === 'youtube'">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                                    <div class="h-4 w-4 rounded-full bg-red-600 flex items-center justify-center text-white">
                                        <svg class="w-2.5 h-2.5 fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                    </div>
                                </div>
                            </template>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
@endunless
