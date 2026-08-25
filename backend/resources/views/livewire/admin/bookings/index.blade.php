<div class="space-y-3" x-data="{ previewImage: null }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-col">
            <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-foreground">Event bookings</h1>
            <p class="text-sm text-muted">Review, verify payments, and approve event seat registrations</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                wire:click="openScanner"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer"
            >
                <x-icon name="qr-code" class="h-4 w-4" />
                Scan Ticket QR
            </button>
            <button
                wire:click="$refresh"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-white px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-surface cursor-pointer transition-all"
            >
                <x-icon name="refresh-cw" class="h-4 w-4" />
                Refresh
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
        <div class="rounded-xl border p-4.5 text-center border-border text-foreground">
            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Total Bookings</p>
            <p class="text-2xl font-black mt-1 leading-none">{{ $total }}</p>
        </div>
        <div class="rounded-xl border p-4.5 text-center border-amber-200 text-amber-600 bg-amber-50/20">
            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Pending Review</p>
            <p class="text-2xl font-black mt-1 leading-none">{{ $pendingCount }}</p>
        </div>
        <div class="rounded-xl border p-4.5 text-center border-emerald-200 text-emerald-600 bg-emerald-50/20">
            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Approved Seats</p>
            <p class="text-2xl font-black mt-1 leading-none">{{ $approvedCount }}</p>
        </div>
        <div class="rounded-xl border p-4.5 text-center border-red-200 text-red-600 bg-red-50/20">
            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Rejected Requests</p>
            <p class="text-2xl font-black mt-1 leading-none">{{ $rejectedCount }}</p>
        </div>
    </div>

    <div class="flex items-center gap-3 rounded-xl bg-primary-soft p-4">
        <x-icon name="info" class="h-5 w-5 shrink-0 text-primary" />
        <p class="text-sm font-medium text-foreground">
            Verify that the payment amount matches the standard/verified price and that the screenshot is valid before approval.
        </p>
    </div>

    <div>
        @if ($registrations->isEmpty())
            <div class="rounded-xl border border-dashed border-border py-20 text-center text-muted">
                No event booking requests found.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($registrations as $reg)
                    <div class="glass-card p-4 border border-border flex flex-col justify-between h-full bg-white rounded-2xl">
                        <div class="flex flex-col justify-between h-full gap-3">
                            <div class="space-y-3">
                                <div class="space-y-2">
                                    <p class="text-[10px] font-bold text-muted uppercase tracking-wider">Attendee Info</p>
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-base font-bold text-primary">
                                            {{ $reg->user?->name ? mb_substr($reg->user->name, 0, 1) : '?' }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-bold text-foreground truncate">{{ $reg->user?->name ?: 'Unknown' }}</h4>
                                            <p class="text-[11px] text-muted-foreground truncate flex items-center gap-1 mt-0.5">
                                                <x-icon name="mail" class="h-2.5 w-2.5" /> {{ $reg->user?->email }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 rounded bg-slate-100 border border-slate-200 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider {{ $reg->ticket_type === 'verified' ? 'text-emerald-700 bg-emerald-50 border-emerald-100' : 'text-muted' }}">
                                            {{ $reg->ticket_type === 'verified' ? '⭐ Sabha Member' : 'Standard Tier' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="border-t border-border/80"></div>

                                <div class="space-y-2">
                                    <p class="text-[10px] font-bold text-muted uppercase tracking-wider">Payment & Ticket</p>
                                    <p class="text-xs text-muted-foreground">Ticket No: <span class="font-semibold text-foreground font-mono">{{ $reg->ticket_number ?: 'Pending Approval' }}</span></p>

                                    @if (media_url($reg->payment_screenshot))
                                        <button
                                            x-on:click="previewImage = {{ Illuminate\Support\Js::from(media_url($reg->payment_screenshot)) }}"
                                            class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline cursor-pointer"
                                        >
                                            <x-icon name="file-text" class="h-3.5 w-3.5" /> View Receipt
                                        </button>
                                    @else
                                        <p class="text-xs text-red-500 font-semibold flex items-center gap-1">
                                            <x-icon name="info" class="h-3 w-3" /> No screenshot uploaded
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="border-t border-border/80 pt-3 mt-auto">
                                @if ($reg->status === 'pending')
                                    <div class="flex items-center gap-2 w-full">
                                        <button wire:click="approve({{ $reg->id }})" class="flex-1 inline-flex items-center justify-center gap-1 rounded-xl bg-primary py-2 text-xs font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer">
                                            <x-icon name="check-circle-2" class="h-3 w-3" /> Approve
                                        </button>
                                        <button
                                            x-on:click="const reason = prompt('Please enter the reason for rejection:'); if (reason !== null) { if (reason.trim()) { $wire.reject({{ $reg->id }}, reason.trim()); } else { alert('Rejection reason is required.'); } }"
                                            class="flex-1 inline-flex items-center justify-center gap-1 rounded-xl border border-red-100 bg-red-50 py-2 text-xs font-semibold text-red-600 transition-all hover:bg-red-100 active:scale-[0.98] cursor-pointer"
                                        >
                                            <x-icon name="x-circle" class="h-3 w-3" /> Reject
                                        </button>
                                    </div>
                                @else
                                    <div class="w-full flex flex-col gap-2">
                                        @if ($reg->status === 'rejected' && $reg->rejection_reason)
                                            <p class="text-[10px] text-red-500 bg-red-50 border border-red-100/50 rounded-lg p-1.5 text-left max-w-full leading-relaxed">
                                                <strong>Reason:</strong> {{ $reg->rejection_reason }}
                                            </p>
                                        @endif
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="inline-flex items-center gap-1.5 rounded-full border border-border px-2.5 py-1 text-[10px] font-bold text-muted justify-center shrink-0">
                                                <x-icon name="shield-check" class="h-3 w-3 {{ in_array($reg->status, ['approved', 'confirmed']) ? 'text-primary' : 'text-muted' }}" />
                                                {{ in_array($reg->status, ['approved', 'confirmed']) ? 'Verified' : 'Rejected' }}
                                            </div>

                                            @if (in_array($reg->status, ['approved', 'confirmed']))
                                                <button
                                                    wire:click="toggleAttendance({{ $reg->id }})"
                                                    class="inline-flex items-center justify-center gap-1 rounded-xl border px-2.5 py-1 text-[10px] font-bold cursor-pointer transition-all active:scale-[0.98] shrink-0 {{ $reg->is_attended ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}"
                                                >
                                                    <span class="h-1.5 w-1.5 rounded-full {{ $reg->is_attended ? 'bg-emerald-600 animate-pulse' : 'bg-slate-400' }}"></span>
                                                    {{ $reg->is_attended ? 'Attended' : 'Attendance' }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Payment Screenshot Lightbox --}}
    <div x-show="previewImage" x-cloak class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-black/90 backdrop-blur-sm" x-on:click="previewImage = null">
        <button x-on:click="previewImage = null" class="absolute top-4 right-4 md:top-6 md:right-6 z-10 p-2.5 rounded-full bg-white/15 text-white hover:bg-white/25 transition-all hover:scale-105 active:scale-95 cursor-pointer shadow-lg">
            <x-icon name="x" class="h-5.5 w-5.5" />
        </button>
        <div class="max-w-[95vw] max-h-[88vh] flex items-center justify-center p-2" x-on:click.stop>
            <img :src="previewImage" alt="Payment receipt proof" class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl border border-white/10" />
        </div>
    </div>

    {{-- QR Scanner Modal --}}
    @if ($isScannerOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl w-full max-w-[340px] overflow-hidden border border-border shadow-2xl flex flex-col">
                <div class="flex items-center justify-between px-4.5 py-3 border-b border-border">
                    <div class="flex items-center gap-2">
                        <x-icon name="qr-code" class="h-4.5 w-4.5 text-primary" />
                        <h3 class="text-sm font-bold text-foreground">Scan QR Code / Check-in</h3>
                    </div>
                    <button x-on:click="window.stopQrScanner(); $wire.closeScanner()" class="p-1.5 rounded-lg text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4 w-4" />
                    </button>
                </div>

                <div class="p-4.5 space-y-4 flex-1 overflow-y-auto">
                    @if ($scanSuccess !== null)
                        <div class="text-center py-4 space-y-3">
                            @if ($scanSuccess)
                                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 animate-bounce">
                                    <x-icon name="check-circle-2" class="h-6 w-6" />
                                </div>
                                <h4 class="text-sm font-bold text-foreground">Successful Check-in</h4>
                                <p class="text-xs text-emerald-700 bg-emerald-50/50 border border-emerald-100/50 rounded-xl p-2.5 leading-relaxed font-medium mx-auto max-w-xs">{{ $scanMessage }}</p>
                            @else
                                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600 border border-red-100">
                                    <x-icon name="x-circle" class="h-6 w-6" />
                                </div>
                                <h4 class="text-sm font-bold text-foreground">Check-in Failed</h4>
                                <p class="text-xs text-red-700 bg-red-50/50 border border-red-100/50 rounded-xl p-2.5 leading-relaxed font-medium mx-auto max-w-xs">{{ $scanMessage }}</p>
                            @endif

                            <div class="pt-2 flex items-center gap-2.5 justify-center">
                                <button wire:click="resetScan" class="px-4 py-2 rounded-xl bg-primary text-xs font-semibold text-white shadow-sm hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer">
                                    Scan Next Ticket
                                </button>
                                <button x-on:click="window.stopQrScanner(); $wire.closeScanner()" class="px-4 py-2 rounded-xl border border-border bg-white text-xs font-semibold text-foreground hover:bg-slate-50 transition-all cursor-pointer">
                                    Done
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4" x-data x-init="window.startQrScanner('qr-reader', (text) => $wire.processCheckIn(text))">
                            <div class="relative rounded-xl overflow-hidden bg-slate-900 h-[180px] w-full flex flex-col items-center justify-center border border-slate-800">
                                <div id="qr-reader" class="w-full h-full overflow-hidden [&_video]:object-cover [&_video]:w-full [&_video]:h-full"></div>

                                <div class="absolute inset-0 border-[20px] border-black/40 pointer-events-none flex items-center justify-center">
                                    <div class="w-[110px] h-[110px] border-2 border-dashed border-primary relative">
                                        <div class="absolute left-0 right-0 h-0.5 bg-primary/80 top-0 animate-[scan_2s_ease-in-out_infinite]"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="relative flex py-1 items-center">
                                <div class="flex-grow border-t border-border"></div>
                                <span class="flex-shrink mx-3 text-[10px] font-bold text-muted uppercase tracking-wider">or Enter Manually</span>
                                <div class="flex-grow border-t border-border"></div>
                            </div>

                            <form wire:submit="submitManualCheckIn" x-on:submit="window.stopQrScanner()" class="flex gap-2">
                                <input
                                    type="text"
                                    wire:model="manualTicketNo"
                                    placeholder="e.g. 2026-SNM-8148"
                                    class="flex-1 rounded-xl border border-border px-3.5 py-2 text-xs font-semibold text-foreground focus:border-primary focus:outline-none placeholder-muted-foreground/60 transition-colors uppercase font-mono"
                                />
                                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition-all hover:bg-slate-800 active:scale-[0.98] disabled:opacity-50 cursor-pointer shrink-0">
                                    Check In
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <style>
        @keyframes scan {
            0%, 100% { top: 0%; }
            50% { top: 100%; }
        }
    </style>
</div>
