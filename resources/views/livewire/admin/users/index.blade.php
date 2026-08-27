<div class="space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex flex-col">
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Members</h1>
                <span class="rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-bold text-primary">{{ $users->total() }}</span>
            </div>
            <p class="text-xs text-muted">Manage community members</p>
        </div>

        <div class="relative w-full sm:w-72">
            <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Search by name, email..."
                class="w-full rounded-xl border border-border bg-white py-1.5 pl-9 pr-4 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
            />
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-border/80 shadow-xs overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-border/70 backdrop-blur-sm">
                        <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Member</th>
                        <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Joined</th>
                        <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Role / Status</th>
                        <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60">
                    @foreach ($users as $user)
                        <tr class="transition-colors hover:bg-slate-50/70">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if (media_url($user->avatar))
                                        <img src="{{ media_url($user->avatar) }}" alt="{{ $user->name }}" class="h-9 w-9 rounded-xl object-contain bg-white shrink-0 border border-border/80 shadow-xs p-0.5" />
                                    @else
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft font-bold text-xs text-primary shadow-xs">
                                            {{ $user->name ? mb_substr($user->name, 0, 1) : '?' }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-xs font-extrabold text-slate-900 leading-tight">{{ $user->name }}</p>
                                        <p class="mt-0.5 flex items-center gap-1 text-[12px] font-medium text-slate-500">
                                            <x-icon name="mail" class="h-2.5 w-2.5 text-primary/70" /> {{ $user->email }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-col">
                                    <p class="text-xs font-semibold text-slate-800">{{ $user->created_at->format('n/j/Y') }}</p>
                                    <p class="flex items-center gap-1 text-[12px] text-slate-400 mt-0.5">
                                        <x-icon name="clock" class="h-2.5 w-2.5" /> {{ $user->created_at->format('g:i A') }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if ($user->role === 'admin')
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[12px] font-bold bg-primary-soft text-primary border border-primary/20">
                                            <x-icon name="shield-check" class="h-2.5 w-2.5" /> admin
                                        </span>
                                    @elseif ($user->role === 'sub_admin')
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[12px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-200">
                                            <x-icon name="key-round" class="h-2.5 w-2.5" /> sub-admin
                                        </span>
                                    @endif
                                    @if ($user->is_blocked)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-[12px] font-bold text-rose-600 border border-rose-200">
                                            <x-icon name="ban" class="h-2.5 w-2.5" /> Blocked
                                        </span>
                                    @endif
                                    <x-member-title-badge :title="$user->memberTitle" />
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($user->role !== 'admin')
                                        <button
                                            wire:click="openBlock({{ $user->id }})"
                                            class="inline-flex items-center justify-center gap-1 rounded-xl px-2.5 py-1.5 text-[12px] font-extrabold border transition-all active:scale-95 cursor-pointer shadow-xs {{ $user->is_blocked ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }}"
                                            title="{{ $user->is_blocked ? 'Unblock Member' : 'Block Member' }}"
                                        >
                                            @if ($user->is_blocked)
                                                <x-icon name="user-check" class="h-3 w-3" /> Unblock
                                            @else
                                                <x-icon name="ban" class="h-3 w-3" /> Block
                                            @endif
                                        </button>
                                        <button
                                            wire:click="openDelete({{ $user->id }})"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 text-rose-600 transition-all hover:bg-rose-100 hover:text-rose-700 active:scale-95 cursor-pointer shadow-xs"
                                            title="Delete Member"
                                        >
                                            <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                        </button>
                                    @endif
                                    <button
                                        wire:click="view({{ $user->id }})"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition-all hover:bg-slate-50 hover:text-slate-900 active:scale-95 cursor-pointer shadow-xs"
                                        title="View Member Details"
                                    >
                                        <x-icon name="arrow-up-right" class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($users->isEmpty())
            <div class="py-20 text-center">
                <p class="text-sm text-slate-500">No members found.</p>
            </div>
        @endif

        <x-pagination :paginator="$users" item-label="members" />
    </div>

    {{-- Member Detail Modal --}}
    @if ($selectedUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeView"></div>

            <div x-data x-show="true" x-transition class="relative w-full max-w-2xl transform rounded-2xl bg-white p-5 shadow-2xl transition-all border border-border">
                <div class="flex items-start justify-between border-b border-border pb-4 mb-4">
                    <div class="flex items-center gap-3">
                        @if (media_url($selectedUser->avatar))
                            <img src="{{ media_url($selectedUser->avatar) }}" alt="{{ $selectedUser->name }}" class="h-12 w-12 rounded-xl object-contain bg-white border border-border" />
                        @else
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-base font-bold text-primary">
                                {{ $selectedUser->name ? mb_substr($selectedUser->name, 0, 1) : '?' }}
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-foreground">{{ $selectedUser->name }}</h3>
                                @if ($selectedUser->role === 'admin')
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[12px] font-bold bg-primary-soft text-primary">admin</span>
                                @elseif ($selectedUser->role === 'sub_admin')
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[12px] font-bold bg-indigo-50 text-indigo-600">sub-admin</span>
                                @endif
                                @if ($selectedUser->is_blocked)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-[12px] font-bold text-rose-600 border border-rose-200">
                                        <x-icon name="ban" class="h-2.5 w-2.5" /> Blocked
                                    </span>
                                @endif
                                <x-member-title-badge :title="$selectedUser->memberTitle" />
                            </div>
                            <p class="text-xs text-muted-foreground mt-0.5">{{ $selectedUser->email }}</p>
                        </div>
                    </div>

                    <button wire:click="closeView" class="rounded-lg p-1 text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-h-[60vh] overflow-y-auto pr-1">
                    <div class="space-y-3.5">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Profile Details</h4>
                        <div class="space-y-2 text-xs">
                            <div>
                                <span class="text-muted-foreground block mb-0.5">Phone Number</span>
                                <span class="font-semibold text-foreground">{{ $selectedUser->phone ?: 'Not specified' }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground block mb-0.5">City</span>
                                <span class="font-semibold text-foreground">{{ $selectedUser->city ?: 'Not specified' }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground block mb-0.5">Native City (વતન)</span>
                                <span class="font-semibold text-foreground">{{ $selectedUser->native_city ?: 'Not specified' }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground block mb-0.5">Birth Date (જન્મ તારીખ)</span>
                                <span class="font-semibold text-foreground">{{ $selectedUser->birth_date ? $selectedUser->birth_date->format('Y-m-d') : 'Not specified' }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground block mb-0.5">Marriage / Anniversary Date</span>
                                <span class="font-semibold text-foreground">{{ $selectedUser->anniversary_date ? $selectedUser->anniversary_date->format('Y-m-d') : 'Not specified' }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground block mb-0.5">Residence Address</span>
                                <span class="font-semibold text-foreground">{{ $selectedUser->residence_address ?: 'Not specified' }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground block mb-0.5">Joined Date</span>
                                <span class="font-semibold text-foreground">{{ $selectedUser->created_at->format('n/j/Y g:i:s A') }}</span>
                            </div>
                            <div wire:key="title-{{ $selectedUser->id }}">
                                <span class="text-muted-foreground block mb-1">Member Title</span>
                                <select
                                    wire:change="assignTitle({{ $selectedUser->id }}, $event.target.value || null)"
                                    class="w-full rounded-lg border border-border bg-white px-2.5 py-1.5 text-xs font-semibold text-foreground outline-none focus:border-primary"
                                >
                                    <option value="" @selected(! $selectedUser->member_title_id)>No title assigned</option>
                                    @foreach ($memberTitles as $title)
                                        <option value="{{ $title->id }}" @selected($selectedUser->member_title_id === $title->id)>{{ $title->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3.5 border-t md:border-t-0 md:border-l border-border pt-4 md:pt-0 md:pl-5">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Registered Business</h4>

                        @if ($selectedUser->business)
                            @php $b = $selectedUser->business; @endphp
                            <div class="space-y-2 text-xs">
                                <div>
                                    <span class="text-muted-foreground block mb-0.5">Business Name</span>
                                    <span class="font-semibold text-foreground">{{ $b->name }}</span>
                                </div>
                                <div>
                                    <span class="text-muted-foreground block mb-0.5">Category</span>
                                    <span class="font-semibold text-foreground">{{ $b->category }}</span>
                                </div>
                                @if ($b->tagline)
                                    <div>
                                        <span class="text-muted-foreground block mb-0.5">Tagline</span>
                                        <span class="font-medium text-foreground">{{ $b->tagline }}</span>
                                    </div>
                                @endif
                                @php $addressLine = collect([$b->area, $b->state])->filter()->implode(', '); @endphp
                                @if ($addressLine)
                                    <div>
                                        <span class="text-muted-foreground block mb-0.5">Location</span>
                                        <span class="font-semibold text-foreground">{{ $addressLine }}</span>
                                    </div>
                                @endif
                                @if ($b->website)
                                    <div>
                                        <span class="text-muted-foreground block mb-0.5">Website</span>
                                        <a href="{{ $b->website }}" target="_blank" rel="noreferrer" class="font-semibold text-primary hover:underline inline-flex items-center gap-1">
                                            {{ $b->website }} <x-icon name="arrow-up-right" class="h-2.5 w-2.5" />
                                        </a>
                                    </div>
                                @endif
                                @if ($b->description)
                                    <div>
                                        <span class="text-muted-foreground block mb-0.5">Description</span>
                                        <p class="text-foreground leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">{{ $b->description }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-10 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                <span class="text-xs text-muted-foreground font-medium">No business profile registered.</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-between items-center border-t border-border pt-4 mt-4">
                    <div class="flex items-center gap-2">
                        @if ($selectedUser->role !== 'admin')
                            <button
                                wire:click="openBlock({{ $selectedUser->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border {{ $selectedUser->is_blocked ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }}"
                            >
                                @if ($selectedUser->is_blocked)
                                    <x-icon name="user-check" class="h-3.5 w-3.5" /> Unblock User
                                @else
                                    <x-icon name="ban" class="h-3.5 w-3.5" /> Block User
                                @endif
                            </button>
                            <button
                                wire:click="openDelete({{ $selectedUser->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border bg-rose-50 text-rose-600 border-rose-200 hover:bg-rose-100"
                            >
                                <x-icon name="trash-2" class="h-3.5 w-3.5" /> Delete Member
                            </button>
                        @endif
                    </div>
                    <button wire:click="closeView" class="rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground transition-colors hover:bg-slate-50 active:scale-95">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <x-admin.confirm-modal
        :show="$blockingUser !== null"
        :title="$blockingUser?->is_blocked ? 'Unblock Member' : 'Block Member'"
        :message="$blockingUser ? ('Are you sure you want to ' . ($blockingUser->is_blocked ? 'unblock' : 'block') . ' member &quot;' . $blockingUser->name . '&quot;? ' . ($blockingUser->is_blocked ? 'They will regain access to the platform.' : 'Their session will be terminated immediately.')) : ''"
        :confirm-label="$blockingUser?->is_blocked ? 'Unblock Member' : 'Block Member'"
        :variant="$blockingUser?->is_blocked ? 'success' : 'warning'"
        confirm-action="confirmToggleBlock"
        cancel-action="cancelBlock"
    />

    <x-admin.confirm-modal
        :show="$deletingUser !== null"
        title="Delete Member"
        :message="$deletingUser ? ('Are you sure you want to permanently delete member &quot;' . $deletingUser->name . '&quot; (' . $deletingUser->email . ')? This action cannot be undone.') : ''"
        confirm-label="Delete Member"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
