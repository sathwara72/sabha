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
    $photosJson = $photos->map(fn ($p, $idx) => [
        'src' => media_url($p->image_path),
        'caption' => $p->caption,
        'index' => $idx,
    ]);
    $heroImage = media_url($event->image) ?: 'https://images.unsplash.com/photo-1540575861501-7ad0582373f3?q=80&w=1400&auto=format&fit=crop';
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
                    <x-icon name="grid-3x3" class="h-3 w-3" /> {{ $photos->count() }} {{ __('site.gallery.photos') }}
                </span>
            </div>
        </div>

        {{-- Hero Banner --}}
        <div class="relative h-52 sm:h-64 overflow-hidden">
            <img src="{{ $heroImage }}" alt="{{ $event->title }}" class="h-full w-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 px-6 pb-6">
                <div class="mx-auto max-w-7xl">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/90 px-3 py-1 text-[12px] font-bold text-white mb-2">
                        📁 {{ __('site.gallery.event_folder') }}
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight">{{ $event->title }}</h1>
                    <div class="flex flex-wrap items-center gap-4 mt-2 text-xs text-white/70 font-medium">
                        <span class="flex items-center gap-1.5"><x-icon name="calendar" class="h-[13px] w-[13px]" /> {{ $event->date->format('F j, Y') }}</span>
                        @if ($event->location)
                            <span class="flex items-center gap-1.5"><x-icon name="map-pin" class="h-[13px] w-[13px]" /> {{ $event->location }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Count Bar --}}
        <div class="border-b border-border bg-surface">
            <div class="mx-auto max-w-7xl px-6 py-3 flex items-center justify-between">
                <p class="text-sm font-semibold text-muted">
                    {{ __('site.gallery.showing') }} <span class="text-foreground font-bold">{{ $photos->count() }}</span> {{ __('site.gallery.photos') }}
                </p>
            </div>
        </div>

        {{-- Photo Grid --}}
        <section class="mx-auto max-w-7xl px-6 py-10">
            @if ($photos->isEmpty())
                <div class="flex flex-col items-center justify-center py-24 gap-4 text-center">
                    <x-icon name="image" class="h-10 w-10 text-muted-foreground/30" />
                    <p class="text-sm text-muted font-medium">{{ __('site.gallery.no_photos_folder') }}</p>
                </div>
            @else
                <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                    @foreach ($photos as $idx => $photo)
                        <div
                            x-on:click="openLightbox({{ $idx }})"
                            class="group relative break-inside-avoid overflow-hidden rounded-2xl border border-border bg-white shadow-sm cursor-pointer"
                        >
                            <img
                                src="{{ media_url($photo->image_path) }}"
                                alt="{{ $photo->caption ?: 'Photo ' . ($idx + 1) }}"
                                class="w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-between p-4">
                                <div class="flex justify-end">
                                    <button
                                        x-on:click.stop="download({{ Illuminate\Support\Js::from(media_url($photo->image_path)) }}, {{ $idx }})"
                                        class="h-8 w-8 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-sm flex items-center justify-center text-white transition-colors"
                                        title="Download photo"
                                    >
                                        <x-icon name="download" class="h-[14px] w-[14px]" />
                                    </button>
                                </div>
                                <div>
                                    <span class="inline-flex items-center gap-1 mb-1.5 rounded-full bg-white/20 backdrop-blur-sm px-2.5 py-0.5 text-[12px] font-bold text-white uppercase">
                                        <x-icon name="zoom-in" class="h-[10px] w-[10px]" /> {{ __('site.gallery.click_to_zoom') }}
                                    </span>
                                    @if ($photo->caption)
                                        <p class="text-xs font-semibold text-white line-clamp-2">{{ $photo->caption }}</p>
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
        <div x-show="lightboxIndex !== null" x-cloak x-on:click="closeLightbox" class="fixed inset-0 z-50 bg-black/97 backdrop-blur-md flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/10 shrink-0" x-on:click.stop="">
                <div>
                    <h4 class="text-base font-bold text-white">{{ $event->title }}</h4>
                    <p class="text-xs text-white/50 mt-0.5">
                        <span x-text="lightboxIndex !== null ? lightboxIndex + 1 : ''"></span> {{ __('site.gallery.of') }} {{ $photos->count() }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button x-on:click="download(photos[lightboxIndex]?.src, lightboxIndex)" class="inline-flex items-center gap-1.5 rounded-xl bg-white/10 hover:bg-white/20 px-3 py-2 text-xs font-semibold text-white transition-colors">
                        <x-icon name="download" class="h-[14px] w-[14px]" /> {{ __('site.gallery.download') }}
                    </button>
                    <button x-on:click="closeLightbox" class="h-9 w-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                        <x-icon name="x" class="h-[18px] w-[18px]" />
                    </button>
                </div>
            </div>

            <div class="flex-1 flex items-center justify-between gap-4 px-4 py-4 min-h-0" x-on:click.stop="">
                <button x-on:click="prev" :disabled="lightboxIndex === 0" class="shrink-0 h-12 w-12 rounded-full bg-white/10 hover:bg-white/20 disabled:opacity-20 flex items-center justify-center text-white transition-colors">
                    <x-icon name="chevron-left" class="h-6 w-6" />
                </button>

                <div class="flex-1 flex items-center justify-center min-h-0 max-h-[72vh]">
                    <img :src="lightboxIndex !== null ? photos[lightboxIndex]?.src : ''" :alt="lightboxIndex !== null ? photos[lightboxIndex]?.caption : ''" class="max-w-full max-h-[72vh] object-contain rounded-xl shadow-2xl" x-on:click.stop="" />
                </div>

                <button x-on:click="next" :disabled="lightboxIndex === photos.length - 1" class="shrink-0 h-12 w-12 rounded-full bg-white/10 hover:bg-white/20 disabled:opacity-20 flex items-center justify-center text-white transition-colors">
                    <x-icon name="chevron-right" class="h-6 w-6" />
                </button>
            </div>

            <div class="shrink-0 border-t border-white/10 px-6 py-4" x-on:click.stop="">
                <p x-show="lightboxIndex !== null && photos[lightboxIndex]?.caption" x-cloak class="text-center text-sm font-medium text-white/80 mb-3" x-text="lightboxIndex !== null ? photos[lightboxIndex]?.caption : ''"></p>
                <div class="flex items-center justify-center gap-2 overflow-x-auto pb-1 max-w-3xl mx-auto">
                    <template x-for="(p, i) in photos" :key="i">
                        <button
                            x-on:click="lightboxIndex = i"
                            :class="i === lightboxIndex ? 'border-primary scale-110 shadow-lg shadow-primary/30' : 'border-white/20 opacity-50 hover:opacity-80'"
                            class="shrink-0 h-12 w-16 rounded-lg overflow-hidden border-2 transition-all"
                        >
                            <img :src="p.src" alt="" class="h-full w-full object-cover" />
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
@endunless
