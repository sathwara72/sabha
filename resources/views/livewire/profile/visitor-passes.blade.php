@php
    $inputClass = 'w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold';
    $labelClass = 'text-[12px] font-bold text-muted-foreground uppercase tracking-wide mb-0.5 block';
@endphp

<div class="space-y-4">
    <div>
        <h3 class="text-sm font-bold text-foreground">Visitor Passes</h3>
        <p class="text-[12px] text-muted">Buy an event pass for a guest — the ticket is emailed to them and stays here on your profile.</p>
    </div>

    @if ($successMsg)
        <div x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 4000)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700 flex items-center gap-2">
            <x-icon name="check-circle-2" class="h-3.5 w-3.5 text-emerald-600 shrink-0" /> {{ $successMsg }}
        </div>
    @endif

    <form wire:submit="purchase" class="glass-card p-4 space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">Event</label>
                <select wire:model="event_id" class="{{ $inputClass }}">
                    <option value="">Select an event</option>
                    @foreach ($upcomingEvents as $evt)
                        <option value="{{ $evt->id }}">{{ $evt->title }} — {{ $evt->date->format('M j, Y') }}</option>
                    @endforeach
                </select>
                @error('event_id') <p class="text-[12px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $labelClass }}">Visitor's Name</label>
                <input type="text" wire:model="guest_name" placeholder="Full name" class="{{ $inputClass }}" />
                @error('guest_name') <p class="text-[12px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $labelClass }}">Visitor's Mobile</label>
                <input type="text" wire:model="guest_mobile" placeholder="10-digit mobile number" class="{{ $inputClass }}" />
                @error('guest_mobile') <p class="text-[12px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">Visitor's Email</label>
                <input type="email" wire:model="guest_email" placeholder="visitor@example.com" class="{{ $inputClass }}" />
                <p class="text-[12px] text-muted mt-0.5">The ticket &amp; QR code will be emailed here once approved.</p>
                @error('guest_email') <p class="text-[12px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">Payment Screenshot</label>
                <input type="file" wire:model="paymentFile" accept="image/*" class="{{ $inputClass }}" />
                @if ($paymentFile)
                    <img src="{{ $paymentFile->temporaryUrl() }}" alt="Preview" class="mt-2 h-20 rounded-lg border border-border object-cover" />
                @endif
                @error('paymentFile') <p class="text-[12px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="purchase" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer shadow-sm">
            <span wire:loading.remove wire:target="purchase">Buy Visitor Pass</span>
            <span wire:loading wire:target="purchase">Submitting...</span>
        </button>
    </form>

    <div class="space-y-3">
        <h4 class="text-xs font-bold text-muted uppercase tracking-wide">Purchased Passes</h4>

        @if ($passes->isEmpty())
            <div class="py-12 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic">
                No visitor passes purchased yet.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($passes as $pass)
                    @continue(!$pass->event)
                    @php
                        $isApproved = in_array($pass->status, ['approved', 'confirmed'], true);
                        $isRejected = $pass->status === 'rejected';
                    @endphp
                    <a href="/profile/events/{{ $pass->id }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-xl border border-border bg-slate-50/50 hover:bg-white hover:shadow-sm hover:border-primary/20 transition-all group">
                        <div class="h-14 w-14 shrink-0 rounded-xl bg-primary-soft text-primary flex items-center justify-center">
                            <x-icon name="ticket" class="h-5 w-5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-bold text-foreground truncate group-hover:text-primary transition-colors">{{ $pass->event->title }}</h4>
                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-[12px] text-muted font-medium mt-1">
                                <span class="inline-flex items-center gap-1"><x-icon name="calendar" class="h-3 w-3" /> {{ $pass->event->date->format('M j, Y') }}</span>
                                <span class="inline-flex items-center gap-1"><x-icon name="user" class="h-3 w-3" /> {{ $pass->guest_name }}</span>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-[12px] font-bold uppercase tracking-wide border {{ $isApproved ? 'bg-emerald-50 text-emerald-700 border-emerald-200/50' : ($isRejected ? 'bg-red-50 text-red-700 border-red-200/50' : 'bg-amber-50 text-amber-700 border-amber-200/50') }}">
                                @if ($isApproved)
                                    <x-icon name="check-circle-2" class="h-3 w-3" /> Approved
                                @elseif ($isRejected)
                                    <x-icon name="x-circle" class="h-3 w-3" /> Rejected
                                @else
                                    <x-icon name="clock" class="h-3 w-3" /> Pending
                                @endif
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
