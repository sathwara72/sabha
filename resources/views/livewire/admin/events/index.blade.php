<div class="space-y-4 font-outfit w-full">
    {{-- Header & Create Event Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-200/80">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">Events & Summits</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Manage, monitor, and publish community summits, workshops, and business mixers</p>
        </div>
        <a
            href="{{ route('admin.events.create') }}"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2 text-xs font-bold text-white shadow-sm shadow-primary/20 transition-all hover:opacity-95 active:scale-[0.98] cursor-pointer self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" />
            <span>Create Event</span>
        </a>
    </div>

    {{-- Events Table Card --}}
    <div class="space-y-3.5">
        <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80">
                            <th class="px-4 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Event Details</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Date & Location</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Normal Price</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Verified Price</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($events as $evt)
                            <tr class="transition-colors hover:bg-slate-50/60 group">
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="text-xs font-bold text-slate-900 group-hover:text-primary transition-colors">{{ $evt->title }}</p>
                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                            <span class="inline-block rounded-full bg-blue-50 border border-blue-200/60 px-2 py-0.5 text-[10px] font-bold text-primary">
                                                {{ $evt->type }}
                                            </span>
                                            @if ($evt->is_popup)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200/80 px-2 py-0.5 text-[10px] font-bold text-amber-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                    <span>Homepage Popup</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs font-semibold text-slate-800">{{ $evt->date->format('M j, Y') }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5 font-medium">{{ $evt->location }}</p>
                                    @php
                                        $bStatus = $evt->bookingStatus();
                                    @endphp
                                    @if ($bStatus === 'upcoming' && $evt->booking_start_date)
                                        <p class="text-[10px] font-semibold mt-1 flex items-center gap-1 text-amber-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            <span>Booking Starts: {{ $evt->booking_start_date->format('M j, Y') }}</span>
                                        </p>
                                    @elseif ($bStatus === 'open')
                                        <p class="text-[10px] font-semibold mt-1 flex items-center gap-1 text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Booking Ends: {{ $evt->booking_end_date ? $evt->booking_end_date->format('M j, Y') : ($evt->date ? $evt->date->format('M j, Y') : 'Event Date') }}</span>
                                        </p>
                                    @elseif ($bStatus === 'closed')
                                        <p class="text-[10px] font-semibold mt-1 flex items-center gap-1 text-slate-500">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            <span>Booking Closed</span>
                                        </p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-bold text-xs text-slate-800">
                                    {{ format_price($evt->price_normal) }}
                                </td>
                                <td class="px-4 py-3 font-bold text-xs text-primary">
                                    {{ format_price($evt->price_verified) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a
                                            href="{{ route('admin.events.show', $evt->id) }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 border border-blue-200/60 text-primary hover:bg-primary hover:text-white transition-all cursor-pointer shadow-2xs active:scale-95"
                                            title="View Event Details & Bookings"
                                        >
                                            <x-icon name="eye" class="h-3.5 w-3.5" />
                                        </a>
                                        <button
                                            type="button"
                                            wire:click="togglePopup({{ $evt->id }})"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-xl {{ $evt->is_popup ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-50 border border-slate-200 text-slate-500 hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200' }} transition-all cursor-pointer shadow-2xs active:scale-95"
                                            title="{{ $evt->is_popup ? 'Active Homepage Popup (Click to Disable)' : 'Set as Homepage Popup' }}"
                                        >
                                            <x-icon name="image" class="h-3.5 w-3.5" />
                                        </button>
                                        <a
                                            href="{{ route('admin.events.edit', $evt->id) }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all cursor-pointer shadow-2xs active:scale-95"
                                            title="Edit Event"
                                        >
                                            <x-icon name="pencil" class="h-3.5 w-3.5" />
                                        </a>
                                        <button
                                            type="button"
                                            wire:click="openDelete({{ $evt->id }}, {{ Illuminate\Support\Js::from($evt->title) }})"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-rose-50 border border-rose-200/60 text-rose-600 hover:bg-rose-600 hover:text-white transition-all cursor-pointer shadow-2xs active:scale-95"
                                            title="Delete Event"
                                        >
                                            <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-xs text-slate-500 font-medium">
                                    No registered events found. Click "+ Create Event" above to publish your first event.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <x-pagination :paginator="$events" item-label="events" />
    </div>

    {{-- Delete Confirmation Modal --}}
    <x-admin.confirm-modal
        :show="$deletingId !== null"
        title="Delete Event Profile"
        :message="'Are you sure you want to delete event &quot;' . $deletingTitle . '&quot;? All associated bookings will be permanently removed.'"
        confirm-label="Delete Event"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
