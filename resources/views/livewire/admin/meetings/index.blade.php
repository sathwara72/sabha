<div class="space-y-4 font-outfit">
    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">1-to-1 Meetings</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Oversight of member networking meetings logged across the platform</p>
        </div>
        <div class="text-xs font-bold text-slate-700 bg-white rounded-xl px-3 py-1.5 border border-slate-200 shadow-2xs shrink-0 self-start sm:self-auto">
            Total Meetings: <span class="text-primary font-black ml-0.5">{{ $totalCount }}</span>
        </div>
    </div>

    {{-- Search Toolbar Card --}}
    <div class="bg-white p-2.5 sm:p-3 rounded-xl border border-slate-200/90 shadow-2xs">
        <div class="relative max-w-md">
            <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by member name or location..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 pl-9 pr-3 text-xs font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary shadow-2xs"
            />
        </div>
    </div>

    @if ($meetings->isEmpty())
        <div class="py-16 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic shadow-xs">
            {{ $search ? 'No meetings matching your search query.' : 'No meetings logged yet.' }}
        </div>
    @else
        <div class="bg-white rounded-2xl border border-border/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 border-b border-border/70">
                            <th class="px-3.5 py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Logged By</th>
                            <th class="px-3.5 py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">With Member</th>
                            <th class="px-3.5 py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Date & Location</th>
                            <th class="px-3.5 py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach ($meetings as $meeting)
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="px-3.5 py-2 text-xs font-extrabold text-slate-900">{{ $meeting->user?->name ?? '—' }}</td>
                                <td class="px-3.5 py-2 text-xs font-semibold text-slate-700">{{ $meeting->withMember?->name ?? '—' }}</td>
                                <td class="px-3.5 py-2">
                                    <p class="text-xs font-semibold text-slate-800">{{ $meeting->meeting_at?->format('M j, Y g:i A') }}</p>
                                    <p class="flex items-center gap-1 text-[11px] text-slate-400 mt-0.5">
                                        <x-icon name="map-pin" class="h-2.5 w-2.5" /> {{ $meeting->location }}
                                    </p>
                                </td>
                                <td class="px-3.5 py-2 text-right">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <button
                                            type="button"
                                            wire:click="openView({{ $meeting->id }})"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-600 transition-all hover:bg-slate-100 hover:text-primary hover:border-primary/40 active:scale-95 cursor-pointer shadow-2xs"
                                            title="View Full Meeting Details"
                                        >
                                            <x-icon name="eye" class="h-3.5 w-3.5" />
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="openDelete({{ $meeting->id }})"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 transition-all hover:bg-rose-100 active:scale-95 cursor-pointer shadow-2xs"
                                            title="Delete Meeting"
                                        >
                                            <x-icon name="trash-2" class="h-3 w-3" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$meetings" item-label="meetings" />
        </div>
    @endif

    {{-- View Meeting Details Modal (Compact & Clean) --}}
    @if ($viewingMeeting)
        <template x-teleport="body">
            <div
                x-data
                x-on:keydown.escape.window="$wire.closeView()"
                class="fixed inset-0 z-[99999] p-3 sm:p-4 flex min-h-full items-center justify-center font-outfit"
                role="dialog"
                aria-modal="true"
            >
                <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs transition-opacity" wire:click="closeView"></div>

                <div x-show="true" x-transition class="relative z-10 w-full max-w-lg rounded-2xl bg-white p-4 sm:p-5 shadow-2xl border border-slate-200 my-auto space-y-3">
                    {{-- Header --}}
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2.5">
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-primary border border-blue-100">
                                <x-icon name="users" class="h-4 w-4" />
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <h3 class="text-sm font-bold text-slate-900 leading-tight">1-to-1 Meeting Details</h3>
                                    <!-- <span class="rounded-full bg-blue-50 border border-blue-200/80 px-1.5 py-0.2 text-[9px] font-extrabold text-primary">
                                        #{{ $viewingMeeting->id }}
                                    </span> -->
                                </div>
                                <p class="text-[10px] text-slate-400 font-medium">
                                    Logged {{ $viewingMeeting->created_at?->format('M d, Y') }}
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="closeView"
                            class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 transition-colors cursor-pointer shrink-0"
                            aria-label="Close"
                        >
                            <x-icon name="x" class="h-3.5 w-3.5" />
                        </button>
                    </div>

                    {{-- Compact Participants Grid --}}
                    @php
                        $u1 = $viewingMeeting->user;
                        $u1Avatar = $u1?->avatar ? media_url($u1->avatar) : null;
                        $u1Biz = $u1?->business;

                        $u2 = $viewingMeeting->withMember;
                        $u2Avatar = $u2?->avatar ? media_url($u2->avatar) : null;
                        $u2Biz = $u2?->business;
                    @endphp
                    <div class="grid grid-cols-2 gap-2">
                        {{-- Host --}}
                        <div class="rounded-xl border border-blue-100 bg-blue-50/40 p-2.5 space-y-1.5">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-blue-700 bg-blue-100/80 px-1.5 py-0.5 rounded">Host</span>
                            <div class="flex items-start gap-2">
                                <div class="h-8 w-8 rounded-lg bg-white border border-blue-200 overflow-hidden flex items-center justify-center text-primary font-bold text-xs shrink-0 mt-0.5">
                                    @if ($u1Avatar)
                                        <img src="{{ $u1Avatar }}" alt="{{ $u1->name }}" class="h-full w-full object-cover" />
                                    @else
                                        {{ mb_substr($u1?->name ?? '?', 0, 1) }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1 space-y-0.5">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $u1?->name ?? '—' }}</p>
                                    @if ($u1?->phone)
                                        <p class="text-[10px] font-semibold text-slate-700 truncate flex items-center gap-1">
                                            <x-icon name="phone" class="h-2.5 w-2.5 text-primary shrink-0" />
                                            <a href="tel:{{ $u1->phone }}" class="hover:text-primary hover:underline">{{ $u1->phone }}</a>
                                        </p>
                                    @endif
                                    @if ($u1Biz?->name)
                                        <p class="text-[10px] text-slate-500 truncate" title="{{ $u1Biz->name }}">{{ $u1Biz->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- With Member --}}
                        <div class="rounded-xl border border-purple-100 bg-purple-50/40 p-2.5 space-y-1.5">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-purple-700 bg-purple-100/80 px-1.5 py-0.5 rounded">With</span>
                            <div class="flex items-start gap-2">
                                <div class="h-8 w-8 rounded-lg bg-white border border-purple-200 overflow-hidden flex items-center justify-center text-purple-700 font-bold text-xs shrink-0 mt-0.5">
                                    @if ($u2Avatar)
                                        <img src="{{ $u2Avatar }}" alt="{{ $u2->name }}" class="h-full w-full object-cover" />
                                    @else
                                        {{ mb_substr($u2?->name ?? '?', 0, 1) }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1 space-y-0.5">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $u2?->name ?? '—' }}</p>
                                    @if ($u2?->phone)
                                        <p class="text-[10px] font-semibold text-slate-700 truncate flex items-center gap-1">
                                            <x-icon name="phone" class="h-2.5 w-2.5 text-purple-600 shrink-0" />
                                            <a href="tel:{{ $u2->phone }}" class="hover:text-purple-600 hover:underline">{{ $u2->phone }}</a>
                                        </p>
                                    @endif
                                    @if ($u2Biz?->name)
                                        <p class="text-[10px] text-slate-500 truncate" title="{{ $u2Biz->name }}">{{ $u2Biz->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Compact Logistics (Date & Location) --}}
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-2 flex items-center gap-2">
                            <x-icon name="calendar" class="h-4 w-4 text-blue-600 shrink-0" />
                            <div class="min-w-0">
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Date & Time</p>
                                <p class="text-[11px] font-bold text-slate-800 truncate">
                                    {{ $viewingMeeting->meeting_at?->format('M d, Y • g:i A') ?? '—' }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-2 flex items-center gap-2">
                            <x-icon name="map-pin" class="h-4 w-4 text-emerald-600 shrink-0" />
                            <div class="min-w-0">
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Location</p>
                                <p class="text-[11px] font-bold text-slate-800 truncate" title="{{ $viewingMeeting->location }}">
                                    {{ $viewingMeeting->location ?: '—' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Discussion / Comments / Image --}}
                    @if ($viewingMeeting->points_of_discussion || $viewingMeeting->comments || $viewingMeeting->image)
                        <div class="space-y-2 rounded-xl border border-slate-100 bg-slate-50/60 p-2.5">
                            @if ($viewingMeeting->points_of_discussion)
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Discussion Points</p>
                                    <p class="text-xs text-slate-700 leading-snug mt-0.5 line-clamp-3">
                                        {{ $viewingMeeting->points_of_discussion }}
                                    </p>
                                </div>
                            @endif

                            @if ($viewingMeeting->comments)
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Comments / Notes</p>
                                    <p class="text-xs text-slate-700 leading-snug mt-0.5 line-clamp-2">
                                        {{ $viewingMeeting->comments }}
                                    </p>
                                </div>
                            @endif

                            @if ($viewingMeeting->image)
                                <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ media_url($viewingMeeting->image) }}" alt="Meeting" class="h-9 w-9 rounded-lg object-cover border border-slate-200" />
                                        <span class="text-[11px] font-semibold text-slate-600">Meeting Photo Attached</span>
                                    </div>
                                    <a
                                        href="{{ media_url($viewingMeeting->image) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-[11px] font-bold text-primary hover:underline inline-flex items-center gap-1"
                                    >
                                        <span>View Full</span>
                                        <x-icon name="external-link" class="h-3 w-3" />
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="flex items-center justify-end pt-1">
                        <button
                            type="button"
                            wire:click="closeView"
                            class="rounded-xl bg-slate-900 px-4 py-1.5 text-xs font-bold text-white hover:bg-slate-800 transition-colors cursor-pointer shadow-xs"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </template>
    @endif

    <x-admin.confirm-modal
        :show="$deletingId !== null"
        title="Delete Meeting"
        message="Are you sure you want to delete this meeting log? This cannot be undone."
        confirm-label="Delete"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
