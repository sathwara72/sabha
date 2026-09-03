@php
    $inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 shadow-2xs';
    $labelClass = 'block text-[11px] font-bold text-slate-700 uppercase tracking-wide';
    $roleLabel = fn ($role) => match ($role) {
        'main_admin' => __('site.chat.role_main_admin'),
        'admin' => __('site.chat.role_admin'),
        default => __('site.chat.role_member'),
    };
@endphp

<div class="space-y-5 w-full">
    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/80 pb-4">
        <div class="flex items-center gap-3 min-w-0">
            <a
                href="{{ route('chat.show', $groupId) }}"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-primary hover:border-primary/40 hover:bg-slate-50 transition-all cursor-pointer shadow-2xs shrink-0"
            >
                <x-icon name="arrow-left" class="h-4 w-4" />
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="h-3 w-1 rounded-full bg-primary"></span>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">
                        {{ __('site.chat.settings_title') }}
                    </h1>
                </div>
                <p class="text-xs text-slate-500 font-medium truncate mt-0.5 ml-3">{{ $group->title }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto ml-12 sm:ml-0">
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-blue-50 border border-blue-200 px-3 py-1 text-xs font-bold text-primary shadow-2xs">
                <x-icon name="users" class="h-3.5 w-3.5" />
                <span>{{ $members->count() }} {{ $members->count() === 1 ? __('site.chat.member_single') : __('site.chat.members_plural') }}</span>
            </span>
        </div>
    </div>

    @if ($successMessage)
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3.5 text-xs font-bold text-emerald-700 shadow-2xs flex items-center gap-2">
            <x-icon name="check-circle-2" class="h-4 w-4 shrink-0 text-emerald-600" />
            <span>{{ $successMessage }}</span>
        </div>
    @endif
    @if ($errorMessage)
        <div class="rounded-xl bg-red-50 border border-red-100 p-3.5 text-xs font-bold text-red-600 shadow-2xs flex items-center gap-2">
            <x-icon name="alert-circle" class="h-4 w-4 shrink-0 text-red-600" />
            <span>{{ $errorMessage }}</span>
        </div>
    @endif

    {{-- Main 2-Column Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        {{-- ===== Left Column: Group Details, Add Members & Danger Zone (7 cols) ===== --}}
        <div class="lg:col-span-7 space-y-5">
            {{-- Group Details Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-primary">
                        <x-icon name="info" class="h-3.5 w-3.5" />
                    </div>
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">{{ __('site.chat.group_details') }}</h2>
                </div>

                @if ($isAdmin)
                    <form wire:submit="saveDetails" class="space-y-4">
                        {{-- Group Photo Uploader --}}
                        <div class="space-y-1">
                            <label class="{{ $labelClass }}">{{ __('site.chat.group_photo') }}</label>
                            <div class="flex items-center gap-3.5 p-3 rounded-xl border border-slate-100 bg-slate-50/60">
                                @if ($avatarFile)
                                    <div class="relative h-14 w-14 rounded-xl overflow-hidden border border-primary/40 bg-slate-900 group shrink-0 shadow-2xs">
                                        <img src="{{ $avatarFile->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover" />
                                        <button
                                            type="button"
                                            wire:click="removeAvatar"
                                            class="absolute inset-0 bg-slate-950/65 backdrop-blur-xs flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-[10px] font-bold cursor-pointer gap-0.5"
                                        >
                                            <x-icon name="trash-2" class="h-3 w-3 text-rose-400" />
                                            <span>{{ __('site.chat.remove') }}</span>
                                        </button>
                                    </div>
                                @elseif ($avatarPreview)
                                    <div class="relative h-14 w-14 rounded-xl overflow-hidden border border-slate-200 bg-slate-900 group shrink-0 shadow-2xs">
                                        <img src="{{ $avatarPreview }}" alt="Preview" class="h-full w-full object-cover" />
                                        <button
                                            type="button"
                                            wire:click="removeAvatar"
                                            class="absolute inset-0 bg-slate-950/65 backdrop-blur-xs flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-[10px] font-bold cursor-pointer gap-0.5"
                                        >
                                            <x-icon name="trash-2" class="h-3 w-3 text-rose-400" />
                                            <span>{{ __('site.chat.remove') }}</span>
                                        </button>
                                    </div>
                                @else
                                    <label class="flex h-14 w-14 shrink-0 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-white hover:bg-blue-50/50 hover:border-primary/50 transition-all group shadow-2xs">
                                        <x-icon name="camera" class="h-4 w-4 text-slate-400 group-hover:text-primary transition-colors" />
                                        <span class="text-[9px] font-bold text-slate-400 group-hover:text-primary mt-0.5">Photo</span>
                                        <input type="file" wire:model="avatarFile" accept="image/*" class="hidden" />
                                    </label>
                                @endif
                                <div class="text-xs text-slate-500 min-w-0">
                                    <p class="font-bold text-slate-800 text-xs">{{ __('site.chat.group_photo') }}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5 leading-tight">{{ __('site.chat.photo_hint') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="space-y-1 sm:col-span-2">
                                <label class="{{ $labelClass }}">{{ __('site.chat.group_name') }}</label>
                                <input type="text" wire:model="title" class="{{ $inputClass }}" />
                                @error('title') <p class="text-[11px] text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1 sm:col-span-2">
                                <label class="{{ $labelClass }}">{{ __('site.chat.description') }}</label>
                                <textarea wire:model="description" rows="2" class="{{ $inputClass }} resize-none"></textarea>
                            </div>

                            <div class="space-y-1 sm:col-span-2">
                                <label class="{{ $labelClass }}">{{ __('site.chat.who_can_join') }}</label>
                                <select wire:model="joinSetting" class="{{ $inputClass }}">
                                    <option value="direct_join">{{ __('site.chat.direct_join') }}</option>
                                    <option value="approval_required">{{ __('site.chat.approval_required') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-2.5 border-t border-slate-100 flex justify-end">
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="saveDetails"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-5 py-2 text-xs font-bold text-white hover:bg-primary-dark active:scale-[0.98] disabled:opacity-60 cursor-pointer shadow-sm transition-all"
                            >
                                <span wire:loading.remove wire:target="saveDetails">{{ __('site.chat.save_changes') }}</span>
                                <span wire:loading wire:target="saveDetails" class="inline-flex items-center gap-1.5">
                                    <x-icon name="loader-2" class="h-3.5 w-3.5 animate-spin" />
                                    <span>{{ __('site.chat.save_changes') }}...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="flex items-center gap-3.5 p-3 rounded-xl border border-slate-100 bg-slate-50/60">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-primary overflow-hidden border border-blue-100 shadow-2xs">
                            @if ($avatarPreview)
                                <img src="{{ $avatarPreview }}" alt="" class="h-full w-full object-cover" />
                            @else
                                <x-icon name="users" class="h-5 w-5" />
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-900 truncate">{{ $group->title }}</p>
                            @if ($group->description)
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2 leading-relaxed">{{ $group->description }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Add Members Section (Admin only) --}}
            @if ($isAdmin)
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-3.5">
                    <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-primary">
                            <x-icon name="user-plus" class="h-3.5 w-3.5" />
                        </div>
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">{{ __('site.chat.add_members') }}</h2>
                    </div>

                    <div class="relative">
                        <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                        <input type="text" wire:model.live.debounce.300ms="memberSearch" placeholder="{{ __('site.chat.search_members') }}" class="{{ $inputClass }} pl-9" />
                    </div>

                    @if (trim($memberSearch) !== '')
                        <div class="rounded-xl border border-slate-200 divide-y divide-slate-100 max-h-[180px] overflow-y-auto bg-white shadow-lg">
                            @forelse ($memberResults as $result)
                                <button type="button" wire:click="addSelectedMember({{ $result->id }})" class="w-full flex items-center justify-between gap-2.5 p-2.5 hover:bg-slate-50 transition-colors cursor-pointer text-left">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-primary font-bold text-xs">{{ mb_substr($result->name, 0, 1) }}</div>
                                        <span class="text-xs font-bold text-slate-900 truncate">{{ $result->name }}</span>
                                    </div>
                                    <x-icon name="plus" class="h-3.5 w-3.5 text-primary ml-auto shrink-0" />
                                </button>
                            @empty
                                <p class="p-3 text-xs text-slate-400 italic text-center">{{ __('site.chat.no_members_found') }}</p>
                            @endforelse
                        </div>
                    @endif

                    @if ($selectedMembers->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach ($selectedMembers as $member)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 border border-blue-200/80 px-2.5 py-1 text-xs font-bold text-primary shadow-2xs">
                                    {{ $member->name }}
                                    <button type="button" wire:click="removeSelectedMember({{ $member->id }})" class="hover:opacity-70 cursor-pointer">
                                        <x-icon name="x" class="h-3 w-3" />
                                    </button>
                                </span>
                            @endforeach
                        </div>
                        <button
                            type="button"
                            wire:click="submitAddMembers"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:bg-primary-dark active:scale-[0.98] cursor-pointer shadow-sm transition-all"
                        >
                            <x-icon name="user-plus" class="h-3.5 w-3.5" />
                            <span>{{ __('site.chat.add_to_group') }}</span>
                        </button>
                    @endif
                </div>
            @endif

            {{-- Danger Zone --}}
            <div class="bg-white rounded-2xl border border-red-200/90 p-5 sm:p-6 shadow-xs space-y-3.5">
                <div class="flex items-center gap-2 pb-2.5 border-b border-red-100">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-50 text-red-600">
                        <x-icon name="alert-triangle" class="h-3.5 w-3.5" />
                    </div>
                    <h2 class="text-xs font-bold text-red-600 uppercase tracking-wider">{{ __('site.chat.danger_zone') }}</h2>
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    @if ($myRole === 'main_admin')
                        <button type="button" disabled title="Transfer main admin to another member first" class="rounded-xl border border-slate-200 bg-slate-100 px-4 py-2 text-xs font-bold text-slate-400 cursor-not-allowed">
                            {{ __('site.chat.leave_group') }}
                        </button>
                        <button
                            type="button"
                            x-on:click="if (confirm('Delete this group for everyone? This cannot be undone.')) { $wire.deleteGroupAction() }"
                            class="rounded-xl bg-gradient-to-r from-rose-600 to-red-700 px-4 py-2 text-xs font-bold text-white hover:opacity-95 transition-all cursor-pointer shadow-sm shadow-rose-600/20"
                        >
                            {{ __('site.chat.delete_group') }}
                        </button>
                    @else
                        <button
                            type="button"
                            x-on:click="if (confirm('Leave this group?')) { $wire.leaveGroupAction() }"
                            class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-100 transition-all cursor-pointer"
                        >
                            {{ __('site.chat.leave_group') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== Right Column: Pending Requests & Group Members List (5 cols) ===== --}}
        <div class="lg:col-span-5 space-y-5">
            {{-- Pending Join Requests --}}
            @if ($isAdmin && $pendingRequests->isNotEmpty())
                <div class="bg-white rounded-2xl border border-amber-200 p-5 sm:p-6 shadow-xs space-y-3">
                    <div class="flex items-center gap-2 pb-2.5 border-b border-amber-100">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <x-icon name="clock" class="h-3.5 w-3.5" />
                        </div>
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            {{ __('site.chat.pending_requests') }} ({{ $pendingRequests->count() }})
                        </h2>
                    </div>
                    <div class="space-y-2 max-h-[260px] overflow-y-auto pr-1">
                        @foreach ($pendingRequests as $req)
                            <div class="flex items-center justify-between gap-2.5 p-2.5 rounded-xl border border-slate-200 bg-slate-50/60">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-primary font-black text-xs border border-blue-100">
                                        {{ mb_substr($req->user?->name ?? '?', 0, 1) }}
                                    </div>
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $req->user?->name }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button
                                        type="button"
                                        wire:click="approveRequest({{ $req->user_id }})"
                                        class="rounded-lg bg-emerald-600 px-2.5 py-1 text-[11px] font-bold text-white hover:bg-emerald-700 transition-colors cursor-pointer shadow-2xs"
                                    >
                                        {{ __('site.chat.approve') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="rejectRequest({{ $req->user_id }})"
                                        class="rounded-lg bg-red-50 border border-red-100 px-2.5 py-1 text-[11px] font-bold text-red-600 hover:bg-red-100 transition-colors cursor-pointer"
                                    >
                                        {{ __('site.chat.reject') }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Group Members List Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-3.5">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-primary">
                            <x-icon name="users" class="h-3.5 w-3.5" />
                        </div>
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            {{ __('site.chat.members') }} ({{ $members->count() }})
                        </h2>
                    </div>
                </div>

                <div class="space-y-2 max-h-[480px] overflow-y-auto pr-1 divide-y divide-slate-100">
                    @foreach ($members as $participant)
                        @php $target = $participant->user; @endphp
                        <div class="pt-2 first:pt-0 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-primary font-bold text-xs overflow-hidden border border-blue-100 shadow-2xs">
                                        @if (media_url($target?->avatar))
                                            <img src="{{ media_url($target->avatar) }}" alt="" class="h-full w-full object-cover" />
                                        @else
                                            {{ mb_substr($target?->name ?? '?', 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 truncate">{{ $target?->name }}</p>
                                        <span class="inline-block rounded-full px-2 py-0.2 text-[9px] font-bold uppercase tracking-wider {{ $participant->role === 'main_admin' ? 'bg-amber-100 text-amber-800' : ($participant->role === 'admin' ? 'bg-blue-100 text-primary' : 'bg-slate-100 text-slate-600') }}">
                                            {{ $roleLabel($participant->role) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if ($isAdmin && $participant->user_id !== auth()->id() && $participant->role !== 'main_admin')
                                <div class="flex items-center gap-1.5 pl-10 flex-wrap">
                                    @if ($participant->role === 'member')
                                        <button type="button" wire:click="promoteMember({{ $participant->user_id }})" class="rounded-lg border border-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">{{ __('site.chat.make_admin') }}</button>
                                    @else
                                        <button type="button" wire:click="demoteMember({{ $participant->user_id }})" class="rounded-lg border border-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">{{ __('site.chat.remove_admin') }}</button>
                                    @endif
                                    @if ($myRole === 'main_admin')
                                        <button type="button" x-on:click="if (confirm('Make {{ $target?->name }} the main group admin? You will become a regular admin.')) { $wire.transferOwnershipTo({{ $participant->user_id }}) }" class="rounded-lg border border-blue-200 bg-blue-50/50 px-2 py-0.5 text-[10px] font-bold text-primary hover:bg-blue-100 transition-colors cursor-pointer">{{ __('site.chat.make_main_admin') }}</button>
                                    @endif
                                    <button type="button" x-on:click="if (confirm('Remove {{ $target?->name }} from the group?')) { $wire.removeMemberAction({{ $participant->user_id }}) }" class="rounded-lg border border-red-100 bg-red-50 px-2 py-0.5 text-[10px] font-bold text-red-600 hover:bg-red-100 transition-colors cursor-pointer">{{ __('site.chat.remove') }}</button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
