<div class="space-y-4 font-outfit">
    {{-- Top Header & Add Sub-Admin Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">Sub-Admins</h1>
        </div>
        <a
            href="{{ route('admin.sub-admins.create') }}"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2 text-xs font-bold text-white transition-all shadow-sm shadow-primary/20 hover:opacity-95 active:scale-[0.98] cursor-pointer self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" />
            <span>Add Sub-Admin</span>
        </a>
    </div>

    @if ($successMsg)
        <div wire:key="success-{{ md5($successMsg) }}" x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 4000)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700 flex items-center gap-2">
            <x-icon name="check-circle-2" class="h-3.5 w-3.5 text-emerald-600" /> {{ $successMsg }}
        </div>
    @endif

    {{-- Search Toolbar Card --}}
    <div class="bg-white p-2.5 sm:p-3 rounded-xl border border-slate-200/90 shadow-2xs">
        <div class="relative max-w-md">
            <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search sub-admins by name or email..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 pl-9 pr-4 text-xs font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary shadow-2xs"
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
                        <a href="{{ route('admin.sub-admins.edit', $sa->id) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-primary-soft px-3 py-1.5 text-[12px] font-bold text-primary hover:opacity-80 cursor-pointer">
                            <x-icon name="key-round" class="h-3 w-3" /> Manage Permissions
                        </a>
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
