@php
    $inputClass = 'w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold';
    $labelClass = 'text-[12px] font-bold text-muted-foreground uppercase tracking-wide mb-0.5 block';
@endphp

<div class="space-y-4">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold text-foreground">{{ __('site.profile.meetings.title') }}</h3>
            <p class="text-[12px] text-muted">{{ __('site.profile.meetings.subtitle') }}</p>
        </div>
        <button
            wire:click="openAdd"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-3.5 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer shadow-sm shrink-0"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" /> {{ __('site.profile.meetings.log_btn') }}
        </button>
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
                            <button wire:click="openEdit({{ $meeting->id }})" class="h-7 w-7 rounded-xl border border-amber-200/80 bg-amber-50 text-amber-700 flex items-center justify-center transition-all hover:bg-amber-100 active:scale-[0.95] cursor-pointer" title="{{ __('site.profile.meetings.edit') }}">
                                <x-icon name="pencil" class="h-3 w-3" />
                            </button>
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

    {{-- Add/Edit Meeting Modal --}}
    @if ($isFormModalOpen)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm" wire:click="cancelForm"></div>
            <div x-data x-show="true" x-transition class="relative z-50 w-full max-w-lg bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4 max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <x-icon name="users" class="h-4.5 w-4.5 text-primary" /> {{ $editingId ? __('site.profile.meetings.modal_title_edit') : __('site.profile.meetings.modal_title_add') }}
                    </h3>
                    <button wire:click="cancelForm" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                <form wire:submit="saveMeeting" class="space-y-4">
                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.meetings.member_label') }}</label>
                        <x-searchable-select
                            wire-model="meetingWithMemberId"
                            :options="$memberLabels"
                            :value-map="$memberValueMap"
                            :value="$editingMemberName"
                            :allow-custom="false"
                            :placeholder="__('site.profile.meetings.member_placeholder')"
                            leading-icon="user"
                            :wire-key="'meeting-member-' . ($editingId ?? 'new')"
                        />
                        @error('meetingWithMemberId')
                            <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.meetings.date_label') }}</label>
                        <input type="datetime-local" wire:model="meetingDate" class="{{ $inputClass }}" />
                        @error('meetingDate')
                            <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.meetings.location_label') }}</label>
                        <input type="text" wire:model="meetingLocation" placeholder="{{ __('site.profile.meetings.location_placeholder') }}" class="{{ $inputClass }}" />
                        @error('meetingLocation')
                            <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.meetings.discussion_label') }}</label>
                        <textarea rows="3" wire:model="meetingDiscussion" placeholder="{{ __('site.profile.meetings.discussion_placeholder') }}" class="{{ $inputClass }} resize-none"></textarea>
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.meetings.comments_label') }}</label>
                        <textarea rows="2" wire:model="meetingComments" placeholder="{{ __('site.profile.meetings.comments_placeholder') }}" class="{{ $inputClass }} resize-none"></textarea>
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.meetings.photo_label') }}</label>
                        <div class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-4 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative min-h-[100px]">
                            <input
                                type="file"
                                accept="image/*"
                                wire:model="meetingImageFile"
                                class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                            />
                            @if ($meetingImageFile)
                                <img src="{{ $meetingImageFile->temporaryUrl() }}" class="h-16 w-16 rounded-lg object-cover" />
                            @elseif ($meetingImagePreview)
                                <img src="{{ $meetingImagePreview }}" class="h-16 w-16 rounded-lg object-cover" />
                            @else
                                <x-icon name="camera" class="h-5 w-5 text-muted-foreground" />
                                <p class="mt-1 text-[12px] text-muted-foreground">{{ __('site.profile.meetings.photo_hint') }}</p>
                            @endif
                        </div>
                        @error('meetingImageFile')
                            <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="cancelForm" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            {{ __('site.profile.meetings.cancel') }}
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 disabled:opacity-50 transition-all cursor-pointer">
                            <span wire:loading.remove wire:target="saveMeeting">{{ $editingId ? __('site.profile.meetings.save_edit_btn') : __('site.profile.meetings.save_btn') }}</span>
                            <span wire:loading wire:target="saveMeeting">{{ __('site.profile.meetings.saving') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
