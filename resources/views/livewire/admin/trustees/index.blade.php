<div class="space-y-5 font-outfit">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Trustees</h1>
            <p class="text-xs text-muted">Select members to feature on the public Trustees page, with their business details shown automatically</p>
        </div>
        <a
            href="{{ route('admin.trustees.create') }}"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer shadow-sm self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" /> Add Trustee
        </a>
    </div>

    @if ($successMsg)
        <div wire:key="success-{{ md5($successMsg) }}" x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 3000)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700 flex items-center gap-2">
            <x-icon name="check-circle-2" class="h-3.5 w-3.5 text-emerald-600" /> {{ $successMsg }}
        </div>
    @endif

    <div class="bg-white p-3 rounded-2xl border border-border shadow-xs flex items-center justify-between gap-3">
        <div class="relative flex-1 max-w-md">
            <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by member name..." class="w-full rounded-xl border border-border bg-slate-50/50 py-2 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none focus:bg-white focus:border-primary" />
        </div>
        <div class="text-xs font-bold text-slate-600 bg-slate-100 rounded-xl px-3 py-2 border border-slate-200 shrink-0">
            Total: <span class="text-primary font-black">{{ $totalCount }}</span>
        </div>
    </div>

    @if ($trustees->isEmpty())
        <div class="py-24 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic shadow-xs">
            {{ $search ? 'No trustees matching your search.' : "No trustees added yet. Click 'Add Trustee' to feature a member on the public Trustees page." }}
        </div>
    @else
        <div class="bg-white rounded-2xl border border-border/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 border-b border-border/70">
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Member</th>
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Position</th>
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Business</th>
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach ($trustees as $trustee)
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-extrabold text-slate-900">{{ $trustee->user?->name ?? 'Deleted user' }}</p>
                                    <p class="text-[12px] text-slate-500">{{ $trustee->user?->email }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-xs font-bold text-primary">{{ $trustee->position }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-600">{{ $trustee->user?->business?->name ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[12px] font-bold {{ $trustee->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        {{ $trustee->is_active ? 'Visible' : 'Hidden' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.trustees.edit', $trustee->id) }}" class="h-7 w-7 rounded-xl border border-amber-200/80 bg-amber-50 text-amber-700 flex items-center justify-center transition-all hover:bg-amber-100 active:scale-[0.95] cursor-pointer" title="Edit">
                                            <x-icon name="pencil" class="h-3 w-3" />
                                        </a>
                                        <button wire:click="openDelete({{ $trustee->id }}, {{ Illuminate\Support\Js::from($trustee->user?->name ?? 'this trustee') }})" class="h-7 w-7 rounded-xl border border-rose-200/80 bg-rose-50 text-rose-600 flex items-center justify-center transition-all hover:bg-rose-100 active:scale-[0.95] cursor-pointer" title="Remove">
                                            <x-icon name="trash-2" class="h-3 w-3" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$trustees" item-label="trustees" />
        </div>
    @endif

    <x-admin.confirm-modal
        :show="$deletingId !== null"
        title="Remove Trustee"
        :message="'Are you sure you want to remove &quot;' . $deletingName . '&quot; from the trustees list?'"
        confirm-label="Remove"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
