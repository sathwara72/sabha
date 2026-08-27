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
        <div class="space-y-3">
            @foreach ($meetings as $meeting)
                @php $isMine = $meeting->user_id === auth()->id(); @endphp
                <div class="glass-card p-4 flex items-start gap-3">
                    @if ($meeting->image)
                        <img src="{{ media_url($meeting->image) }}" alt="Meeting photo" class="h-14 w-14 rounded-xl object-cover border border-border shrink-0" />
                    @else
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                            <x-icon name="users" class="h-5 w-5" />
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="text-xs font-bold text-foreground">
                                {{ $isMine ? __('site.profile.meetings.you_met') . ' ' . $meeting->withMember?->name : $meeting->user?->name . ' ' . __('site.profile.meetings.logged_with_you') }}
                            </h4>
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[12px] font-bold text-slate-600">
                                {{ $meeting->meeting_at?->format('M j, Y g:i A') }}
                            </span>
                        </div>
                        <p class="mt-1 flex items-center gap-1 text-[12px] text-muted-foreground">
                            <x-icon name="map-pin" class="h-2.5 w-2.5" /> {{ $meeting->location }}
                        </p>
                        @if ($meeting->points_of_discussion)
                            <p class="mt-1.5 text-xs text-foreground">{{ $meeting->points_of_discussion }}</p>
                        @endif
                        @if ($meeting->comments)
                            <p class="mt-1 text-[12px] text-muted-foreground italic">{{ $meeting->comments }}</p>
                        @endif
                    </div>
                    @if ($isMine)
                        <div class="flex items-center gap-1.5 shrink-0">
                            <a href="{{ route('profile.meetings.edit', $meeting->id) }}" class="h-7 w-7 rounded-xl border border-amber-200/80 bg-amber-50 text-amber-700 flex items-center justify-center transition-all hover:bg-amber-100 active:scale-[0.95] cursor-pointer" title="{{ __('site.profile.meetings.edit') }}">
                                <x-icon name="pencil" class="h-3 w-3" />
                            </a>
                            <button wire:click="openDelete({{ $meeting->id }})" class="h-7 w-7 rounded-xl border border-rose-200/80 bg-rose-50 text-rose-600 flex items-center justify-center transition-all hover:bg-rose-100 active:scale-[0.95] cursor-pointer" title="{{ __('site.profile.meetings.delete') }}">
                                <x-icon name="trash-2" class="h-3 w-3" />
                            </button>
                        </div>
                    @endif
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
