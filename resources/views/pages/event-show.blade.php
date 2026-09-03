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
    $attendeesCount = $event->approvedRegistrations->count();

    $galleryItems = collect();

    // 1. YouTube Videos
    foreach ($event->youtube_urls ?? [] as $yIdx => $yUrl) {
        $embed = youtube_embed_url($yUrl);
        $thumb = youtube_thumbnail_url($yUrl);
        if ($embed) {
            $galleryItems->push([
                'type' => 'youtube',
                'src' => $thumb ?: $eventImage,
                'embedUrl' => $embed,
                'caption' => 'YouTube Video ' . ($yIdx + 1),
                'isVideo' => true,
            ]);
        }
    }

    // 2. Uploaded Photos & Videos
    foreach ($event->galleryImages as $gIdx => $g) {
        $galleryItems->push([
            'type' => is_video_file($g->image_path) ? 'video' : 'image',
            'src' => media_url($g->image_path),
            'embedUrl' => null,
            'caption' => $g->caption,
            'isVideo' => is_video_file($g->image_path),
        ]);
    }

    $galleryJson = $galleryItems->values();
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
                    window.location.href = '/login';
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
        {{-- Cover Banner with Profile Bar (Matches Business Details Style with Full Uncropped Image) --}}
        <div class="mx-auto max-w-7xl sm:px-6 sm:pt-6">
            <section class="relative w-full min-h-[360px] sm:min-h-[420px] lg:min-h-[460px] sm:rounded-3xl overflow-hidden bg-slate-950 flex items-center justify-center shadow-lg border border-border/40">
                @if ($eventImage)
                    {{-- Ambient blurred backdrop --}}
                    <img
                        src="{{ $eventImage }}"
                        alt=""
                        aria-hidden="true"
                        class="absolute inset-0 h-full w-full object-cover blur-2xl scale-125 opacity-40 filter pointer-events-none"
                    />
                    {{-- Full uncropped image centered --}}
                    <div class="absolute inset-0 flex items-center justify-center p-6 sm:p-10 pointer-events-none">
                        <img
                            src="{{ $eventImage }}"
                            alt="{{ $event->title }}"
                            class="max-h-[260px] sm:max-h-[320px] lg:max-h-[350px] w-auto max-w-full object-contain rounded-2xl shadow-2xl drop-shadow-2xl"
                        />
                    </div>
                @endif

                {{-- Back to Events Button --}}
                <div class="absolute top-6 left-6 z-10">
                    <a href="/events" class="group inline-flex items-center gap-1.5 rounded-lg bg-black/40 backdrop-blur-md px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:bg-black/60 cursor-pointer border border-white/10">
                        <x-icon name="arrow-left" class="h-4 w-4 transition-transform group-hover:-translate-x-0.5" />
                        {{ __('site.eventDetail.back_all_events') }}
                    </a>
                </div>

                {{-- Bottom Overlay Info Bar --}}
                <div class="absolute bottom-0 left-0 right-0 z-10">
                    <div class="px-6 pb-6">
                        <div class="flex flex-col md:flex-row md:items-end gap-5">
                            {{-- Event Date/Icon Badge Box --}}
                            <div class="h-24 w-24 sm:h-28 sm:w-28 rounded-2xl overflow-hidden bg-white text-primary flex flex-col items-center justify-center border-4 border-white/20 shadow-2xl shrink-0 select-none backdrop-blur-sm p-2 text-center">
                                @if ($eventImage)
                                    <x-safe-image :src="$eventImage" :alt="$event->title" :title="$event->title" fallback-type="event" img-class="h-full w-full object-contain rounded-xl" />
                                @else
                                    <span class="text-[12px] font-black uppercase text-primary tracking-wider">{{ $event->date->format('M') }}</span>
                                    <span class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-none">{{ $event->date->format('d') }}</span>
                                    <span class="text-[11px] font-bold text-slate-500">{{ $event->date->format('Y') }}</span>
                                @endif
                            </div>

                            {{-- Title and Meta --}}
                            <div class="flex-1 space-y-1.5 md:pb-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white/15 backdrop-blur-sm px-2.5 py-0.5 text-[12px] font-bold text-white border border-white/20">
                                        <x-icon name="tag" class="h-3 w-3" /> {{ $event->type }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 text-[12px] font-bold text-emerald-300 bg-emerald-500/15 backdrop-blur-sm px-2.5 py-0.5 rounded-full border border-emerald-400/20">
                                        <x-icon name="users" class="h-3 w-3 text-emerald-400 animate-pulse" /> {{ $attendeesCount }} {{ __('site.eventDetail.registered') }}
                                    </span>
                                </div>

                                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight drop-shadow-lg">{{ $event->title }}</h1>

                                <div class="flex flex-wrap gap-x-4 gap-y-1 text-[12px] text-white/80 font-medium">
                                    <span class="inline-flex items-center gap-1"><x-icon name="calendar" class="h-3 w-3 text-white/90" /> {{ $event->date->format('M j, Y') }}</span>
                                    <span class="inline-flex items-center gap-1"><x-icon name="clock" class="h-3 w-3 text-white/90" /> {{ $event->date->format('g:i A') }}</span>
                                    @if ($event->location)
                                        <span class="inline-flex items-center gap-1"><x-icon name="map-pin" class="h-3 w-3 text-white/90" /> {{ $event->location }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Quick Action Button --}}
                            <div class="flex gap-2 shrink-0 md:pb-1">
                                @if ($userRegistration)
                                    <a
                                        href="/profile?tab=events"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-5 py-3 text-xs font-bold text-white shadow-lg transition-all hover:bg-emerald-700 active:scale-[0.98] cursor-pointer"
                                    >
                                        <x-icon name="check-circle-2" class="h-4 w-4" /> {{ $userRegistration->status === 'approved' ? __('site.eventDetail.registered') : ucfirst($userRegistration->status) }}
                                    </a>
                                @elseif ($isBookingOpen)
                                    <button
                                        type="button"
                                        x-on:click="openModal"
                                        class="group inline-flex items-center justify-center gap-1.5 rounded-xl bg-white px-5 py-3 text-xs font-bold text-slate-900 shadow-lg transition-all hover:bg-primary hover:text-white active:scale-[0.98] cursor-pointer"
                                    >
                                        <x-icon name="ticket" class="h-3.5 w-3.5" /> {{ __('site.eventDetail.reserve_seat') }}
                                    </button>
                                @elseif ($status === 'upcoming')
                                    <span class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-3 text-xs font-bold text-amber-300">
                                        <x-icon name="clock" class="h-3.5 w-3.5" /> {{ __('site.eventDetail.booking_soon') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-3 text-xs font-bold text-slate-300">
                                        {{ __('site.eventDetail.booking_closed') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

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
                                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[12px] font-semibold text-primary">{{ $i + 1 }}</span>
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
                                            <p class="text-[12px] font-medium text-primary leading-tight mt-0.5">{{ $speaker['role'] ?? '' }}</p>
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
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-[12px] font-bold text-muted border border-border">
                                            {{ collect(explode(' ', $member['name']))->map(fn ($n) => mb_substr($n, 0, 1))->implode('') }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate text-xs font-semibold text-foreground leading-tight">{{ $member['name'] }}</p>
                                            <p class="truncate text-[12px] text-muted leading-tight mt-0.5">{{ $member['role'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </section>

                    @if ($galleryItems->isNotEmpty())
                        <section class="glass-card p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                    <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.eventDetail.event_gallery') }}</h2>
                                </div>
                                <span class="text-[11px] font-bold text-slate-400">
                                    {{ $galleryItems->count() }} Media
                                </span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($galleryItems as $idx => $item)
                                    <div x-on:click="openLightbox({{ $idx }})" class="overflow-hidden rounded-xl relative cursor-pointer group aspect-video hover:shadow-md transition-all duration-300 border border-border bg-slate-900 flex items-center justify-center">
                                        @if ($item['type'] === 'youtube')
                                            {{-- YouTube Video Thumbnail with Play Button --}}
                                            <img src="{{ $item['src'] }}" alt="{{ $item['caption'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 opacity-90 group-hover:opacity-100" />
                                            <div class="absolute inset-0 bg-black/25 group-hover:bg-black/40 transition-colors flex items-center justify-center">
                                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-red-600 text-white shadow-lg transition-transform group-hover:scale-110">
                                                    <svg class="w-5 h-5 fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                                </div>
                                            </div>
                                            <div class="absolute top-2 left-2 inline-flex items-center gap-1 rounded-md bg-black/70 backdrop-blur-xs px-2 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">
                                                <x-icon name="video" class="h-3 w-3 text-red-400" />
                                                <span>YouTube</span>
                                            </div>
                                        @elseif ($item['type'] === 'video')
                                            {{-- Local Video --}}
                                            <video src="{{ $item['src'] }}" class="w-full h-full object-cover" muted preload="metadata"></video>
                                            <div class="absolute inset-0 bg-black/30 flex items-center justify-center group-hover:bg-black/50 transition-colors">
                                                <span class="p-2 bg-white/20 backdrop-blur rounded-full text-white">
                                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                                </span>
                                            </div>
                                        @else
                                            {{-- Image: Full Uncropped Image with Ambient Blurred Backdrop --}}
                                            <x-safe-image
                                                :src="$item['src']"
                                                :alt="$item['caption'] ?: 'Event photo ' . ($idx + 1)"
                                                :title="$item['caption']"
                                                :blur-backdrop="true"
                                                fallback-type="gallery"
                                                img-class="h-full w-full object-contain"
                                            />
                                        @endif

                                        @if ($item['caption'])
                                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-2 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                                <p class="text-[11px] text-white font-semibold truncate">{{ $item['caption'] }}</p>
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
                        <p class="text-[12px] font-bold uppercase tracking-wider text-muted-foreground mb-2">{{ __('site.eventDetail.ticket_prices') }}</p>
                        <div class="space-y-1.5 text-center">
                            <div class="py-1.5 px-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <span class="text-[12px] font-semibold text-muted-foreground">{{ __('site.eventDetail.standard') }}:</span>
                                <span class="text-sm font-bold text-foreground">{{ format_price($priceNormal) }}</span>
                            </div>

                            @if ($isVerifiedMember)
                                <div class="py-1.5 px-2.5 rounded-xl border flex items-center justify-between transition-colors bg-emerald-50 border-emerald-200 text-emerald-700">
                                    <span class="text-[12px] font-semibold text-emerald-600 flex items-center gap-0.5"><x-icon name="shield-check" class="h-[11px] w-[11px]" /> {{ __('site.eventDetail.verified') }}:</span>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-foreground">{{ format_price($priceVerified) }}</span>
                                    </div>
                                </div>
                            @else
                                <a href="/profile?tab=business" class="block py-1.5 px-2.5 rounded-xl border flex items-center justify-between transition-colors {{ $status === 'past' ? 'bg-slate-50 border-border text-muted-foreground pointer-events-none' : 'bg-white border-border hover:bg-emerald-50/20 hover:border-emerald-200 text-muted-foreground group cursor-pointer' }}">
                                    <span class="text-[12px] font-semibold text-emerald-600 flex items-center gap-0.5"><x-icon name="shield-check" class="h-[11px] w-[11px]" /> {{ __('site.eventDetail.verified') }}:</span>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-foreground">{{ format_price($priceVerified) }}</span>
                                        @if ($status !== 'past')
                                            <span class="block text-[12px] font-semibold text-emerald-600">{{ __('site.eventDetail.click_to_get') }} &rarr;</span>
                                        @endif
                                    </div>
                                </a>
                            @endif
                        </div>

                        <div class="mt-3 border-t border-border pt-3 w-full text-center space-y-2.5">
                            {{-- Dynamic Single Booking Status / Date Display --}}
                            @if ($status === 'upcoming' && $event->booking_start_date)
                                <div class="rounded-xl bg-amber-50/80 border border-amber-200/80 p-2.5 text-left flex items-center justify-between text-[11px]">
                                    <span class="text-amber-700 font-semibold flex items-center gap-1.5">
                                        <x-icon name="calendar" class="h-3.5 w-3.5 text-amber-600" /> Booking Starts:
                                    </span>
                                    <span class="font-bold text-amber-900">
                                        {{ $event->booking_start_date->format('M j, Y') }}
                                    </span>
                                </div>
                            @elseif ($status === 'open')
                                <div class="rounded-xl bg-emerald-50/80 border border-emerald-200/80 p-2.5 text-left flex items-center justify-between text-[11px]">
                                    <span class="text-emerald-700 font-semibold flex items-center gap-1.5">
                                        <x-icon name="clock" class="h-3.5 w-3.5 text-emerald-600" /> Booking Ends:
                                    </span>
                                    <span class="font-bold text-emerald-900">
                                        {{ $event->booking_end_date ? $event->booking_end_date->format('M j, Y') : ($event->date ? $event->date->format('M j, Y') : 'Event Date') }}
                                    </span>
                                </div>
                            @elseif ($status === 'closed')
                                <div class="rounded-xl bg-rose-50/80 border border-rose-200/80 p-2.5 text-left flex items-center justify-between text-[11px]">
                                    <span class="text-rose-700 font-semibold flex items-center gap-1.5">
                                        <x-icon name="alert-circle" class="h-3.5 w-3.5 text-rose-600" /> Booking Closed:
                                    </span>
                                    <span class="font-bold text-rose-900">
                                        {{ $event->booking_end_date ? $event->booking_end_date->format('M j, Y') : 'Closed' }}
                                    </span>
                                </div>
                            @elseif ($status === 'past')
                                <div class="rounded-xl bg-slate-100 border border-slate-200 p-2.5 text-left flex items-center justify-between text-[11px]">
                                    <span class="text-slate-600 font-semibold">Event Concluded</span>
                                    <span class="font-bold text-slate-800">{{ $event->date ? $event->date->format('M j, Y') : '' }}</span>
                                </div>
                            @endif

                            {{-- Action Button / Already Booked Banner --}}
                            @if ($userRegistration)
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 p-3 text-left space-y-2 shadow-2xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 flex items-center gap-1">
                                            <x-icon name="check-circle-2" class="h-3.5 w-3.5 text-emerald-600" />
                                            @if ($userRegistration->status === 'approved')
                                                Ticket Confirmed
                                            @elseif ($userRegistration->status === 'pending')
                                                Reservation Pending
                                            @elseif ($userRegistration->status === 'attended')
                                                Attended
                                            @else
                                                Status: {{ ucfirst($userRegistration->status) }}
                                            @endif
                                        </span>
                                        @if ($userRegistration->ticket_number)
                                            <!-- <span class="font-mono text-[11px] font-black bg-white px-2 py-0.5 rounded-md border border-emerald-300 text-emerald-900 shadow-2xs">
                                                #{{ $userRegistration->ticket_number }}
                                            </span> -->
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-emerald-900 leading-snug font-medium">
                                        @if ($userRegistration->status === 'approved')
                                            You are already registered for this event! Your ticket is confirmed.
                                        @elseif ($userRegistration->status === 'pending')
                                            You have requested a reservation. Payment verification is in progress.
                                        @else
                                            Your reservation is {{ $userRegistration->status }}.
                                        @endif
                                    </p>
                                    <a
                                        href="/profile?tab=events"
                                        class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 px-3 shadow-sm transition-all"
                                    >
                                        <span>View My Booking / Ticket</span>
                                        <x-icon name="arrow-right" class="h-3 w-3" />
                                    </a>
                                </div>
                            @else
                                @if ($status === 'open')
                                    <button x-on:click="{{ auth()->check() ? 'isReserving = true' : "window.location.href = '/login'" }}" class="group inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-primary/20 transition-all hover:opacity-95 active:scale-[0.98] w-full cursor-pointer">
                                        <span>Reserve Seat / Book Ticket</span>
                                        <x-icon name="arrow-up-right" class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                                    </button>
                                @elseif ($status === 'upcoming')
                                    <button disabled class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50/60 px-4 py-2.5 text-xs font-bold text-amber-700 w-full opacity-90 cursor-not-allowed">
                                        <x-icon name="clock" class="h-3.5 w-3.5" />
                                        <span>Booking Starts {{ $event->booking_start_date ? $event->booking_start_date->format('M j, Y') : 'Soon' }}</span>
                                    </button>
                                @elseif ($status === 'closed')
                                    <button disabled class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50/60 px-4 py-2.5 text-xs font-bold text-rose-700 w-full opacity-90 cursor-not-allowed">
                                        <span>Booking Closed</span>
                                    </button>
                                @else
                                    <button disabled class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-xs font-bold text-slate-500 w-full opacity-90 cursor-not-allowed">
                                        <span>Event Completed</span>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="glass-card p-4">
                        <h3 class="border-b border-border pb-2 text-xs font-bold uppercase tracking-wider text-foreground">{{ __('site.eventDetail.details') }}</h3>
                        <div class="space-y-3 pt-3">
                            <div class="flex gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="map-pin" class="h-3.5 w-3.5" /></div>
                                <div>
                                    <p class="text-[12px] font-semibold text-muted">{{ __('site.eventDetail.location') }}</p>
                                    <p class="mt-0.5 text-xs font-medium text-foreground">{{ $event->location }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="zap" class="h-3.5 w-3.5" /></div>
                                <div>
                                    <p class="text-[12px] font-semibold text-muted">{{ __('site.eventDetail.format') }}</p>
                                    <p class="mt-0.5 text-xs font-medium text-foreground">{{ $event->type }} {{ __('site.eventDetail.interactive') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 space-y-2">
                            <div class="flex items-center gap-1.5 rounded-xl bg-primary-soft px-2.5 py-1.5 text-[12px] font-medium text-primary">
                                <x-icon name="check-circle-2" class="h-3 w-3" /> {{ __('site.eventDetail.limited_capacity') }}
                            </div>
                            <div class="flex items-center gap-1.5 px-1 text-[12px] text-muted">
                                <x-icon name="info" class="h-3 w-3" /> {{ __('site.eventDetail.register_before') }}
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-border bg-primary p-4 text-white">
                        <h4 class="text-xs font-bold uppercase tracking-wider">{{ __('site.eventDetail.good_to_know') }}</h4>
                        <p class="mt-1.5 text-xs leading-relaxed text-white/80">{{ __('site.eventDetail.good_to_know_desc') }}</p>
                        <div class="mt-3 inline-flex items-center gap-1.5 rounded-xl bg-white/15 px-2.5 py-1.5">
                            <x-icon name="star" class="h-3.5 w-3.5 text-white fill-current" />
                            <span class="text-[12px] font-semibold text-white">{{ __('site.eventDetail.great_experience') }}</span>
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

            <div class="relative max-w-4xl max-h-[85vh] w-full mx-12 flex items-center justify-center" x-on:click.stop="">
                <template x-if="lightboxIndex !== null && gallery[lightboxIndex]?.type === 'youtube'">
                    <div class="aspect-video w-full max-w-4xl max-h-[80vh] rounded-2xl overflow-hidden shadow-2xl border border-white/20 bg-black">
                        <iframe :src="gallery[lightboxIndex]?.embedUrl + '?autoplay=1'" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                </template>
                <template x-if="lightboxIndex !== null && gallery[lightboxIndex]?.type === 'image'">
                    <img :src="gallery[lightboxIndex]?.src" :alt="gallery[lightboxIndex]?.caption" class="max-h-[80vh] max-w-full rounded-xl shadow-2xl object-contain" />
                </template>
                <template x-if="lightboxIndex !== null && gallery[lightboxIndex]?.type === 'video'">
                    <video :src="gallery[lightboxIndex]?.src" controls autoplay class="max-h-[80vh] max-w-full rounded-xl shadow-2xl object-contain"></video>
                </template>
                <div x-show="lightboxIndex !== null && gallery[lightboxIndex]?.caption" class="absolute bottom-0 left-0 right-0 px-4 py-2 bg-gradient-to-t from-black/80 to-transparent rounded-b-xl">
                    <p class="text-xs text-white font-medium text-center" x-text="gallery[lightboxIndex]?.caption"></p>
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
        <div
            x-show="isReserving"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 font-outfit"
        >
            {{-- Backdrop --}}
            <div
                x-on:click="isReserving = false"
                x-show="isReserving"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm"
            ></div>

            {{-- Modal Card --}}
            <div
                x-show="isReserving"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white border border-slate-200/90 shadow-2xl z-10 max-h-[90vh] flex flex-col"
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-[#00379D] to-[#082e6e] text-white shrink-0">
                    <div class="min-w-0 pr-2">
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-white/20 border border-white/30 px-2 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">
                                {{ $event->type ?: 'Event' }}
                            </span>
                            <h3 class="text-sm font-bold text-white truncate">Reserve Your Seat</h3>
                        </div>
                        <p class="text-xs text-white/80 font-medium truncate mt-0.5" title="{{ $event->title }}">{{ $event->title }}</p>
                    </div>
                    <button
                        type="button"
                        x-on:click="isReserving = false"
                        class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors cursor-pointer shrink-0"
                        aria-label="Close"
                    >
                        <x-icon name="x" class="h-4 w-4" />
                    </button>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div class="p-4 overflow-y-auto space-y-3.5 flex-1">
                    {{-- Success State --}}
                    <div x-show="reserveSuccess" x-cloak class="text-center py-6 space-y-4">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 shadow-sm animate-bounce">
                            <x-icon name="check-circle-2" class="h-7 w-7" />
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-base font-black text-slate-900">Seat Reservation Requested!</h4>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                                Your payment proof has been submitted. Our administrators will verify your transaction and issue your digital entry ticket shortly.
                            </p>
                        </div>
                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-2">
                            <a
                                href="/profile?tab=events"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:opacity-95 transition-all"
                            >
                                <span>View My Bookings</span>
                                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                            </a>
                            <button
                                type="button"
                                x-on:click="isReserving = false; reserveSuccess = false"
                                class="w-full sm:w-auto rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-all cursor-pointer"
                            >
                                Close
                            </button>
                        </div>
                    </div>

                    {{-- Reservation Form --}}
                    <form x-show="!reserveSuccess" x-on:submit.prevent="submitReservation" class="space-y-4">
                        {{-- Error Alert Banner --}}
                        <div
                            x-show="reserveError"
                            x-cloak
                            class="rounded-xl bg-rose-50 border border-rose-200/80 p-3 text-xs font-semibold text-rose-800 flex items-start gap-2.5 shadow-2xs"
                        >
                            <x-icon name="alert-circle" class="h-4 w-4 text-rose-600 shrink-0 mt-0.5" />
                            <span x-text="reserveError" class="leading-relaxed"></span>
                        </div>

                        {{-- Attendee Profile Details Strip --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 p-3.5 space-y-2.5 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Attendee Information</h4>
                                <span class="text-[10px] font-bold text-primary bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200/50">
                                    {{ $isVerifiedMember ? 'Verified Member' : 'Standard Registrant' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs pt-0.5">
                                <div class="min-w-0">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Full Name</span>
                                    <span class="font-bold text-slate-900 block truncate mt-0.5">{{ auth()->user()->name ?? '' }}</span>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Email Address</span>
                                    <span class="font-bold text-slate-900 block truncate mt-0.5" title="{{ auth()->user()->email ?? '' }}">{{ auth()->user()->email ?? '' }}</span>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Mobile</span>
                                    <span class="font-bold text-slate-900 block truncate mt-0.5">{{ auth()->user()->phone ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Ticket Tier Selection --}}
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Select Ticket Tier</label>
                            <div class="grid grid-cols-2 gap-2.5">
                                {{-- Standard Tier --}}
                                <div class="p-3 rounded-2xl border transition-all {{ !$isVerifiedMember ? 'border-primary bg-primary/5 shadow-2xs ring-1 ring-primary/20' : 'border-slate-200 bg-slate-50/50 opacity-70' }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold uppercase tracking-wider {{ !$isVerifiedMember ? 'text-primary' : 'text-slate-500' }}">{{ __('site.eventDetail.standard') }}</span>
                                        @if (!$isVerifiedMember)
                                            <div class="h-4 w-4 rounded-full border-2 border-primary bg-primary flex items-center justify-center shadow-xs">
                                                <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-base font-black text-slate-900 mt-1 block">{{ format_price($priceNormal) }}</span>
                                </div>

                                {{-- Verified Member Tier --}}
                                <div class="p-3 rounded-2xl border transition-all {{ $isVerifiedMember ? 'border-emerald-500 bg-emerald-50/40 shadow-2xs ring-1 ring-emerald-500/20' : 'border-slate-200 bg-white' }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1">
                                            <x-icon name="shield-check" class="h-3 w-3" /> {{ __('site.eventDetail.verified') }}
                                        </span>
                                        @if ($isVerifiedMember)
                                            <div class="h-4 w-4 rounded-full border-2 border-emerald-600 bg-emerald-600 flex items-center justify-center shadow-xs">
                                                <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-base font-black text-emerald-700 mt-1 block">{{ format_price($priceVerified) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Screenshot Upload Dropzone --}}
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">
                                Payment Proof / Transaction Screenshot <span class="text-rose-500">*</span>
                            </label>
                            
                            <label class="relative flex flex-col items-center justify-center w-full min-h-[120px] rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/60 hover:bg-slate-100/70 hover:border-slate-300 transition-all cursor-pointer p-3.5">
                                <template x-if="paymentPreview">
                                    <div class="flex items-center gap-3.5 w-full bg-white p-2 rounded-xl border border-slate-200/80 shadow-2xs">
                                        <div class="h-16 w-16 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shrink-0 flex items-center justify-center">
                                            <img :src="paymentPreview" alt="Receipt preview" class="h-full w-full object-cover" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-bold text-slate-900 truncate" x-text="paymentFile?.name"></p>
                                            <p class="text-[10px] text-emerald-600 font-semibold mt-0.5 flex items-center gap-1">
                                                <x-icon name="check" class="h-3 w-3" /> Screenshot attached
                                            </p>
                                            <span class="text-[10px] font-bold text-primary hover:underline mt-1 inline-block">Click to replace file</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!paymentPreview">
                                    <div class="flex flex-col items-center justify-center text-center space-y-1 py-2">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-primary mb-1">
                                            <x-icon name="upload" class="h-4.5 w-4.5" />
                                        </div>
                                        <p class="text-xs font-bold text-slate-800">Upload payment screenshot or QR receipt</p>
                                        <p class="text-[10px] text-slate-400 font-medium">PNG, JPG, or WebP up to 10MB</p>
                                    </div>
                                </template>
                                <input type="file" accept="image/*" x-on:change="onFileChange" class="hidden" />
                            </label>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="reservingLoading"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#00379D] to-[#082e6e] py-3.5 px-5 text-xs font-bold text-white shadow-md shadow-primary/25 hover:opacity-95 active:scale-[0.98] transition-all disabled:opacity-60 cursor-pointer"
                            >
                                <template x-if="reservingLoading">
                                    <x-icon name="loader-2" class="h-4 w-4 text-white animate-spin" />
                                </template>
                                <template x-if="!reservingLoading">
                                    <x-icon name="shield-check" class="h-4 w-4 text-white" />
                                </template>
                                <span x-text="reservingLoading ? 'Submitting Reservation...' : 'Submit Reservation Request'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
@endunless
