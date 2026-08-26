<div class="space-y-5 font-outfit">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Sub-Admins</h1>
            <p class="text-xs text-muted">Grant members limited admin access to specific modules without making them a full admin</p>
        </div>
        <button
            wire:click="openAdd"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer shadow-sm self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" /> Add Sub-Admin
        </button>
    </div>

    @if ($successMsg)
        <div wire:key="success-{{ md5($successMsg) }}" x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 4000)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700 flex items-center gap-2">
            <x-icon name="check-circle-2" class="h-3.5 w-3.5 text-emerald-600" /> {{ $successMsg }}
        </div>
    @endif

    <div class="bg-white p-3 rounded-2xl border border-border shadow-xs">
        <div class="relative max-w-md">
            <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search sub-admins by name or email..."
                class="w-full rounded-xl border border-border bg-slate-50/50 py-2 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary"
            />
        </div>
    </div>

    @if ($subAdmins->isEmpty())
        <div class="py-24 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic shadow-xs">
            {{ $search ? 'No sub-admins matching your search query.' : "No sub-admins yet. Click 'Add Sub-Admin' to delegate limited admin access to a member." }}
        </div>
    @else
        <div class="space-y-3.5">
            @foreach ($subAdmins as $sa)
                <div class="p-4 rounded-2xl border border-slate-200/80 bg-white flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft font-bold text-xs text-primary">
                            {{ $sa->name ? mb_substr($sa->name, 0, 1) : '?' }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-extrabold text-slate-900">{{ $sa->name }}</h3>
                            <p class="text-[12px] font-medium text-slate-500">{{ $sa->email }} &middot; {{ $sa->sub_admin_permissions_count }} module{{ $sa->sub_admin_permissions_count === 1 ? '' : 's' }} configured</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button wire:click="manage({{ $sa->id }})" class="inline-flex items-center gap-1.5 rounded-xl bg-primary-soft px-3 py-1.5 text-[12px] font-bold text-primary hover:opacity-80 cursor-pointer">
                            <x-icon name="key-round" class="h-3 w-3" /> Manage Permissions
                        </button>
                        <button wire:click="openRevoke({{ $sa->id }})" class="h-8 w-8 rounded-xl border border-rose-200/80 bg-rose-50 text-rose-600 flex items-center justify-center transition-all hover:bg-rose-100 active:scale-[0.95] cursor-pointer" title="Revoke Sub-Admin Access">
                            <x-icon name="trash-2" class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-2xl border border-border shadow-xs">
            <x-pagination :paginator="$subAdmins" item-label="sub-admins" />
        </div>
    @endif

    {{-- Add Sub-Admin Modal --}}
    @if ($isAddModalOpen)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm" wire:click="cancelAdd"></div>
            <div x-data x-show="true" x-transition class="relative z-50 w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <x-icon name="key-round" class="h-4.5 w-4.5 text-primary" /> Add Sub-Admin
                    </h3>
                    <button wire:click="cancelAdd" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                <form wire:submit="addSubAdmin" class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-700 mb-1 block">Select Member</label>
                        <x-searchable-select
                            wire-model="newSubAdminUserId"
                            :options="$candidateOptions"
                            :value-map="$candidateValueMap"
                            :allow-custom="false"
                            placeholder="Search by name or phone..."
                            leading-icon="user"
                            wire-key="add-subadmin-member"
                        />
                        @error('newSubAdminUserId')
                            <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="text-[12px] text-slate-500">After adding, you'll set exactly which modules and actions they can access.</p>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="cancelAdd" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 disabled:opacity-50 transition-all cursor-pointer">
                            <span wire:loading.remove wire:target="addSubAdmin">Continue to Permissions</span>
                            <span wire:loading wire:target="addSubAdmin">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Manage Permissions Modal --}}
    @if ($managingUser)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm" wire:click="closeManage"></div>
            <div x-data x-show="true" x-transition class="relative z-50 w-full max-w-3xl bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4 max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <x-icon name="key-round" class="h-4.5 w-4.5 text-primary" /> Permissions for {{ $managingUser->name }}
                    </h3>
                    <button wire:click="closeManage" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/90 border-b border-border/70">
                                <th class="px-3 py-2.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Module</th>
                                <th class="px-3 py-2.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider text-center">View</th>
                                <th class="px-3 py-2.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider text-center">Add</th>
                                <th class="px-3 py-2.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider text-center">Edit</th>
                                <th class="px-3 py-2.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider text-center">Delete</th>
                                <th class="px-3 py-2.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider text-center">Approve</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            @foreach ($modules as $key => $label)
                                <tr>
                                    <td class="px-3 py-2 text-xs font-bold text-slate-800">{{ $label }}</td>
                                    @foreach (['can_view', 'can_add', 'can_edit', 'can_delete', 'can_approve'] as $ability)
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox" wire:model="permissions.{{ $key }}.{{ $ability }}" class="h-4 w-4 rounded border-border text-primary focus:ring-primary/30 cursor-pointer" />
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="closeManage" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">Cancel</button>
                    <button wire:click="savePermissions" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 disabled:opacity-50 transition-all cursor-pointer">
                        <span wire:loading.remove wire:target="savePermissions">Save Permissions</span>
                        <span wire:loading wire:target="savePermissions">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <x-admin.confirm-modal
        :show="$revokingId !== null"
        title="Revoke Sub-Admin Access"
        message="This member will lose all admin access immediately. This cannot be undone (permissions would need to be re-configured if re-added)."
        confirm-label="Revoke Access"
        variant="danger"
        confirm-action="confirmRevoke"
        cancel-action="cancelRevoke"
    />
</div>
