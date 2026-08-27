<div class="space-y-5 font-outfit max-w-3xl">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.sub-admins.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-border bg-white text-muted-foreground hover:text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-xs">
            <x-icon name="arrow-left" class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">{{ $userId ? 'Manage Permissions' : 'Add Sub-Admin' }}</h1>
            <p class="text-xs text-muted">{{ $userId ? 'Permissions for ' . $subAdminUser?->name : 'Grant a member limited admin access to specific modules' }}</p>
        </div>
    </div>

    <form wire:submit="save" class="glass-card p-6 space-y-4">
        @if (! $userId)
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
        @endif

        <div>
            <label class="text-xs font-bold text-slate-700 mb-2 block">Module Permissions</label>
            <div class="overflow-x-auto rounded-xl border border-border/70">
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
                    <tbody class="divide-y divide-border/60 bg-white">
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
        </div>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <a href="{{ route('admin.sub-admins.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">Cancel</a>
            <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 disabled:opacity-50 transition-all cursor-pointer">
                <span wire:loading.remove wire:target="save">{{ $userId ? 'Save Permissions' : 'Add Sub-Admin' }}</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
