@php
    $inputClass = 'w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold';
    $labelClass = 'text-[12px] font-bold text-muted-foreground uppercase tracking-wide mb-0.5 block';
@endphp

<div class="space-y-4">
    <div>
        <h3 class="text-sm font-bold text-foreground">{{ __('site.visitor_passes.title') }}</h3>
        <p class="text-[12px] text-muted">{{ __('site.visitor_passes.subtitle') }}</p>
    </div>

    @if ($successMsg)
        <div x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 4000)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700 flex items-center gap-2">
            <x-icon name="check-circle-2" class="h-3.5 w-3.5 text-emerald-600 shrink-0" /> {{ $successMsg }}
        </div>
    @endif

    <form wire:submit="purchase" class="glass-card p-4 space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">{{ __('site.visitor_passes.event_label') }}</label>
                <select wire:model="event_id" class="{{ $inputClass }}">
                    <option value="">{{ __('site.visitor_passes.event_select') }}</option>
                    @foreach ($upcomingEvents as $evt)
                        <option value="{{ $evt->id }}">{{ $evt->title }} — {{ $evt->date->format('M j, Y') }}</option>
                    @endforeach
                </select>
                @error('event_id') <p class="text-[12px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $labelClass }}">{{ __('site.visitor_passes.guest_name') }}</label>
                <input type="text" wire:model="guest_name" placeholder="{{ __('site.visitor_passes.guest_name') }}" class="{{ $inputClass }}" />
                @error('guest_name') <p class="text-[12px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $labelClass }}">{{ __('site.visitor_passes.guest_phone') }}</label>
                <input type="text" wire:model="guest_mobile" placeholder="{{ __('site.visitor_passes.guest_phone') }}" class="{{ $inputClass }}" />
                @error('guest_mobile') <p class="text-[12px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">{{ __('site.visitor_passes.guest_email') }}</label>
                <input type="email" wire:model="guest_email" placeholder="visitor@example.com" class="{{ $inputClass }}" />
                <p class="text-[12px] text-muted mt-0.5">{{ __('site.visitor_passes.email_hint') }}</p>
                @error('guest_email') <p class="text-[12px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">{{ __('site.visitor_passes.payment_screenshot') }}</label>
                <input type="file" wire:model="paymentFile" accept="image/*" class="{{ $inputClass }}" />
                @if ($paymentFile)
                    <img src="{{ $paymentFile->temporaryUrl() }}" alt="Preview" class="mt-2 h-20 rounded-lg border border-border object-cover" />
                @endif
                @error('paymentFile') <p class="text-[12px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="purchase" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer shadow-sm">
            <span wire:loading.remove wire:target="purchase">{{ __('site.visitor_passes.issue_pass') }}</span>
            <span wire:loading wire:target="purchase">{{ __('site.visitor_passes.submitting') }}</span>
        </button>
    </form>

    <div class="space-y-3">
        <h4 class="text-xs font-bold text-muted uppercase tracking-wide">{{ __('site.visitor_passes.purchased_passes') }}</h4>

        @if ($passes->isEmpty())
            <div class="py-12 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic">
                {{ __('site.visitor_passes.no_passes') }}
            </div>
        @else
            <div class="space-y-3">
                @foreach ($passes as $pass)
                    @continue(!$pass->event)
                    @php
                        $isApproved = in_array($pass->status, ['approved', 'confirmed'], true);
                        $isRejected = $pass->status === 'rejected';
                    @endphp
                    <div class="rounded-2xl border border-border bg-white hover:border-primary/30 hover:shadow-md transition-all p-4 space-y-3 group">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 shrink-0 rounded-xl bg-primary-soft text-primary flex items-center justify-center">
                                    <x-icon name="ticket" class="h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-foreground group-hover:text-primary transition-colors">{{ $pass->event->title }}</h4>
                                    <p class="text-[11px] text-muted font-medium">{{ __('site.visitor_passes.guest_prefix') }}: <b class="text-foreground">{{ $pass->guest_name }}</b></p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider border {{ $isApproved ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($isRejected ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                                @if ($isApproved)
                                    <x-icon name="check-circle-2" class="h-3 w-3" /> {{ __('site.visitor_passes.approved') }}
                                @elseif ($isRejected)
                                    <x-icon name="x-circle" class="h-3 w-3" /> {{ __('site.visitor_passes.rejected') }}
                                @else
                                    <x-icon name="clock" class="h-3 w-3" /> {{ __('site.visitor_passes.pending') }}
                                @endif
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 text-xs">
                            <div class="rounded-lg bg-surface/50 p-2">
                                <span class="text-[10px] font-bold text-muted uppercase block">{{ __('site.eventDetail.date_label') }}</span>
                                <span class="font-bold text-foreground">{{ $pass->event->date?->format('M j, Y') }}</span>
                            </div>
                            <div class="rounded-lg bg-surface/50 p-2">
                                <span class="text-[10px] font-bold text-muted uppercase block">{{ __('site.visitor_passes.guest_phone') }}</span>
                                <span class="font-bold text-foreground">{{ $pass->guest_mobile ?: '—' }}</span>
                            </div>
                            <div class="rounded-lg bg-surface/50 p-2">
                                <span class="text-[10px] font-bold text-muted uppercase block">{{ __('site.visitor_passes.guest_email') }}</span>
                                <span class="font-bold text-foreground truncate block">{{ $pass->guest_email ?: '—' }}</span>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2 border-t border-border/60">
                            <a href="/profile/events/{{ $pass->id }}" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-primary-dark">
                                {{ __('site.visitor_passes.download_ticket') }} <x-icon name="chevron-right" class="h-3 w-3" />
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
