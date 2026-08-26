<div class="space-y-5 font-outfit">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">1-to-1 Meetings</h1>
            <p class="text-xs text-muted">Oversight of member networking meetings logged across the platform</p>
        </div>
        <div class="text-xs font-bold text-slate-600 bg-slate-100 rounded-xl px-3 py-2 border border-slate-200 shrink-0">
            Total Meetings: <span class="text-primary font-black">{{ $totalCount }}</span>
        </div>
    </div>

    <div class="bg-white p-3 rounded-2xl border border-border shadow-xs">
        <div class="relative max-w-md">
            <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by member name or location..."
                class="w-full rounded-xl border border-border bg-slate-50/50 py-2 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary"
            />
        </div>
    </div>

    @if ($meetings->isEmpty())
        <div class="py-24 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic shadow-xs">
            {{ $search ? 'No meetings matching your search query.' : 'No meetings logged yet.' }}
        </div>
    @else
        <div class="bg-white rounded-2xl border border-border/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 border-b border-border/70">
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Logged By</th>
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">With Member</th>
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Date & Location</th>
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach ($meetings as $meeting)
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="px-5 py-3.5 text-xs font-extrabold text-slate-900">{{ $meeting->user?->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-xs font-semibold text-slate-700">{{ $meeting->withMember?->name ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-semibold text-slate-800">{{ $meeting->meeting_at?->format('M j, Y g:i A') }}</p>
                                    <p class="flex items-center gap-1 text-[12px] text-slate-400 mt-0.5">
                                        <x-icon name="map-pin" class="h-2.5 w-2.5" /> {{ $meeting->location }}
                                    </p>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <button
                                        wire:click="openDelete({{ $meeting->id }})"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 text-rose-600 transition-all hover:bg-rose-100 active:scale-95 cursor-pointer shadow-xs"
                                        title="Delete Meeting"
                                    >
                                        <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$meetings" item-label="meetings" />
        </div>
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
