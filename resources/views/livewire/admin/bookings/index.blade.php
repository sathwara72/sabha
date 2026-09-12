<div class="space-y-4 font-outfit w-full" x-data="{ previewImage: null }">
    {{-- Top Header & Action Buttons --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">Event Bookings & Tickets</h1>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
            <button
                type="button"
                wire:click="openScanner"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2 text-xs font-bold text-white shadow-sm shadow-primary/20 hover:opacity-95 active:scale-[0.98] transition-all cursor-pointer"
            >
                <x-icon name="qr-code" class="h-4 w-4" />
                <span>Scan Ticket QR</span>
            </button>
            <button
                type="button"
                wire:click="$refresh"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all cursor-pointer shadow-2xs"
            >
                <x-icon name="refresh-cw" class="h-3.5 w-3.5 text-slate-500" />
                <span>Refresh</span>
            </button>
        </div>
    </div>

    {{-- Success Notification Banner --}}
    @if ($successMsg)
        <div
            wire:key="book-success-{{ md5($successMsg) }}"
            x-data="{ show: true }"
            x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 3500)"
            x-show="show"
            x-transition
            class="rounded-xl bg-emerald-50 border border-emerald-200/80 p-3 text-xs font-semibold text-emerald-800 flex items-center gap-2 shadow-2xs"
        >
            <x-icon name="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0" />
            <span>{{ $successMsg }}</span>
        </div>
    @endif

    {{-- 4 Metric Stat Cards (Clickable Filter Shortcuts) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <button
            type="button"
            wire:click="$set('statusFilter', 'all')"
            class="group flex items-center justify-between rounded-2xl border p-3.5 text-left transition-all cursor-pointer {{ $statusFilter === 'all' ? 'border-primary ring-2 ring-primary/40 bg-blue-100/90 shadow-sm' : 'border-blue-200/70 bg-blue-50/60 hover:bg-blue-100/70 hover:border-blue-300 shadow-2xs' }}"
        >
            <div>
                <p class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">Total Bookings</p>
                <h3 class="mt-1 text-xl sm:text-2xl font-black text-blue-950 leading-none">{{ $total }}</h3>
            </div>
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-primary border border-blue-200 shadow-2xs">
                <x-icon name="calendar" class="h-4.5 w-4.5" />
            </div>
        </button>

        <button
            type="button"
            wire:click="$set('statusFilter', 'pending')"
            class="group flex items-center justify-between rounded-2xl border p-3.5 text-left transition-all cursor-pointer {{ $statusFilter === 'pending' ? 'border-amber-500 ring-2 ring-amber-400/40 bg-amber-100/90 shadow-sm' : 'border-amber-200/70 bg-amber-50/60 hover:bg-amber-100/70 hover:border-amber-300 shadow-2xs' }}"
        >
            <div>
                <p class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Pending Review</p>
                <h3 class="mt-1 text-xl sm:text-2xl font-black text-amber-950 leading-none">{{ $pendingCount }}</h3>
            </div>
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-amber-600 border border-amber-200 shadow-2xs">
                <x-icon name="clock" class="h-4.5 w-4.5" />
            </div>
        </button>

        <button
            type="button"
            wire:click="$set('statusFilter', 'approved')"
            class="group flex items-center justify-between rounded-2xl border p-3.5 text-left transition-all cursor-pointer {{ $statusFilter === 'approved' ? 'border-emerald-500 ring-2 ring-emerald-400/40 bg-emerald-100/90 shadow-sm' : 'border-emerald-200/70 bg-emerald-50/60 hover:bg-emerald-100/70 hover:border-emerald-300 shadow-2xs' }}"
        >
            <div>
                <p class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Approved Seats</p>
                <h3 class="mt-1 text-xl sm:text-2xl font-black text-emerald-950 leading-none">{{ $approvedCount }}</h3>
            </div>
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-emerald-600 border border-emerald-200 shadow-2xs">
                <x-icon name="shield-check" class="h-4.5 w-4.5" />
            </div>
        </button>

        <button
            type="button"
            wire:click="$set('statusFilter', 'attended')"
            class="group flex items-center justify-between rounded-2xl border p-3.5 text-left transition-all cursor-pointer {{ $statusFilter === 'attended' ? 'border-purple-500 ring-2 ring-purple-400/40 bg-purple-100/90 shadow-sm' : 'border-purple-200/70 bg-purple-50/60 hover:bg-purple-100/70 hover:border-purple-300 shadow-2xs' }}"
        >
            <div>
                <p class="text-[11px] font-bold text-purple-700 uppercase tracking-wider">Attended / Present</p>
                <h3 class="mt-1 text-xl sm:text-2xl font-black text-purple-950 leading-none">{{ $attendedCount }}</h3>
            </div>
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-purple-600 border border-purple-200 shadow-2xs">
                <x-icon name="user-check" class="h-4.5 w-4.5" />
            </div>
        </button>
    </div>

    {{-- Search & Filter Toolbar --}}
    <div class="space-y-3.5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-2.5 sm:p-3 rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center gap-2 flex-1 max-w-xl">
                {{-- Search Box --}}
                <div class="relative flex-1">
                    <x-icon
                        name="search"
                        class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400"
                        wire:loading.remove
                        wire:target="search"
                    />
                    <x-icon
                        name="loader-2"
                        class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-primary animate-spin"
                        wire:loading
                        wire:target="search"
                    />
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search attendee, ticket #, email, phone, or event..."
                        class="w-full rounded-lg border border-slate-200 bg-slate-50/50 py-1.5 pl-9 pr-8 text-xs font-medium text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10"
                    />
                    @if ($search !== '')
                        <button
                            type="button"
                            wire:click="$set('search', '')"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors"
                        >
                            <x-icon name="x" class="h-3 w-3" />
                        </button>
                    @endif
                </div>

                {{-- Event Filter Dropdown --}}
                <div class="w-48 shrink-0">
                    <select
                        wire:model.live="eventFilter"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50/50 py-1.5 px-2.5 text-xs font-medium text-slate-700 outline-none focus:bg-white focus:border-primary"
                    >
                        <option value="">All Events</option>
                        @foreach ($eventsList as $evt)
                            <option value="{{ $evt->id }}">{{ $evt->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Status Tabs --}}
            <div class="flex items-center gap-1 overflow-x-auto pb-1 md:pb-0">
                @php
                    $tabs = [
                        'all' => 'All (' . $total . ')',
                        'pending' => 'Pending (' . $pendingCount . ')',
                        'approved' => 'Approved (' . $approvedCount . ')',
                        'attended' => 'Attended (' . $attendedCount . ')',
                        'rejected' => 'Rejected (' . $rejectedCount . ')',
                    ];
                @endphp
                @foreach ($tabs as $key => $label)
                    <button
                        type="button"
                        wire:click="$set('statusFilter', '{{ $key }}')"
                        class="rounded-lg px-2.5 py-1 text-[11px] font-bold transition-all whitespace-nowrap cursor-pointer {{ $statusFilter === $key ? 'bg-primary text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Bookings Cards Grid --}}
        <div wire:loading.class="opacity-50 pointer-events-none" wire:target="search,statusFilter,eventFilter" class="transition-opacity duration-150">
            @if ($registrations->isEmpty())
                <div class="py-16 text-center text-slate-500 text-xs rounded-xl border border-dashed border-slate-200 bg-white shadow-2xs">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-2">
                        <x-icon name="calendar" class="h-5 w-5" />
                    </div>
                    <p class="font-bold text-slate-700">No booking requests found.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Try clearing filters or search terms.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    @foreach ($registrations as $reg)
                        <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs flex flex-col justify-between hover:shadow-md hover:border-slate-300 transition-all duration-200 space-y-3">
                            {{-- Card Header: Attendee & Status --}}
                            <div class="space-y-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#00379D] to-[#0F3459] text-white text-xs font-black uppercase shadow-2xs">
                                            {{ $reg->attendeeName() ? mb_substr($reg->attendeeName(), 0, 1) : '?' }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-bold text-slate-900 truncate" title="{{ $reg->attendeeName() }}">
                                                {{ $reg->attendeeName() ?: 'Unknown Attendee' }}
                                            </h4>
                                            <p class="text-[11px] text-slate-500 truncate flex items-center gap-1 mt-0.5">
                                                <x-icon name="mail" class="h-2.5 w-2.5 text-slate-400 shrink-0" />
                                                <span class="truncate">{{ $reg->attendeeEmail() ?: 'No email' }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Status Badge --}}
                                    @if ($reg->status === 'pending')
                                        <span class="rounded-full bg-amber-50 border border-amber-200 px-2 py-0.5 text-[10px] font-bold text-amber-700 shrink-0">
                                            Pending
                                        </span>
                                    @elseif (in_array($reg->status, ['approved', 'confirmed']))
                                        <span class="rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[10px] font-bold text-emerald-700 shrink-0 flex items-center gap-1">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Approved</span>
                                        </span>
                                    @else
                                        <span class="rounded-full bg-rose-50 border border-rose-200 px-2 py-0.5 text-[10px] font-bold text-rose-700 shrink-0">
                                            Rejected
                                        </span>
                                    @endif
                                </div>

                                {{-- Event Details Strip --}}
                                <div class="rounded-xl bg-slate-50/70 border border-slate-200/70 p-2.5 space-y-1">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="text-[11px] font-bold text-slate-800 truncate" title="{{ $reg->event?->title }}">
                                            {{ $reg->event?->title ?: 'Event' }}
                                        </span>
                                        <span class="text-[10px] font-semibold text-slate-500 shrink-0">
                                            {{ $reg->event?->date ? $reg->event->date->format('M j, Y') : 'TBA' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between gap-1 pt-1 border-t border-slate-200/50">
                                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $reg->ticket_type === 'verified' ? 'text-emerald-700' : 'text-slate-600' }}">
                                            {{ $reg->ticket_type === 'verified' ? '⭐ Sabha Member' : 'Standard Pass' }}
                                        </span>
                                        @if ($reg->purchased_by_user_id)
                                            <span class="text-[9px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200/60 truncate" title="Purchased by {{ $reg->purchasedBy?->name }}">
                                                By {{ $reg->purchasedBy?->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Ticket Number & Payment Proof --}}
                                <div class="flex items-center justify-between gap-2 text-[11px] pt-1">
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">Ticket: </span>
                                        <span class="font-mono font-bold text-slate-900">{{ $reg->ticket_number ?: 'Pending' }}</span>
                                    </div>

                                    @if (has_media_file($reg->payment_screenshot) && media_url($reg->payment_screenshot))
                                        <button
                                            type="button"
                                            x-on:click="previewImage = {{ Illuminate\Support\Js::from(media_url($reg->payment_screenshot)) }}"
                                            class="inline-flex items-center gap-1 text-[11px] font-bold text-primary hover:underline cursor-pointer shrink-0"
                                        >
                                            <x-icon name="file-text" class="h-3 w-3" />
                                            <span>View Receipt ↗</span>
                                        </button>
                                    @else
                                        <span class="text-[10px] font-semibold text-slate-400 italic">No receipt</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Card Footer Actions --}}
                            <div class="pt-2.5 border-t border-slate-100">
                                @if ($reg->status === 'pending')
                                    <div class="grid grid-cols-2 gap-2">
                                        <button
                                            type="button"
                                            wire:click="approve({{ $reg->id }})"
                                            class="inline-flex items-center justify-center gap-1 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 py-1.5 px-3 text-xs font-bold text-white shadow-2xs hover:opacity-95 active:scale-[0.98] transition-all cursor-pointer"
                                        >
                                            <x-icon name="check-circle-2" class="h-3.5 w-3.5" />
                                            <span>Approve</span>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="openRejectModal({{ $reg->id }})"
                                            class="inline-flex items-center justify-center gap-1 rounded-xl border border-rose-200 bg-rose-50 py-1.5 px-3 text-xs font-bold text-rose-700 hover:bg-rose-100 active:scale-[0.98] transition-all cursor-pointer"
                                        >
                                            <x-icon name="x" class="h-3.5 w-3.5" />
                                            <span>Reject</span>
                                        </button>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        @if ($reg->status === 'rejected' && $reg->rejection_reason)
                                            <p class="text-[10px] text-rose-700 bg-rose-50 border border-rose-200/60 rounded-lg p-1.5 leading-relaxed font-medium">
                                                <strong>Reason:</strong> {{ $reg->rejection_reason }}
                                            </p>
                                        @endif

                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[11px] font-bold text-slate-500">
                                                {{ in_array($reg->status, ['approved', 'confirmed']) ? 'Ticket Active' : 'Request Closed' }}
                                            </span>

                                            @if (in_array($reg->status, ['approved', 'confirmed']))
                                                <button
                                                    type="button"
                                                    wire:click="toggleAttendance({{ $reg->id }})"
                                                    class="inline-flex items-center justify-center gap-1.5 rounded-xl border px-3 py-1 text-[11px] font-bold cursor-pointer transition-all active:scale-[0.98] {{ $reg->is_attended ? 'bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100 shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}"
                                                >
                                                    <span class="h-2 w-2 rounded-full {{ $reg->is_attended ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                                    <span>{{ $reg->is_attended ? 'Attended' : 'Mark Present' }}</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <x-pagination :paginator="$registrations" item-label="bookings" />
            @endif
        </div>
    </div>

    {{-- Payment Screenshot Modal Lightbox --}}
    <div
        x-show="previewImage"
        x-cloak
        class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4"
        x-on:click="previewImage = null"
    >
        <button
            type="button"
            x-on:click="previewImage = null"
            class="absolute top-4 right-4 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30 transition-all cursor-pointer shadow-lg"
        >
            <x-icon name="x" class="h-5 w-5" />
        </button>
        <div class="max-w-[92vw] max-h-[85vh] flex items-center justify-center" x-on:click.stop>
            <img :src="previewImage" alt="Payment proof" class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl border-2 border-white/20" />
        </div>
    </div>

    {{-- Rejection Reason Modal --}}
    @if ($rejectingId)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" wire:click="cancelReject"></div>
            <div class="relative w-full max-w-md bg-white rounded-2xl p-5 shadow-2xl border border-slate-200 z-10 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-900">Reject Booking Request</h3>
                    <button type="button" wire:click="cancelReject" class="text-slate-400 hover:text-slate-700">
                        <x-icon name="x" class="h-4 w-4" />
                    </button>
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase">Reason for Rejection <span class="text-red-500">*</span></label>
                    <textarea
                        wire:model="rejectionReason"
                        rows="3"
                        placeholder="e.g. Payment transaction reference not found / Incorrect amount paid..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 text-xs font-medium text-slate-900 outline-none focus:bg-white focus:border-primary"
                    ></textarea>
                    @error('rejectionReason') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" wire:click="cancelReject" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="button" wire:click="confirmReject" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700 shadow-sm">Confirm Rejection</button>
                </div>
            </div>
        </div>
    @endif

    {{-- QR Scanner & Manual Check-in Modal --}}
    @if ($isScannerOpen)
        <div
            x-data="{
                scanner: null,
                cameraActive: false,
                cameraError: '',
                initScanner() {
                    this.$nextTick(() => {
                        const el = document.getElementById('qr-reader');
                        if (!el || typeof window.Html5Qrcode === 'undefined') return;
                        
                        try {
                            this.scanner = new window.Html5Qrcode('qr-reader');
                            this.scanner.start(
                                { facingMode: 'environment' },
                                { fps: 10, qrbox: { width: 220, height: 220 } },
                                (decodedText) => {
                                    this.stopScanner().then(() => {
                                        @this.processCheckIn(decodedText);
                                    });
                                },
                                (err) => {}
                            ).then(() => {
                                this.cameraActive = true;
                            }).catch(err => {
                                this.cameraError = 'Camera not available. Please enter the ticket number below.';
                            });
                        } catch (e) {
                            this.cameraError = 'Could not start camera scanner.';
                        }
                    });
                },
                async stopScanner() {
                    if (this.scanner && this.cameraActive) {
                        try {
                            await this.scanner.stop();
                            this.cameraActive = false;
                        } catch (e) {}
                    }
                }
            }"
            x-init="initScanner()"
            x-on:destroy="stopScanner()"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 font-outfit"
        >
            <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden border border-slate-200 shadow-2xl flex flex-col">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50/80">
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-primary">
                            <x-icon name="qr-code" class="h-4 w-4" />
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Scan Ticket QR / Check-in</h3>
                    </div>
                    <button
                        type="button"
                        x-on:click="stopScanner(); $wire.closeScanner();"
                        class="rounded-xl p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 cursor-pointer"
                    >
                        <x-icon name="x" class="h-4 w-4" />
                    </button>
                </div>

                <div class="p-4 space-y-3.5 flex-1 overflow-y-auto">
                    @if ($scanSuccess !== null)
                        <div class="text-center py-4 space-y-2.5">
                            @if ($scanSuccess)
                                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 shadow-sm animate-bounce">
                                    <x-icon name="check-circle-2" class="h-6 w-6" />
                                </div>
                                <h4 class="text-sm font-bold text-slate-900">Check-in Verified!</h4>
                                <p class="text-xs text-emerald-800 bg-emerald-50 rounded-xl p-2.5 leading-relaxed font-semibold border border-emerald-200/60">{{ $scanMessage }}</p>
                            @else
                                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-rose-50 text-rose-600 border border-rose-200 shadow-sm">
                                    <x-icon name="x" class="h-6 w-6" />
                                </div>
                                <h4 class="text-sm font-bold text-slate-900">Verification Failed</h4>
                                <p class="text-xs text-rose-800 bg-rose-50 rounded-xl p-2.5 leading-relaxed font-semibold border border-rose-200/60">{{ $scanMessage }}</p>
                            @endif

                            <div class="pt-2 flex items-center gap-2 justify-center">
                                <button
                                    type="button"
                                    x-on:click="$wire.resetScan(); initScanner();"
                                    class="px-4 py-2 rounded-xl bg-primary text-xs font-bold text-white shadow-sm hover:opacity-95 cursor-pointer"
                                >
                                    Scan Next Ticket
                                </button>
                                <button
                                    type="button"
                                    x-on:click="stopScanner(); $wire.closeScanner();"
                                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 cursor-pointer"
                                >
                                    Done
                                </button>
                            </div>
                        </div>
                    @else
                        {{-- QR Scanner Viewport --}}
                        <div class="space-y-2">
                            <div
                                id="qr-reader"
                                wire:ignore
                                class="w-full aspect-square rounded-2xl overflow-hidden bg-slate-950 flex items-center justify-center border border-slate-800 relative"
                            >
                                <div x-show="!cameraActive && !cameraError" class="text-center p-4 text-slate-400 space-y-1">
                                    <x-icon name="loader-2" class="h-6 w-6 animate-spin mx-auto text-primary" />
                                    <p class="text-[11px] font-semibold">Starting camera...</p>
                                </div>
                                <div x-show="cameraError" x-cloak class="p-4 text-center text-xs text-amber-300 font-semibold leading-relaxed" x-text="cameraError"></div>
                            </div>
                            <p class="text-[10px] text-center text-slate-400 font-medium">Point camera at attendee's ticket QR code</p>
                        </div>

                        {{-- Manual Entry Option --}}
                        <form wire:submit="submitManualCheckIn" class="space-y-2 pt-2 border-t border-slate-100">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Or Enter Ticket Number Manually</label>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    wire:model="manualTicketNo"
                                    placeholder="e.g. 2026-SE2-9888"
                                    class="flex-1 rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-mono font-bold text-slate-900 outline-none focus:border-primary focus:bg-white"
                                />
                                <button
                                    type="submit"
                                    class="rounded-xl bg-primary px-3.5 py-2 text-xs font-bold text-white hover:opacity-95 active:scale-95 shadow-sm cursor-pointer"
                                >
                                    Verify
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
