<div class="space-y-4">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold text-foreground">{{ __('site.profile.meetings.title') }}</h3>
            <p class="text-[12px] text-muted">{{ __('site.profile.meetings.subtitle') }}</p>
        </div>
        <a
            href="{{ route('profile.meetings.create') }}"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-3.5 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer shadow-sm shrink-0"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" /> {{ __('site.profile.meetings.log_btn') }}
        </a>
    </div>

    @if ($successMsg)
        <div x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 3000)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700 flex items-center gap-2">
            <x-icon name="check-circle-2" class="h-3.5 w-3.5 text-emerald-600" /> {{ $successMsg }}
        </div>
    @endif

    @if ($meetings->isEmpty())
        <div class="py-16 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic">
            {{ __('site.profile.meetings.empty') }}
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach ($meetings as $meeting)
                @php
                    $isMine = $meeting->user_id === auth()->id();
                    $otherUser = $isMine ? $meeting->withMember : $meeting->user;
                    $otherName = $otherUser?->name ?? 'Member';
                    $otherAvatar = $otherUser?->avatar ? media_url($otherUser->avatar) : null;
                @endphp
                <div class="rounded-2xl border border-border bg-white hover:border-primary/40 hover:shadow-sm transition-all duration-200 overflow-hidden flex flex-col justify-between group">
                    <div>
                        {{-- Top Image (if meeting photo uploaded) --}}
                        @if ($meeting->image)
                            <div class="relative w-full h-32 sm:h-36 bg-slate-950 overflow-hidden flex items-center justify-center">
                                <x-safe-image
                                    :src="media_url($meeting->image)"
                                    alt="Meeting photo"
                                    title="1-to-1 Meeting with {{ $otherName }}"
                                    :date="$meeting->meeting_at"
                                    :blur-backdrop="true"
                                    fallback-type="generic"
                                />
                            </div>
                        @endif

                        {{-- Card Content Body --}}
                        <div class="p-3 space-y-2">
                            {{-- Header with Participant --}}
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="h-8 w-8 rounded-lg overflow-hidden bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold text-xs shrink-0">
                                        @if ($otherAvatar)
                                            <img src="{{ $otherAvatar }}" alt="{{ $otherName }}" class="h-full w-full object-cover" />
                                        @else
                                            {{ mb_substr($otherName, 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-xs sm:text-sm font-bold text-foreground truncate group-hover:text-primary transition-colors">
                                            {{ $isMine ? __('site.profile.meetings.you_met') . ' ' . $otherName : $otherName . ' ' . __('site.profile.meetings.logged_with_you') }}
                                        </h4>
                                        <p class="text-[10px] text-muted font-medium flex items-center gap-1 truncate">
                                            <x-icon name="calendar" class="h-2.5 w-2.5 text-primary shrink-0" />
                                            <span>{{ $meeting->meeting_at?->format('M j, Y • g:i A') }}</span>
                                        </p>
                                    </div>
                                </div>

                                @if ($isMine)
                                    <div class="flex items-center gap-1 shrink-0">
                                        <a href="{{ route('profile.meetings.edit', $meeting->id) }}" class="h-6 w-6 rounded-md border border-amber-200 bg-amber-50 text-amber-700 flex items-center justify-center transition-all hover:bg-amber-100 active:scale-95 cursor-pointer" title="{{ __('site.profile.meetings.edit') }}">
                                            <x-icon name="pencil" class="h-2.5 w-2.5" />
                                        </a>
                                        <button wire:click="openDelete({{ $meeting->id }})" class="h-6 w-6 rounded-md border border-rose-200 bg-rose-50 text-rose-600 flex items-center justify-center transition-all hover:bg-rose-100 active:scale-95 cursor-pointer" title="{{ __('site.profile.meetings.delete') }}">
                                            <x-icon name="trash-2" class="h-2.5 w-2.5" />
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Location Box --}}
                            @if ($meeting->location)
                                <div class="rounded-lg border border-border/70 bg-surface/40 px-2 py-1 flex items-center gap-1.5 text-[11px] text-foreground font-medium truncate">
                                    <x-icon name="map-pin" class="h-2.5 w-2.5 text-primary shrink-0" />
                                    <span class="truncate">{{ $meeting->location }}</span>
                                </div>
                            @endif

                            {{-- Discussion Snippet --}}
                            @if ($meeting->points_of_discussion)
                                <div class="rounded-lg bg-slate-50 border border-slate-100 p-2">
                                    <p class="text-[9px] font-bold text-muted uppercase tracking-wider mb-0.5">Discussion Points</p>
                                    <p class="text-[11px] text-slate-700 leading-relaxed line-clamp-2">{{ $meeting->points_of_discussion }}</p>
                                </div>
                            @endif

                            @if ($meeting->comments)
                                <p class="text-[10px] text-muted italic line-clamp-1">
                                    Note: {{ $meeting->comments }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <x-pagination :paginator="$meetings" item-label="meetings" />
    @endif


    <x-admin.confirm-modal
        :show="$deletingId !== null"
        :title="__('site.profile.meetings.delete_title')"
        :message="__('site.profile.meetings.delete_msg')"
        :confirm-label="__('site.profile.meetings.delete_confirm')"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
