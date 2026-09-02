<div class="space-y-4 font-outfit">
    {{-- Top Header Card --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs">
        <div class="flex flex-col">
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Trustees</h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-primary border border-blue-200/60">
                    {{ $totalCount }} {{ Str::plural('Trustee', $totalCount) }}
                </span>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Select members to feature on the public Trustees page with their business details shown automatically</p>
        </div>
        <button
            type="button"
            wire:click="openCreate"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2.5 text-xs font-bold text-white transition-all shadow-md shadow-primary/20 hover:opacity-95 active:scale-[0.98] cursor-pointer self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-4 w-4" />
            <span>Add Trustee</span>
        </button>
    </div>

    @if ($successMsg)
        <div wire:key="success-{{ md5($successMsg) }}" x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 3500)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-800 flex items-center gap-2 shadow-2xs">
            <x-icon name="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0" />
            <span>{{ $successMsg }}</span>
        </div>
    @endif

    {{-- Search Filter Card --}}
    <div class="bg-white p-3 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between gap-3">
        <div class="relative flex-1 max-w-md">
            <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 pointer-events-none" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by member name, email..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-2 pl-10 pr-9 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors placeholder:text-slate-400"
            />
            @if ($search)
                <button
                    type="button"
                    wire:click="$set('search', '')"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer"
                >
                    <x-icon name="x" class="h-3.5 w-3.5" />
                </button>
            @endif
        </div>
        <div class="text-xs font-bold text-slate-600 bg-slate-50 rounded-xl px-3.5 py-2 border border-slate-200 shrink-0">
            Total: <span class="text-primary font-black">{{ $totalCount }}</span>
        </div>
    </div>

    {{-- Table or Empty State --}}
    @if ($trustees->isEmpty())
        <div class="py-20 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-slate-200 italic shadow-2xs">
            {{ $search ? 'No trustees matching your search.' : "No trustees added yet. Click 'Add Trustee' to feature a member on the public Trustees page." }}
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 border-b border-slate-200/80">
                            <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Member</th>
                            <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Position</th>
                            <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Business</th>
                            <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($trustees as $trustee)
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @if ($trustee->user?->avatar && media_url($trustee->user->avatar))
                                            <img src="{{ media_url($trustee->user->avatar) }}" alt="{{ $trustee->user->name }}" class="h-9 w-9 rounded-xl object-contain bg-white shrink-0 border border-slate-200 p-0.5 shadow-2xs" />
                                        @else
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-primary font-black text-xs border border-blue-100 shadow-2xs">
                                                {{ $trustee->user?->name ? strtoupper(substr($trustee->user->name, 0, 1)) : '?' }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-xs font-extrabold text-slate-900 leading-tight">{{ $trustee->user?->name ?? 'Deleted user' }}</p>
                                            <p class="text-[11px] text-slate-500 font-medium">{{ $trustee->user?->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-primary border border-blue-100">
                                        {{ $trustee->position }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-700 font-medium">
                                    {{ $trustee->user?->business?->name ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $trustee->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $trustee->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $trustee->is_active ? 'Visible' : 'Hidden' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button
                                            type="button"
                                            wire:click="openEdit({{ $trustee->id }})"
                                            class="h-8 w-8 rounded-xl border border-amber-200 bg-amber-50 text-amber-700 flex items-center justify-center transition-all hover:bg-amber-100 active:scale-[0.95] cursor-pointer shadow-2xs"
                                            title="Edit Trustee"
                                        >
                                            <x-icon name="pencil" class="h-3.5 w-3.5" />
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="openDelete({{ $trustee->id }}, {{ Illuminate\Support\Js::from($trustee->user?->name ?? 'this trustee') }})"
                                            class="h-8 w-8 rounded-xl border border-rose-200 bg-rose-50 text-rose-600 flex items-center justify-center transition-all hover:bg-rose-100 active:scale-[0.95] cursor-pointer shadow-2xs"
                                            title="Remove Trustee"
                                        >
                                            <x-icon name="trash-2" class="h-3.5 w-3.5" />
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

    {{-- In-Page Modal: Add / Edit Trustee Teleported to <body> --}}
    @if ($isFormModalOpen)
        <template x-teleport="body">
            <div
                class="fixed inset-0 z-[99999] overflow-y-auto p-4 sm:p-6 flex min-h-full items-center justify-center font-outfit"
                x-on:keydown.escape.window="$wire.closeFormModal()"
            >
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" wire:click="closeFormModal"></div>

                <div class="relative w-full max-w-md bg-white rounded-3xl p-5 sm:p-6 shadow-2xl border border-slate-200 my-auto z-10 flex flex-col space-y-4">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-blue-50 text-primary border border-blue-100 shadow-2xs">
                                <x-icon name="award" class="h-4.5 w-4.5" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">{{ $editingId ? 'Edit Trustee' : 'Add New Trustee' }}</h3>
                                <p class="text-[11px] text-slate-500 font-medium">{{ $editingId ? 'Update trustee details and position' : 'Select a member to feature on the Trustees page' }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="closeFormModal"
                            class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer shadow-2xs"
                            title="Close"
                        >
                            <x-icon name="x" class="h-4 w-4" />
                        </button>
                    </div>

                    {{-- Modal Form --}}
                    <form wire:submit="saveTrustee" class="space-y-4">
                        @if ($editingId)
                            <div>
                                <label class="text-xs font-bold text-slate-700 mb-1 block">Selected Member</label>
                                <div class="flex items-center gap-3 w-full rounded-2xl border border-slate-200 bg-slate-50/80 p-3 shadow-2xs">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary text-white font-black text-xs shadow-2xs">
                                        {{ $editingTrustee?->user?->name ? strtoupper(substr($editingTrustee->user->name, 0, 1)) : '?' }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 truncate leading-tight">{{ $editingTrustee?->user?->name ?? 'Deleted user' }}</p>
                                        <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $editingTrustee?->user?->email }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div>
                                <label class="text-xs font-bold text-slate-700 mb-1 block">Select Member <span class="text-rose-500">*</span></label>
                                <select
                                    wire:model="userId"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 px-3.5 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors cursor-pointer"
                                >
                                    <option value="">-- Choose a member --</option>
                                    @foreach ($candidateUsers as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                                @error('userId') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="text-xs font-bold text-slate-700 mb-1 block">Position / Designation <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                wire:model="position"
                                placeholder="e.g. President, Vice President, Trustee, Secretary"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 px-3.5 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors"
                            />
                            @error('position') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        @if ($editingId)
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label class="text-xs font-bold text-slate-700 mb-1 block">Display Order</label>
                                    <input
                                        type="number"
                                        min="0"
                                        wire:model="sortOrder"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 px-3.5 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors"
                                    />
                                    @error('sortOrder') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div class="flex flex-col justify-end">
                                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs font-bold text-slate-700 cursor-pointer hover:bg-slate-100 transition-colors">
                                        <input
                                            type="checkbox"
                                            wire:model="isActive"
                                            class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4"
                                        />
                                        <span>Visible Publicly</span>
                                    </label>
                                </div>
                            </div>
                        @endif

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                            <button
                                type="button"
                                wire:click="closeFormModal"
                                class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-100 active:scale-[0.98] transition-all cursor-pointer shadow-xs"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-primary/20 hover:opacity-95 active:scale-[0.98] transition-all cursor-pointer"
                            >
                                <span wire:loading.remove wire:target="saveTrustee">{{ $editingId ? 'Save Changes' : 'Add Trustee' }}</span>
                                <span wire:loading wire:target="saveTrustee">{{ $editingId ? 'Saving...' : 'Adding...' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

    {{-- Delete Confirm Modal --}}
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
