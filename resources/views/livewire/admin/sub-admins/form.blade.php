<div class="space-y-4 font-outfit max-w-5xl mx-auto">
    {{-- Header Banner Card --}}
    <div class="glass-card p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a
                href="{{ route('admin.sub-admins.index') }}"
                class="flex h-9 w-9 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-900 hover:bg-slate-50 active:scale-95 transition-all cursor-pointer shadow-2xs shrink-0"
                title="Back to Sub-Admins"
            >
                <x-icon name="arrow-left" class="h-4 w-4" />
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    {{ $userId ? 'Manage Sub-Admin Permissions' : 'Add New Sub-Admin' }}
                </h1>
            </div>
        </div>

        {{-- Quick Bulk Actions --}}
        <div class="flex items-center gap-2 shrink-0">
            <button
                type="button"
                wire:click="selectAll"
                class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-100 active:scale-95 transition-all cursor-pointer shadow-2xs"
            >
                <x-icon name="check-check" class="h-3.5 w-3.5 text-emerald-600" />
                Select All
            </button>
            <button
                type="button"
                wire:click="clearAll"
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 active:scale-95 transition-all cursor-pointer shadow-2xs"
            >
                <x-icon name="x" class="h-3.5 w-3.5 text-slate-400" />
                Clear All
            </button>
        </div>
    </div>

    <form wire:submit="save" class="space-y-4">
        {{-- Member Selection Card (if new) --}}
        @if (! $userId)
            <div class="glass-card p-4 sm:p-5 space-y-2">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block">
                    Select Member to Promote <span class="text-rose-500">*</span>
                </label>
                <x-searchable-select
                    wire-model="newSubAdminUserId"
                    :options="$candidateOptions"
                    :value-map="$candidateValueMap"
                    :allow-custom="false"
                    placeholder="Search by member name or phone..."
                    leading-icon="user"
                    wire-key="add-subadmin-member"
                />
                @error('newSubAdminUserId')
                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-400">Only regular active members who are not already administrators are listed.</p>
            </div>
        @else
            {{-- Existing Sub-Admin Profile Card --}}
            <div class="glass-card p-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-[#00379D] to-[#082e6e] text-white font-black text-sm shadow-md">
                        {{ strtoupper(substr($subAdminUser->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">{{ $subAdminUser->name }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ $subAdminUser->email }} &middot; {{ $subAdminUser->phone ?: 'No phone' }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center rounded-full bg-blue-50 border border-blue-200 px-2.5 py-0.5 text-[11px] font-bold text-primary uppercase tracking-wide">
                    Sub-Admin Account
                </span>
            </div>
        @endif

        {{-- Module Permissions Matrix Card --}}
        <div class="glass-card p-4 sm:p-5 space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <div>
                    <h3 class="text-sm font-black text-slate-900">Module Access Control Matrix</h3>
                    <p class="text-xs text-slate-500">Click column or row titles to quickly toggle permissions</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-2xs">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 border-b border-slate-200">
                            <th class="px-4 py-3 text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Module
                            </th>
                            @foreach (['can_view' => 'View', 'can_add' => 'Add', 'can_edit' => 'Edit', 'can_delete' => 'Delete', 'can_approve' => 'Approve'] as $ability => $abilityLabel)
                                <th class="px-3 py-2.5 text-center">
                                    <button
                                        type="button"
                                        wire:click="toggleColumn('{{ $ability }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-slate-700 hover:bg-white hover:text-primary hover:shadow-2xs transition-all cursor-pointer"
                                        title="Click to toggle {{ $abilityLabel }} for all modules"
                                    >
                                        {{ $abilityLabel }}
                                        <x-icon name="arrow-down-up" class="h-2.5 w-2.5 text-slate-400" />
                                    </button>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($modules as $key => $label)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-4 py-2.5">
                                    <button
                                        type="button"
                                        wire:click="toggleRow('{{ $key }}')"
                                        class="text-left font-bold text-xs text-slate-900 hover:text-primary transition-colors cursor-pointer inline-flex items-center gap-1.5"
                                        title="Click to toggle all permissions for {{ $label }}"
                                    >
                                        <span>{{ $label }}</span>
                                        <span class="text-[10px] text-slate-400 font-normal hover:underline">(Toggle)</span>
                                    </button>
                                </td>
                                @foreach (['can_view', 'can_add', 'can_edit', 'can_delete', 'can_approve'] as $ability)
                                    <td class="px-3 py-2.5 text-center">
                                        <label class="inline-flex items-center justify-center cursor-pointer p-1">
                                            <input
                                                type="checkbox"
                                                wire:model="permissions.{{ $key }}.{{ $ability }}"
                                                class="h-4.5 w-4.5 rounded-lg border-slate-300 text-primary focus:ring-primary/30 transition-colors cursor-pointer"
                                            />
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form Actions Footer --}}
        <div class="flex items-center justify-end gap-2.5 pt-2">
            <a
                href="{{ route('admin.sub-admins.index') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-100 active:scale-98 transition-all cursor-pointer shadow-xs"
            >
                Cancel
            </a>
            <button
                type="submit"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-primary/20 hover:opacity-95 active:scale-98 transition-all cursor-pointer"
            >
                <span wire:loading.remove wire:target="save">
                    <x-icon name="check" class="h-3.5 w-3.5 inline mr-1" />
                    {{ $userId ? 'Save Permissions' : 'Create Sub-Admin' }}
                </span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
