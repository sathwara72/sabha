@unless ($event)
    <x-layouts.app :title="__('site.eventDetail.not_found_title') . ' | Sabha'" :noindex="true">
        <div class="min-h-screen flex flex-col items-center justify-center bg-background font-outfit text-center p-6">
            <h2 class="text-2xl font-bold text-foreground">{{ __('site.eventDetail.not_found_title') }}</h2>
            <p class="mt-2 text-sm text-muted">{{ __('site.eventDetail.not_found_desc') }}</p>
            <a href="/events" class="mt-6 inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98]">
                {{ __('site.eventDetail.back_to_events') }}
            </a>
        </div>
    </x-layouts.app>
@else
@php
    $eventImage = media_url($event->image);
    $galleryImages = $event->galleryImages;
    $attendeesCount = $event->approvedRegistrations->count();
    $galleryJson = $galleryImages->map(fn ($g) => [
        'src' => media_url($g->image_path),
        'caption' => $g->caption,
        'isVideo' => is_video_file($g->image_path),
    ]);
    $metaDescription = \Illuminate\Support\Str::limit(strip_tags((string) $event->description), 160);
@endphp

<x-layouts.app
    :title="$event->title . ' | Sabha'"
    :description="$metaDescription ?: __('site.events.subtitle')"
    :image="$eventImage"
>
    <div
        class="bg-background font-outfit min-h-screen pb-2"
        x-data="{
            imgError: false,
            lightboxIndex: null,
            gallery: {{ Illuminate\Support\Js::from($galleryJson) }},
            isReserving: false,
            reservingLoading: false,
            reserveSuccess: false,
            reserveError: '',
            paymentFile: null,
            paymentPreview: '',
            openLightbox(i) { this.lightboxIndex = i },
            closeLightbox() { this.lightboxIndex = null },
            nextMedia() { if (this.lightboxIndex !== null) this.lightboxIndex = (this.lightboxIndex + 1) % this.gallery.length },
            prevMedia() { if (this.lightboxIndex !== null) this.lightboxIndex = (this.lightboxIndex - 1 + this.gallery.length) % this.gallery.length },
            onFileChange(e) {
                const f = e.target.files[0] || null;
                this.paymentFile = f;
                this.paymentPreview = f ? URL.createObjectURL(f) : '';
            },
            async submitReservation() {
                @if (!auth()->check())
                    isReserving = false;
                    $store.auth.openLogin();
                    return;
                @endif
                if (!this.paymentFile) {
                    this.reserveError = '{{ __('site.eventDetail.payment_required') }}';
                    return;
                }
                this.reservingLoading = true;
                this.reserveError = '';
                try {
                    const data = new FormData();
                    data.append('payment_screenshot', this.paymentFile);
                    data.append('ticket_type', {{ $isVerifiedMember ? "'verified'" : "'standard'" }});
                    data.append('amount_paid', {{ Illuminate\Support\Js::from(strtolower(($isVerifiedMember ? $priceVerified : $priceNormal)) === 'free' ? '0' : preg_replace('/[^0-9]/', '', $isVerifiedMember ? $priceVerified : $priceNormal)) }});
                    const res = await fetch('/events/{{ $event->id }}/reserve', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: data,
                    });
                    const json = await res.json();
                    if (!res.ok) throw new Error(json.message || '{{ __('site.eventDetail.reserve_failed') }}');
                    this.reserveSuccess = true;
                    this.paymentFile = null;
                    this.paymentPreview = '';
                } catch (e) {
                    this.reserveError = e.message;
                } finally {
                    this.reservingLoading = false;
                }
            },
        }"
    >
        {{-- Hero Section with Background Image --}}
        <section class="relative overflow-hidden bg-slate-950 py-6 lg:py-6 text-white min-h-[260px] flex items-center border-b border-border">
            @if ($eventImage)
                <div class="absolute inset-0 z-0" x-show="!imgError">
                    <img src="{{ $eventImage }}" alt="" x-on:error="imgError = true" class="h-full w-full object-cover opacity-35" />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-900/30"></div>
                </div>
            @endif

            <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8 w-full">
                <a href="/events" class="group mb-4 inline-flex items-center gap-1.5 text-xs font-semibold text-white/80 transition-colors hover:text-white cursor-pointer bg-white/10 backdrop-blur-sm px-3 py-1 rounded-xl border border-white/10">
                    <x-icon name="arrow-left" class="h-3.5 w-3.5 transition-transform group-hover:-translate-x-0.5" />
                    {{ __('site.eventDetail.back_all_events') }}
                </a>

                <div class="max-w-3xl space-y-3.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-white px-3 py-0.5 text-xs font-semibold text-primary shadow-sm">{{ $event->type }}</span>
                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-900 bg-white px-2.5 py-0.5 rounded-full shadow-sm font-medium">
                            <x-icon name="users" class="h-3.5 w-3.5 text-primary animate-pulse" /> {{ $attendeesCount }} {{ __('site.eventDetail.registered') }}
                        </span>
                    </div>

                    <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl lg:text-4xl drop-shadow-md">{{ $event->title }}</h1>

                    <div class="flex flex-wrap gap-x-3 gap-y-1.5 text-xs text-white/90 pt-1">
                        <span class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1 rounded-xl border border-white/10"><x-icon name="calendar" class="h-3.5 w-3.5 text-white" /> {{ $event->date->format('M j, Y') }}</span>
                        <span class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1 rounded-xl border border-white/10"><x-icon name="clock" class="h-3.5 w-3.5 text-white" /> {{ $event->date->format('g:i A') }}</span>
                        <span class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1 rounded-xl border border-white/10"><x-icon name="map-pin" class="h-3.5 w-3.5 text-white" /> {{ $event->location }}</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Split Body Layout --}}
        <div class="mx-auto max-w-7xl px-6 py-6 lg:px-4">
            <div class="grid grid-cols-1 gap-2 lg:grid-cols-3">
                {{-- Left Column --}}
                <div class="space-y-3 lg:col-span-2">
                    @if ($event->description)
                        <section class="glass-card p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.eventDetail.about_event') }}</h2>
                            </div>
                            <p class="text-xs sm:text-sm leading-relaxed text-muted font-medium">{{ $event->description }}</p>
                        </section>
                    @endif

                    @if (!empty($event->agenda))
                        <section class="glass-card p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.eventDetail.agenda') }}</h2>
                            </div>
                            <div class="space-y-1.5">
                                @foreach ($event->agenda as $i => $item)
                                    <div class="flex items-center gap-2.5 py-1.5 border-b border-dashed border-slate-100 last:border-0">
                                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[10px] font-semibold text-primary">{{ $i + 1 }}</span>
                                        <span class="text-xs font-medium text-foreground">{{ $item }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if (!empty($event->speakers))
                        <section class="glass-card p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.eventDetail.speakers') }}</h2>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach ($event->speakers as $speaker)
                                    <div class="flex items-start gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-sm font-bold text-primary">{{ mb_substr($speaker['name'] ?? '', 0, 1) }}</div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-xs font-semibold text-foreground leading-tight">{{ $speaker['name'] ?? '' }}</h4>
                                            <p class="text-[10px] font-medium text-primary leading-tight mt-0.5">{{ $speaker['role'] ?? '' }}</p>
                                            <p class="mt-1.5 text-xs text-muted leading-relaxed line-clamp-2">{{ $speaker['bio'] ?? '' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="glass-card p-4">
                        <div class="mb-3 flex items-center gap-2">
                            <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.eventDetail.members_attending') }}</h2>
                        </div>
                        @if ($members->isEmpty())
                            <p class="text-xs text-muted italic pl-1">No members have registered for this event yet.</p>
                        @else
                            <div class="grid grid-cols-2 gap-2 md:grid-cols-3">
                                @foreach ($members as $member)
                                    <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 border border-slate-100">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-[10px] font-bold text-muted border border-border">
                                            {{ collect(explode(' ', $member['name']))->map(fn ($n) => mb_substr($n, 0, 1))->implode('') }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate text-xs font-semibold text-foreground leading-tight">{{ $member['name'] }}</p>
                                            <p class="truncate text-[9px] text-muted leading-tight mt-0.5">{{ $member['role'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </section>

                    @if ($galleryImages->isNotEmpty())
                        <section class="glass-card p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.eventDetail.event_gallery') }}</h2>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($galleryImages as $idx => $img)
                                    <div x-on:click="openLightbox({{ $idx }})" class="overflow-hidden rounded-xl relative cursor-pointer group aspect-video hover:shadow-md transition-all duration-300 border border-border bg-slate-100">
                                        @if (is_video_file($img->image_path))
                                            <div class="w-full h-full relative bg-slate-900 flex items-center justify-center">
                                                <video src="{{ media_url($img->image_path) }}" class="w-full h-full object-cover" muted preload="metadata"></video>
                                                <div class="absolute inset-0 bg-black/30 flex items-center justify-center group-hover:bg-black/50 transition-colors">
                                                    <span class="p-2 bg-white/20 backdrop-blur rounded-full text-white">
                                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <img src="{{ media_url($img->image_path) }}" alt="{{ $img->caption ?: 'Event photo ' . ($idx + 1) }}" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300" />
                                        @endif
                                        @if ($img->caption)
                                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 to-transparent p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <p class="text-[10px] text-white font-semibold truncate">{{ $img->caption }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                {{-- Right Column - Sticky Sidebar --}}
                <div class="space-y-4 lg:sticky lg:top-20 h-fit">
                    <div class="glass-card p-4">
                        <p class="text-[9px] font-bold uppercase tracking-wider text-muted-foreground mb-2">{{ __('site.eventDetail.ticket_prices') }}</p>
                        <div class="space-y-1.5 text-center">
                            <div class="py-1.5 px-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-muted-foreground">{{ __('site.eventDetail.standard') }}:</span>
                                <span class="text-sm font-bold text-foreground">{{ $priceNormal }}</span>
                            </div>

                            @if ($isVerifiedMember)
                                <div class="py-1.5 px-2.5 rounded-xl border flex items-center justify-between transition-colors bg-emerald-50 border-emerald-200 text-emerald-700">
                                    <span class="text-[11px] font-semibold text-emerald-600 flex items-center gap-0.5"><x-icon name="shield-check" class="h-[11px] w-[11px]" /> {{ __('site.eventDetail.verified') }}:</span>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-foreground">{{ $priceVerified }}</span>
                                    </div>
                                </div>
                            @else
                                <a href="/profile?tab=business" class="block py-1.5 px-2.5 rounded-xl border flex items-center justify-between transition-colors {{ $status === 'past' ? 'bg-slate-50 border-border text-muted-foreground pointer-events-none' : 'bg-white border-border hover:bg-emerald-50/20 hover:border-emerald-200 text-muted-foreground group cursor-pointer' }}">
                                    <span class="text-[11px] font-semibold text-emerald-600 flex items-center gap-0.5"><x-icon name="shield-check" class="h-[11px] w-[11px]" /> {{ __('site.eventDetail.verified') }}:</span>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-foreground">{{ $priceVerified }}</span>
                                        @if ($status !== 'past')
                                            <span class="block text-[7px] font-semibold text-emerald-600">{{ __('site.eventDetail.click_to_get') }} &rarr;</span>
                                        @endif
                                    </div>
                                </a>
                            @endif
                        </div>

                        <div class="mt-2.5 border-t border-border pt-2.5 w-full text-center space-y-2">
                            @if ($status === 'upcoming')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-100 uppercase tracking-wide w-full justify-center">{{ __('site.eventDetail.booking_soon') }}</span>
                            @elseif ($status === 'current')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-100 uppercase tracking-wide w-full justify-center">{{ __('site.eventDetail.booking_available') }}</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500 border border-slate-200 uppercase tracking-wide w-full justify-center">{{ __('site.eventDetail.booking_closed') }}</span>
                            @endif

                            @if ($status === 'current')
                                <button x-on:click="{{ auth()->check() ? 'isReserving = true' : '$store.auth.openLogin()' }}" class="group inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] w-full cursor-pointer">
                                    {{ __('site.eventDetail.reserve_seat') }}
                                    <x-icon name="arrow-up-right" class="h-[13px] w-[13px] transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                                </button>
                            @elseif ($status === 'upcoming')
                                <button disabled class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50/50 px-4 py-2 text-xs font-semibold text-amber-700 w-full opacity-80">
                                    {{ __('site.eventDetail.booking_soon') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="glass-card p-4">
                        <h3 class="border-b border-border pb-2 text-xs font-bold uppercase tracking-wider text-foreground">{{ __('site.eventDetail.details') }}</h3>
                        <div class="space-y-3 pt-3">
                            <div class="flex gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="map-pin" class="h-3.5 w-3.5" /></div>
                                <div>
                                    <p class="text-[9px] font-semibold text-muted">{{ __('site.eventDetail.location') }}</p>
                                    <p class="mt-0.5 text-xs font-medium text-foreground">{{ $event->location }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="zap" class="h-3.5 w-3.5" /></div>
                                <div>
                                    <p class="text-[9px] font-semibold text-muted">{{ __('site.eventDetail.format') }}</p>
                                    <p class="mt-0.5 text-xs font-medium text-foreground">{{ $event->type }} {{ __('site.eventDetail.interactive') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 space-y-2">
                            <div class="flex items-center gap-1.5 rounded-xl bg-primary-soft px-2.5 py-1.5 text-[11px] font-medium text-primary">
                                <x-icon name="check-circle-2" class="h-3 w-3" /> {{ __('site.eventDetail.limited_capacity') }}
                            </div>
                            <div class="flex items-center gap-1.5 px-1 text-[11px] text-muted">
                                <x-icon name="info" class="h-3 w-3" /> {{ __('site.eventDetail.register_before') }}
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-border bg-primary p-4 text-white">
                        <h4 class="text-xs font-bold uppercase tracking-wider">{{ __('site.eventDetail.good_to_know') }}</h4>
                        <p class="mt-1.5 text-xs leading-relaxed text-white/80">{{ __('site.eventDetail.good_to_know_desc') }}</p>
                        <div class="mt-3 inline-flex items-center gap-1.5 rounded-xl bg-white/15 px-2.5 py-1.5">
                            <x-icon name="star" class="h-3.5 w-3.5 text-white fill-current" />
                            <span class="text-[11px] font-semibold text-white">{{ __('site.eventDetail.great_experience') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lightbox Modal --}}
        <div x-show="lightboxIndex !== null" x-cloak x-on:click="closeLightbox" class="fixed inset-0 z-[200] flex items-center justify-center bg-black/90 backdrop-blur-sm">
            <button x-on:click.stop="closeLightbox" class="absolute top-4 right-4 z-20 p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors cursor-pointer" aria-label="Close lightbox">
                <x-icon name="x" class="h-6 w-6" />
            </button>

            <button x-show="gallery.length > 1" x-on:click.stop="prevMedia" class="absolute left-4 z-20 p-3 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors cursor-pointer" aria-label="Previous">
                <x-icon name="chevron-left" class="h-6 w-6" />
            </button>

            <div class="relative max-w-4xl max-h-[80vh] w-full mx-16 flex items-center justify-center" x-on:click.stop="">
                <template x-if="lightboxIndex !== null && gallery[lightboxIndex] && !gallery[lightboxIndex].isVideo">
                    <img :src="gallery[lightboxIndex]?.src" :alt="gallery[lightboxIndex]?.caption" class="max-h-[80vh] max-w-full rounded-xl shadow-2xl object-contain" />
                </template>
                <template x-if="lightboxIndex !== null && gallery[lightboxIndex] && gallery[lightboxIndex].isVideo">
                    <video :src="gallery[lightboxIndex]?.src" controls autoplay class="max-h-[80vh] max-w-full rounded-xl shadow-2xl object-contain"></video>
                </template>
                <div x-show="lightboxIndex !== null && gallery[lightboxIndex]?.caption" class="absolute bottom-0 left-0 right-0 px-4 py-3 bg-gradient-to-t from-black/70 to-transparent rounded-b-xl">
                    <p class="text-sm text-white font-medium text-center" x-text="gallery[lightboxIndex]?.caption"></p>
                </div>
            </div>

            <button x-show="gallery.length > 1" x-on:click.stop="nextMedia" class="absolute right-4 z-20 p-3 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors cursor-pointer" aria-label="Next">
                <x-icon name="chevron-right" class="h-6 w-6" />
            </button>

            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 text-white/70 text-xs font-semibold tracking-wider">
                <span x-text="lightboxIndex !== null ? lightboxIndex + 1 : ''"></span> / <span x-text="gallery.length"></span>
            </div>
        </div>

        {{-- Seat Reservation Modal --}}
        <div x-show="isReserving" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-on:click="isReserving = false" x-show="isReserving" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div x-show="isReserving" x-transition class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white border border-border p-5 shadow-2xl z-10">
                <div class="flex items-center justify-between border-b border-border pb-3 mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-foreground">{{ __('site.eventDetail.reserve_seat_title') }}</h3>
                        <p class="text-[10px] text-muted-foreground mt-0.5">{{ $event->title }}</p>
                    </div>
                    <button x-on:click="isReserving = false" class="rounded-lg p-1 text-muted-foreground hover:bg-slate-100 transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4 w-4" />
                    </button>
                </div>

                <div x-show="reserveSuccess" x-cloak class="text-center py-4 space-y-3">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-green-50 text-green-600 border border-green-200">
                        <x-icon name="check-circle-2" class="h-5 w-5" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-foreground">{{ __('site.eventDetail.reservation_requested') }}</h4>
                        <p class="text-[11px] text-muted-foreground mt-1 max-w-xs mx-auto">{{ __('site.eventDetail.reservation_requested_desc') }}</p>
                    </div>
                    <button x-on:click="isReserving = false; reserveSuccess = false" class="w-full inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-xs font-semibold text-white shadow-sm hover:opacity-90 active:scale-[0.98] cursor-pointer">
                        {{ __('site.eventDetail.close') }}
                    </button>
                </div>

                <form x-show="!reserveSuccess" x-on:submit.prevent="submitReservation" class="space-y-3">
                    <div x-show="reserveError" x-cloak class="rounded-xl bg-red-50 border border-red-100 p-2.5 text-center text-xs font-semibold text-red-600" x-text="reserveError"></div>

                    <div class="space-y-1.5 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <h4 class="text-[9px] font-bold uppercase tracking-wider text-muted-foreground mb-0.5">{{ __('site.eventDetail.your_personal_details') }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                            <div class="min-w-0">
                                <span class="text-[9px] text-muted-foreground font-semibold block">{{ __('site.eventDetail.full_name') }}</span>
                                <span class="font-bold text-foreground block truncate">{{ auth()->user()->name ?? '' }}</span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9px] text-muted-foreground font-semibold block">{{ __('site.eventDetail.email_address') }}</span>
                                <span class="font-bold text-foreground block truncate" title="{{ auth()->user()->email ?? '' }}">{{ auth()->user()->email ?? '' }}</span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9px] text-muted-foreground font-semibold block">{{ __('site.eventDetail.phone_number') }}</span>
                                <span class="font-bold text-foreground block truncate">{{ auth()->user()->phone ?? __('site.eventDetail.phone_not_provided') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-muted-foreground block">{{ __('site.eventDetail.select_ticket_type') }}</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="p-2.5 rounded-xl border flex flex-col justify-between transition-all {{ !$isVerifiedMember ? 'border-primary bg-primary-soft/30 text-foreground' : 'border-border bg-slate-50 text-muted-foreground' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold uppercase tracking-wider">{{ __('site.eventDetail.standard') }}</span>
                                    @if (!$isVerifiedMember)
                                        <div class="h-2.5 w-2.5 rounded-full border-2 border-primary bg-primary flex items-center justify-center"><div class="h-1 w-1 rounded-full bg-white"></div></div>
                                    @endif
                                </div>
                                <span class="text-sm font-extrabold text-foreground mt-0.5">{{ $priceNormal }}</span>
                            </div>

                            <div class="p-2.5 rounded-xl border flex flex-col justify-between transition-all {{ $isVerifiedMember ? 'border-primary bg-primary-soft/30 text-foreground' : 'border-border bg-white text-muted-foreground' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-600 flex items-center gap-0.5"><x-icon name="shield-check" class="h-[10px] w-[10px]" /> {{ __('site.eventDetail.verified') }}</span>
                                    @if ($isVerifiedMember)
                                        <div class="h-2.5 w-2.5 rounded-full border-2 border-primary bg-primary flex items-center justify-center"><div class="h-1 w-1 rounded-full bg-white"></div></div>
                                    @endif
                                </div>
                                <div class="mt-0.5 flex flex-col">
                                    <span class="text-sm font-extrabold text-foreground">{{ $priceVerified }}</span>
                                    @if (!$isVerifiedMember)
                                        <span class="text-[7px] font-semibold text-emerald-600 mt-0.5">{{ __('site.eventDetail.requires_business') }} &rarr;</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-muted-foreground block">{{ __('site.eventDetail.upload_payment') }}</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-22 border-2 border-dashed border-border rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-colors">
                                <template x-if="paymentPreview">
                                    <div class="relative h-full w-full p-1.5 flex items-center justify-center gap-3">
                                        <img :src="paymentPreview" alt="Payment SS" class="h-full object-contain rounded-lg border border-border" />
                                        <span class="text-xs text-muted-foreground font-semibold truncate max-w-[150px]" x-text="paymentFile?.name"></span>
                                    </div>
                                </template>
                                <template x-if="!paymentPreview">
                                    <div class="flex flex-col items-center justify-center py-3">
                                        <x-icon name="upload" class="w-6 h-6 text-muted-foreground mb-1" />
                                        <p class="text-[10px] text-muted-foreground font-semibold">{{ __('site.eventDetail.click_upload') }}</p>
                                    </div>
                                </template>
                                <input type="file" accept="image/*" x-on:change="onFileChange" class="hidden" />
                            </label>
                        </div>
                    </div>

                    <button type="submit" :disabled="reservingLoading" class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer">
                        <span x-text="reservingLoading ? '{{ __('site.eventDetail.requesting') }}' : '{{ __('site.eventDetail.submit_reservation') }}'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
@endunless
