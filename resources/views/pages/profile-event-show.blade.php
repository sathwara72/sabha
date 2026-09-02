@unless ($registration)
    <x-layouts.app :title="__('site.bookingDetail.not_found_title') . ' | Sabha'" :noindex="true">
        <div class="min-h-screen flex flex-col items-center justify-center bg-background gap-4 px-6 text-center">
            <div class="text-6xl">🎟️</div>
            <h2 class="text-xl font-bold text-foreground">{{ __('site.bookingDetail.not_found_title') }}</h2>
            <p class="text-sm text-muted">{{ __('site.bookingDetail.not_found_desc') }}</p>
            <a href="/profile?tab=events" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white mt-2">
                <x-icon name="arrow-left" class="h-4 w-4" /> {{ __('site.bookingDetail.back_my_events') }}
            </a>
        </div>
    </x-layouts.app>
@else
@php
    $event = $registration->event;
    $statusConfig = [
        'confirmed' => ['label' => __('site.bookingDetail.status_confirmed'), 'color' => 'text-emerald-700', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'icon' => 'check-circle-2'],
        'approved' => ['label' => __('site.bookingDetail.status_confirmed'), 'color' => 'text-emerald-700', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'icon' => 'check-circle-2'],
        'pending' => ['label' => __('site.bookingDetail.status_pending'), 'color' => 'text-amber-700', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'icon' => 'alert-circle'],
        'rejected' => ['label' => __('site.bookingDetail.status_rejected'), 'color' => 'text-red-700', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'icon' => 'x-circle'],
    ];
    $status = $statusConfig[$registration->status] ?? $statusConfig['pending'];
    $heroImage = media_url($event->image) ?: 'https://images.unsplash.com/photo-1540575861501-7ad0582373f3?q=80&w=1400&auto=format&fit=crop';
    $price = (float) $registration->amount_paid === 0.0 ? 'Free' : '₹' . number_format((float) $registration->amount_paid);
    $notes = in_array($registration->status, ['confirmed', 'approved'], true)
        ? 'Please carry your QR code / ticket number at entry. Dress code: Business Formal.'
        : ($registration->status === 'rejected'
            ? 'Your registration request was rejected. Reason: ' . ($registration->rejection_reason ?: 'None specified')
            : 'Your payment is under verification. You will receive a confirmation email once approved.');
    $isConfirmed = in_array($registration->status, ['confirmed', 'approved'], true);
@endphp

<x-layouts.app :title="$event->title . ' | Sabha'" :noindex="true">
    <div class="min-h-screen bg-background font-outfit " x-data="{ copied: false, copy() { navigator.clipboard.writeText('{{ $registration->ticket_number }}'); this.copied = true; setTimeout(() => this.copied = false, 2000) } }">
        <div class="sticky top-0 z-30 border-b border-border bg-white/90 backdrop-blur-md">
            <div class="mx-auto max-w-5xl px-6 py-4 flex items-center gap-3">
                <a href="/profile?tab=events" class="inline-flex items-center gap-1.5 text-sm font-semibold text-muted hover:text-primary transition-colors">
                    <x-icon name="arrow-left" class="h-4 w-4" /> {{ __('site.bookingDetail.my_events') }}
                </a>
                <x-icon name="chevron-right" class="h-[14px] w-[14px] text-muted-foreground" />
                <span class="text-sm font-semibold text-foreground line-clamp-1">{{ $event->title }}</span>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-6 py-5 space-y-2">
            <div class="relative h-64 sm:h-80 md:h-96 rounded-2xl overflow-hidden border border-border shadow-lg bg-slate-950 flex items-center justify-center">
                <x-safe-image
                    :src="$heroImage"
                    :alt="$event->title"
                    :title="$event->title"
                    :date="$event->date"
                    :blur-backdrop="true"
                    fallback-type="event"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-transparent to-black/20 pointer-events-none"></div>
                <div class="absolute bottom-5 left-5 right-5 flex items-end justify-between gap-4 pointer-events-none">
                    <div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full mb-2">{{ $event->type }}</span>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-sm">{{ $event->title }}</h1>
                    </div>
                    <div class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold border shadow-sm {{ $status['bg'] }} {{ $status['color'] }} {{ $status['border'] }}">
                        <x-icon :name="$status['icon']" class="h-[13px] w-[13px]" /> {{ $status['label'] }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-start gap-3 rounded-xl border p-4 {{ $status['bg'] }} {{ $status['border'] }}">
                        <x-icon :name="$status['icon']" class="h-5 w-5 mt-0.5 shrink-0 {{ $status['color'] }}" />
                        <div>
                            <p class="text-sm font-bold {{ $status['color'] }}">{{ __('site.bookingDetail.booking_status') }}: {{ $status['label'] }}</p>
                            <p class="text-xs font-medium mt-0.5 {{ $status['color'] }} opacity-80">
                                @if ($registration->status === 'confirmed' || $registration->status === 'approved') {{ __('site.bookingDetail.confirmed_msg') }}
                                @elseif ($registration->status === 'pending') {{ __('site.bookingDetail.pending_msg') }}
                                @elseif ($registration->status === 'rejected') {{ __('site.bookingDetail.rejected_msg') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="glass-card p-6 space-y-4">
                        <h2 class="text-base font-bold text-foreground border-b border-border pb-2">{{ __('site.bookingDetail.event_details') }}</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="calendar" class="h-4 w-4" /></div>
                                <div>
                                    <p class="text-[12px] font-bold text-muted uppercase tracking-wider">{{ __('site.bookingDetail.date') }}</p>
                                    <p class="text-sm font-semibold text-foreground mt-0.5">{{ $event->date->format('M j, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="clock" class="h-4 w-4" /></div>
                                <div>
                                    <p class="text-[12px] font-bold text-muted uppercase tracking-wider">{{ __('site.bookingDetail.time') }}</p>
                                    <p class="text-sm font-semibold text-foreground mt-0.5">{{ $event->date->format('g:i A') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 sm:col-span-2">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="map-pin" class="h-4 w-4" /></div>
                                <div>
                                    <p class="text-[12px] font-bold text-muted uppercase tracking-wider">{{ __('site.bookingDetail.venue') }}</p>
                                    <p class="text-sm font-semibold text-foreground mt-0.5">{{ $event->location }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="users" class="h-4 w-4" /></div>
                                <div>
                                    <p class="text-[12px] font-bold text-muted uppercase tracking-wider">{{ __('site.bookingDetail.attendees') }}</p>
                                    <p class="text-sm font-semibold text-foreground mt-0.5">100+ {{ __('site.bookingDetail.registered') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="credit-card" class="h-4 w-4" /></div>
                                <div>
                                    <p class="text-[12px] font-bold text-muted uppercase tracking-wider">{{ __('site.bookingDetail.amount_paid') }}</p>
                                    <p class="text-sm font-semibold text-foreground mt-0.5">{{ $price }} <span class="text-muted font-medium">{{ __('site.bookingDetail.on') }} {{ $registration->created_at->format('M j, Y') }}</span></p>
                                </div>
                            </div>
                        </div>

                        @if ($event->description)
                            <div class="pt-3 border-t border-border">
                                <p class="text-xs text-muted leading-relaxed font-medium">{{ $event->description }}</p>
                            </div>
                        @endif
                    </div>

                    @if (!empty($event->agenda))
                        <div class="glass-card p-6 space-y-4">
                            <h2 class="text-base font-bold text-foreground border-b border-border pb-2">{{ __('site.bookingDetail.agenda') }}</h2>
                            <ol class="space-y-3">
                                @foreach ($event->agenda as $i => $item)
                                    <li class="flex items-start gap-3">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-white text-[12px] font-bold mt-0.5">{{ $i + 1 }}</span>
                                        <span class="text-sm font-medium text-foreground">{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    <div class="flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <x-icon name="info" class="h-5 w-5 text-blue-500 shrink-0 mt-0.5" />
                        <p class="text-xs font-semibold text-blue-800">{{ $notes }}</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="glass-card overflow-hidden border border-border">
                        <div class="bg-primary px-5 py-4">
                            <div class="flex items-center gap-2 text-white">
                                <x-icon name="ticket" class="h-4 w-4" />
                                <span class="text-sm font-bold">{{ __('site.bookingDetail.your_ticket') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between px-5 py-3 border-y border-dashed border-border">
                            <div class="flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold border {{ $status['bg'] }} {{ $status['color'] }} {{ $status['border'] }}">
                                <x-icon :name="$status['icon']" class="h-3 w-3" /> {{ $status['label'] }}
                            </div>
                            @if ($registration->is_attended)
                                <div class="flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold border border-emerald-100 bg-emerald-50 text-emerald-700">
                                    <x-icon name="check-circle-2" class="h-3 w-3" /> Attended
                                </div>
                            @endif
                        </div>

                        <div class="px-5 py-5 space-y-4">
                            <div>
                                <p class="text-[12px] font-bold text-muted uppercase tracking-wider">{{ __('site.bookingDetail.ticket_number') }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-sm font-extrabold text-foreground font-mono tracking-wider">{{ $registration->ticket_number ?: 'Pending Approval' }}</p>
                                    @if ($registration->ticket_number)
                                        <button type="button" x-on:click="copy" class="text-primary hover:opacity-70 transition-opacity" title="Copy ticket number">
                                            <x-icon name="check-circle-2" x-show="copied" class="h-[15px] w-[15px] text-emerald-500" />
                                            <x-icon name="share-2" x-show="!copied" class="h-[15px] w-[15px]" />
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <p class="text-[12px] font-bold text-muted uppercase tracking-wider">{{ __('site.bookingDetail.registered_for') }}</p>
                                <p class="text-sm font-semibold text-foreground mt-1 leading-snug">{{ $event->title }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-[12px] font-bold text-muted uppercase tracking-wider">{{ __('site.bookingDetail.date') }}</p>
                                    <p class="text-xs font-semibold text-foreground mt-0.5">{{ $event->date->format('M j, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[12px] font-bold text-muted uppercase tracking-wider">{{ __('site.bookingDetail.paid') }}</p>
                                    <p class="text-xs font-semibold text-foreground mt-0.5">{{ $price }}</p>
                                </div>
                            </div>

                            @if ($registration->ticket_number && $isConfirmed)
                                <div class="mt-2 flex flex-col items-center justify-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <x-qr-code :value="$registration->ticket_number" :size="120" class="border border-slate-200 rounded-lg p-1 bg-white" />
                                    <span class="text-[12px] font-bold text-muted-foreground uppercase tracking-widest">Scan QR at Entrance</span>
                                </div>
                            @else
                                <div class="mt-2 flex justify-center">
                                    <div class="h-14 w-full rounded-lg bg-gradient-to-r from-slate-900 via-slate-700 to-slate-900 flex items-center justify-center gap-px px-4 overflow-hidden">
                                        @for ($i = 0; $i < 38; $i++)
                                            <div class="bg-white rounded-sm" style="height: {{ (($i * 37) % 60) + 30 }}%; width: {{ $i % 3 === 0 ? '3px' : '2px' }}; opacity: {{ 0.5 + (($i * 13) % 50) / 100 }};"></div>
                                        @endfor
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if (($registration->status === 'confirmed' || $registration->status === 'approved') && $registration->ticket_number)
                            <div class="border-t border-border px-5 py-4 space-y-2">
                                <a href="{{ route('profile.events.ticket', $registration->id) }}" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white transition-all hover:opacity-90">
                                    <x-icon name="download" class="h-[14px] w-[14px]" /> {{ __('site.bookingDetail.download_ticket') }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <a href="/events/{{ $event->id }}" class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-white px-4 py-2.5 text-xs font-bold text-foreground transition-all hover:bg-surface">
                        {{ __('site.bookingDetail.view_event_page') }} <x-icon name="chevron-right" class="h-[14px] w-[14px]" />
                    </a>

                    <div class="flex items-center gap-2 rounded-xl border border-border bg-surface p-3">
                        <x-icon name="shield-check" class="h-[15px] w-[15px] text-primary shrink-0" />
                        <p class="text-[12px] font-semibold text-muted">{{ __('site.bookingDetail.trust_note') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
@endunless
