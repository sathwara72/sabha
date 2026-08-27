@php
    $inputClass = 'w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold';
    $labelClass = 'text-[12px] font-bold text-muted-foreground uppercase tracking-wide mb-0.5 block';
@endphp

<div class="bg-background font-outfit py-3 px-2">
    <div class="mx-auto max-w-lg space-y-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('profile', ['tab' => 'meetings']) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-border bg-white text-muted-foreground hover:text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-xs">
                <x-icon name="arrow-left" class="h-4 w-4" />
            </a>
            <h1 class="text-sm font-bold text-foreground flex items-center gap-2">
                <x-icon name="users" class="h-4.5 w-4.5 text-primary" /> {{ $meetingId ? __('site.profile.meetings.modal_title_edit') : __('site.profile.meetings.modal_title_add') }}
            </h1>
        </div>

        <form wire:submit="saveMeeting" class="glass-card p-6 space-y-4">
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
                    :wire-key="'meeting-member-' . ($meetingId ?? 'new')"
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
                <a href="{{ route('profile', ['tab' => 'meetings']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                    {{ __('site.profile.meetings.cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 disabled:opacity-50 transition-all cursor-pointer">
                    <span wire:loading.remove wire:target="saveMeeting">{{ $meetingId ? __('site.profile.meetings.save_edit_btn') : __('site.profile.meetings.save_btn') }}</span>
                    <span wire:loading wire:target="saveMeeting">{{ __('site.profile.meetings.saving') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
