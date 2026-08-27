<div class="space-y-3 w-full">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Events</h1>
            <p class="text-xs text-muted">Manage and monitor community events</p>
        </div>
        <a
            href="{{ route('admin.events.create') }}"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer"
        >
            <x-icon name="plus-circle" class="h-4 w-4" />
            Create Event
        </a>
    </div>

    <div class="space-y-3 pt-1">
        <div class="glass-card overflow-hidden p-0">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface border-b border-border">
                        <th class="px-4 py-2.5 text-xs font-bold text-muted">Event Details</th>
                        <th class="px-4 py-2.5 text-xs font-bold text-muted">Date & Location</th>
                        <th class="px-4 py-2.5 text-xs font-bold text-muted">Normal Price</th>
                        <th class="px-4 py-2.5 text-xs font-bold text-muted">Verified Price</th>
                        <th class="px-4 py-2.5 text-xs font-bold text-muted text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($events as $evt)
                        <tr class="transition-colors hover:bg-surface/50">
                            <td class="px-4 py-2.5">
                                <div>
                                    <p class="text-xs font-bold text-foreground">{{ $evt->title }}</p>
                                    <span class="inline-block rounded-full bg-primary-soft px-2 py-0.5 text-[12px] font-bold text-primary mt-0.5">{{ $evt->type }}</span>
                                    @if ($evt->is_popup)
                                        <span class="inline-block rounded-full bg-amber-100 px-2 py-0.5 text-[12px] font-bold text-amber-700 mt-0.5 ml-1">Homepage Popup</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2.5">
                                <p class="text-xs font-semibold text-foreground">{{ $evt->date->format('M j, Y') }}</p>
                                <p class="text-[12px] text-muted-foreground mt-0.5">{{ $evt->location }}</p>
                            </td>
                            <td class="px-4 py-2.5 font-bold text-xs text-foreground">{{ $evt->price_normal ?: 'N/A' }}</td>
                            <td class="px-4 py-2.5 font-bold text-xs text-primary">{{ $evt->price_verified ?: 'N/A' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a
                                        href="{{ route('admin.events.show', $evt->id) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-primary-soft text-primary hover:bg-primary hover:text-white transition-all cursor-pointer shadow-xs active:scale-95"
                                        title="View Event Details & Members"
                                    >
                                        <x-icon name="eye" class="h-3.5 w-3.5" />
                                    </a>
                                    <button
                                        wire:click="togglePopup({{ $evt->id }})"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl {{ $evt->is_popup ? 'bg-amber-500 text-white' : 'bg-slate-50 border border-slate-200 text-slate-500 hover:bg-amber-500 hover:text-white' }} transition-all cursor-pointer shadow-xs active:scale-95"
                                        title="{{ $evt->is_popup ? 'Remove as homepage popup' : 'Set as homepage popup' }}"
                                    >
                                        <x-icon name="image" class="h-3.5 w-3.5" />
                                    </button>
                                    <a
                                        href="{{ route('admin.events.edit', $evt->id) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 border border-amber-200/60 text-amber-700 hover:bg-amber-500 hover:text-white transition-all cursor-pointer shadow-xs active:scale-95"
                                        title="Edit Event"
                                    >
                                        <x-icon name="pencil" class="h-3.5 w-3.5" />
                                    </a>
                                    <button
                                        wire:click="openDelete({{ $evt->id }}, {{ Illuminate\Support\Js::from($evt->title) }})"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-rose-50 border border-rose-200/60 text-rose-600 hover:bg-rose-600 hover:text-white transition-all cursor-pointer shadow-xs active:scale-95"
                                        title="Delete Event"
                                    >
                                        <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-xs text-muted font-medium">
                                No registered events found. Create one above to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$events" item-label="events" />
    </div>

    <x-admin.confirm-modal
        :show="$deletingId !== null"
        title="Delete Event Profile"
        :message="'Are you sure you want to delete event &quot;' . $deletingTitle . '&quot;? All associated data will be permanently removed.'"
        confirm-label="Delete Event"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
