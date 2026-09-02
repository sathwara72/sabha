@php
    $inputClass = 'w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary';
    $labelClass = 'text-xs font-semibold text-foreground';
    $roleLabel = fn ($role) => match ($role) {
        'main_admin' => 'Main Admin',
        'admin' => 'Admin',
        default => 'Member',
    };
@endphp

<div class="space-y-3 w-full max-w-2xl">
    <div class="flex items-center gap-3">
        <a href="{{ route('chat.show', $groupId) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-border bg-white text-muted-foreground hover:text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-xs">
            <x-icon name="arrow-left" class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Group Settings</h1>
            <p class="text-xs text-muted">{{ $group->title }}</p>
        </div>
    </div>

    @if ($successMessage)
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700">{{ $successMessage }}</div>
    @endif
    @if ($errorMessage)
        <div class="rounded-xl bg-red-50 border border-red-100 p-3 text-xs font-semibold text-red-600">{{ $errorMessage }}</div>
    @endif

    {{-- Group Details --}}
    <div class="glass-card p-5 space-y-4">
        <h2 class="text-xs font-bold text-muted uppercase tracking-wide">Group Details</h2>

        @if ($isAdmin)
            <form wire:submit="saveDetails" class="space-y-3">
                <div class="flex items-center gap-4">
                    @if ($avatarFile)
                        <div class="relative h-16 w-16 rounded-full overflow-hidden border border-border bg-slate-50 group shrink-0">
                            <img src="{{ $avatarFile->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover" />
                            <button type="button" wire:click="removeAvatar" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-[10px] font-semibold cursor-pointer">Remove</button>
                        </div>
                    @elseif ($avatarPreview)
                        <div class="relative h-16 w-16 rounded-full overflow-hidden border border-border bg-slate-50 group shrink-0">
                            <img src="{{ $avatarPreview }}" alt="Preview" class="h-full w-full object-cover" />
                            <button type="button" wire:click="removeAvatar" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-[10px] font-semibold cursor-pointer">Remove</button>
                        </div>
                    @else
                        <label class="flex h-16 w-16 shrink-0 cursor-pointer flex-col items-center justify-center rounded-full border border-dashed border-border bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <x-icon name="upload" class="h-4 w-4 text-muted-foreground" />
                            <input type="file" wire:model="avatarFile" accept="image/*" class="hidden" />
                        </label>
                    @endif
                    <p class="text-[12px] text-muted-foreground">Group photo — PNG, JPG, WebP (max 5MB).</p>
                </div>

                <div class="space-y-1">
                    <label class="{{ $labelClass }}">Group Name</label>
                    <input type="text" wire:model="title" class="{{ $inputClass }}" />
                    @error('title') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="{{ $labelClass }}">Description</label>
                    <textarea wire:model="description" rows="2" class="{{ $inputClass }}"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="{{ $labelClass }}">Who can join?</label>
                    <select wire:model="joinSetting" class="{{ $inputClass }}">
                        <option value="direct_join">Anyone can join directly</option>
                        <option value="approval_required">Requires admin approval</option>
                    </select>
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:target="saveDetails" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-1.5 text-[12px] font-semibold text-white hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer shadow-sm">
                    Save Changes
                </button>
            </form>
        @else
            <div class="flex items-center gap-3">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary overflow-hidden">
                    @if ($avatarPreview)
                        <img src="{{ $avatarPreview }}" alt="" class="h-full w-full object-cover" />
                    @else
                        <x-icon name="users" class="h-5 w-5" />
                    @endif
                </div>
                <div>
                    <p class="text-sm font-bold text-foreground">{{ $group->title }}</p>
                    @if ($group->description)
                        <p class="text-xs text-muted-foreground mt-0.5">{{ $group->description }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Pending join requests --}}
    @if ($isAdmin && $pendingRequests->isNotEmpty())
        <div class="glass-card p-5 space-y-3">
            <h2 class="text-xs font-bold text-muted uppercase tracking-wide">Pending Requests ({{ $pendingRequests->count() }})</h2>
            <div class="space-y-2">
                @foreach ($pendingRequests as $req)
                    <div class="flex items-center gap-2.5 p-2 rounded-xl border border-border">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary font-bold text-xs">{{ mb_substr($req->user?->name ?? '?', 0, 1) }}</div>
                        <p class="text-xs font-semibold text-foreground flex-1 truncate">{{ $req->user?->name }}</p>
                        <button type="button" wire:click="approveRequest({{ $req->user_id }})" class="rounded-lg bg-emerald-500 px-2.5 py-1 text-[11px] font-bold text-white hover:bg-emerald-600 transition-colors cursor-pointer">Approve</button>
                        <button type="button" wire:click="rejectRequest({{ $req->user_id }})" class="rounded-lg bg-red-50 border border-red-100 px-2.5 py-1 text-[11px] font-bold text-red-600 hover:bg-red-100 transition-colors cursor-pointer">Reject</button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Members --}}
    <div class="glass-card p-5 space-y-3">
        <h2 class="text-xs font-bold text-muted uppercase tracking-wide">Members ({{ $members->count() }})</h2>
        <div class="space-y-1.5">
            @foreach ($members as $participant)
                @php $target = $participant->user; @endphp
                <div class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-surface transition-colors">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary font-bold text-xs overflow-hidden">
                        @if (media_url($target?->avatar))
                            <img src="{{ media_url($target->avatar) }}" alt="" class="h-full w-full object-cover" />
                        @else
                            {{ mb_substr($target?->name ?? '?', 0, 1) }}
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-foreground truncate">{{ $target?->name }}</p>
                        <span class="inline-block rounded-full px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $participant->role === 'main_admin' ? 'bg-amber-100 text-amber-700' : ($participant->role === 'admin' ? 'bg-primary-soft text-primary' : 'bg-slate-100 text-muted-foreground') }}">
                            {{ $roleLabel($participant->role) }}
                        </span>
                    </div>

                    @if ($isAdmin && $participant->user_id !== auth()->id() && $participant->role !== 'main_admin')
                        <div class="flex items-center gap-1 shrink-0">
                            @if ($participant->role === 'member')
                                <button type="button" wire:click="promoteMember({{ $participant->user_id }})" class="rounded-lg border border-border px-2 py-1 text-[10px] font-bold text-foreground hover:bg-slate-50 transition-colors cursor-pointer">Make Admin</button>
                            @else
                                <button type="button" wire:click="demoteMember({{ $participant->user_id }})" class="rounded-lg border border-border px-2 py-1 text-[10px] font-bold text-foreground hover:bg-slate-50 transition-colors cursor-pointer">Remove Admin</button>
                            @endif
                            @if ($myRole === 'main_admin')
                                <button type="button" x-on:click="if (confirm('Make {{ $target?->name }} the main group admin? You will become a regular admin.')) { $wire.transferOwnershipTo({{ $participant->user_id }}) }" class="rounded-lg border border-border px-2 py-1 text-[10px] font-bold text-foreground hover:bg-slate-50 transition-colors cursor-pointer">Make Main Admin</button>
                            @endif
                            <button type="button" x-on:click="if (confirm('Remove {{ $target?->name }} from the group?')) { $wire.removeMemberAction({{ $participant->user_id }}) }" class="rounded-lg border border-red-100 bg-red-50 px-2 py-1 text-[10px] font-bold text-red-600 hover:bg-red-100 transition-colors cursor-pointer">Remove</button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Add members --}}
    @if ($isAdmin)
        <div class="glass-card p-5 space-y-3">
            <h2 class="text-xs font-bold text-muted uppercase tracking-wide">Add Members</h2>
            <div class="relative">
                <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                <input type="text" wire:model.live.debounce.300ms="memberSearch" placeholder="Search by name or phone..." class="{{ $inputClass }} pl-9" />
            </div>

            @if (trim($memberSearch) !== '')
                <div class="rounded-xl border border-border divide-y divide-border/60 max-h-[160px] overflow-y-auto">
                    @forelse ($memberResults as $result)
                        <button type="button" wire:click="addSelectedMember({{ $result->id }})" class="w-full flex items-center gap-2.5 p-2 hover:bg-surface transition-colors cursor-pointer text-left">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary font-bold text-[11px]">{{ mb_substr($result->name, 0, 1) }}</div>
                            <span class="text-xs font-semibold text-foreground truncate">{{ $result->name }}</span>
                            <x-icon name="plus" class="h-3 w-3 text-primary ml-auto shrink-0" />
                        </button>
                    @empty
                        <p class="p-3 text-xs text-muted-foreground italic text-center">No members found.</p>
                    @endforelse
                </div>
            @endif

            @if ($selectedMembers->isNotEmpty())
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($selectedMembers as $member)
                        <span class="inline-flex items-center gap-1 rounded-full bg-primary-soft px-2.5 py-1 text-[11px] font-bold text-primary">
                            {{ $member->name }}
                            <button type="button" wire:click="removeSelectedMember({{ $member->id }})" class="hover:opacity-70 cursor-pointer">
                                <x-icon name="x" class="h-2.5 w-2.5" />
                            </button>
                        </span>
                    @endforeach
                </div>
                <button type="button" wire:click="submitAddMembers" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-1.5 text-[12px] font-semibold text-white hover:opacity-90 active:scale-[0.98] cursor-pointer shadow-sm">
                    Add to Group
                </button>
            @endif
        </div>
    @endif

    {{-- Danger zone --}}
    <div class="glass-card p-5 space-y-3 border border-red-100">
        <h2 class="text-xs font-bold text-red-500 uppercase tracking-wide">Danger Zone</h2>
        <div class="flex flex-wrap items-center gap-2.5">
            @if ($myRole === 'main_admin')
                <button type="button" disabled title="Transfer main admin to another member first" class="rounded-xl border border-border bg-slate-50 px-3.5 py-1.5 text-[12px] font-semibold text-muted-foreground cursor-not-allowed">
                    Leave Group
                </button>
                <button type="button" x-on:click="if (confirm('Delete this group for everyone? This cannot be undone.')) { $wire.deleteGroupAction() }" class="rounded-xl bg-red-600 px-3.5 py-1.5 text-[12px] font-semibold text-white hover:bg-red-700 transition-all cursor-pointer">
                    Delete Group
                </button>
            @else
                <button type="button" x-on:click="if (confirm('Leave this group?')) { $wire.leaveGroupAction() }" class="rounded-xl border border-red-200 bg-red-50 px-3.5 py-1.5 text-[12px] font-semibold text-red-600 hover:bg-red-100 transition-all cursor-pointer">
                    Leave Group
                </button>
            @endif
        </div>
    </div>
</div>
