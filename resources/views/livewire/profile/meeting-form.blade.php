@php
    $inputClass = 'w-full rounded-lg border border-border bg-white px-3 py-1.5 text-xs text-foreground outline-none transition-colors focus:border-primary font-medium placeholder:text-muted-foreground shadow-2xs';
    $labelClass = 'text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1 flex items-center gap-1';
@endphp

<div class="bg-background font-outfit min-h-[calc(100vh-140px)] py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-4">

        {{-- Page Header --}}
        <div class="flex items-center gap-3 pb-3 border-b border-border">
            <a
                href="{{ route('profile', ['tab' => 'meetings']) }}"
                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-border bg-white text-muted hover:text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-xs"
                title="{{ __('site.profile.meetings.cancel') }}"
            >
                <x-icon name="arrow-left" class="h-3.5 w-3.5" />
            </a>
            <div class="space-y-0.5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-primary-soft px-2 py-0.5 text-[10px] font-bold text-primary">
                        <x-icon name="users" class="h-2.5 w-2.5" /> {{ __('site.profile.meetings.title') }}
                    </span>
                </div>
                <h1 class="text-base sm:text-lg font-bold tracking-tight text-foreground">
                    {{ $meetingId ? __('site.profile.meetings.modal_title_edit') : __('site.profile.meetings.modal_title_add') }}
                </h1>
            </div>
        </div>

        {{-- Single Card Compact Form with Photo on Left & Inputs on Right --}}
        <form wire:submit="saveMeeting" x-on:keydown.enter="$event.target.tagName !== 'TEXTAREA' && $event.preventDefault()" class="glass-card p-4 sm:p-5 space-y-4 border border-border/80 rounded-2xl bg-white shadow-2xs">
            <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100">
                <span class="h-3.5 w-[3px] rounded-full bg-primary"></span>
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">Meeting Details</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

                {{-- Left Column: Photo Upload Box --}}
                <div class="lg:col-span-4 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <label class="{{ $labelClass }} mb-0">
                            <x-icon name="camera" class="h-3 w-3 text-primary" />
                            <span>{{ __('site.profile.meetings.photo_label') }}</span>
                        </label>
                        <span class="text-[10px] font-semibold text-muted">Optional</span>
                    </div>

                    @if ($meetingImageFile)
                        <div class="relative rounded-xl overflow-hidden border border-border bg-slate-900 group aspect-video max-h-44 flex items-center justify-center shadow-inner">
                            <img src="{{ $meetingImageFile->temporaryUrl() }}" class="w-full h-full object-contain" />
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <button
                                    type="button"
                                    wire:click="removePhoto"
                                    class="p-1.5 rounded-lg bg-rose-600 text-white hover:bg-rose-700 transition-all shadow-md cursor-pointer"
                                    title="Remove Photo"
                                >
                                    <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </div>
                    @elseif ($meetingImagePreview)
                        <div class="relative rounded-xl overflow-hidden border border-border bg-slate-900 group aspect-video max-h-44 flex items-center justify-center shadow-inner">
                            <img src="{{ $meetingImagePreview }}" class="w-full h-full object-contain" />
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <button
                                    type="button"
                                    wire:click="removePhoto"
                                    class="p-1.5 rounded-lg bg-rose-600 text-white hover:bg-rose-700 transition-all shadow-md cursor-pointer"
                                    title="Remove Photo"
                                >
                                    <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </div>
                    @else
                        <label class="group relative flex flex-col items-center justify-center border-2 border-dashed border-border hover:border-primary/60 rounded-xl p-4 bg-slate-50/70 hover:bg-primary-soft/20 transition-all cursor-pointer min-h-[150px] text-center">
                            <input
                                type="file"
                                accept="image/*"
                                wire:model="meetingImageFile"
                                class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                            />
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white border border-border shadow-xs text-primary group-hover:scale-110 transition-transform">
                                <x-icon name="camera" class="h-4 w-4" />
                            </div>
                            <p class="mt-2 text-xs font-bold text-foreground">{{ __('site.profile.meetings.photo_hint') }}</p>
                            <p class="mt-0.5 text-[10px] text-muted font-medium">PNG, JPG, WEBP up to 5MB</p>
                            <div wire:loading wire:target="meetingImageFile" class="mt-1 text-[11px] font-semibold text-primary">
                                Uploading photo...
                            </div>
                        </label>
                    @endif

                    @error('meetingImageFile')
                        <p class="mt-1 text-[11px] font-semibold text-rose-600 flex items-center gap-1">
                            <x-icon name="alert-circle" class="h-3 w-3" /> {{ $message }}
                        </p>
                    @enderror

                    <div class="rounded-xl border border-slate-200/80 bg-slate-50 p-2.5 text-[10px] text-muted leading-relaxed font-medium">
                        💡 <span class="font-bold text-slate-700">Tip:</span> Uploading a meeting photo builds credibility across the SABHA network.
                    </div>
                </div>

                {{-- Right Column: Form Inputs --}}
                <div class="lg:col-span-8 space-y-3">
                    {{-- Member Selection --}}
                    <div>
                        <label class="{{ $labelClass }}">
                            <x-icon name="user" class="h-3 w-3 text-primary" />
                            <span>{{ __('site.profile.meetings.member_label') }}</span>
                            <span class="text-rose-500">*</span>
                        </label>
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
                            <p class="mt-1 text-[11px] font-semibold text-rose-600 flex items-center gap-1">
                                <x-icon name="alert-circle" class="h-3 w-3" /> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Date and Location Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="{{ $labelClass }}">
                                <x-icon name="calendar" class="h-3 w-3 text-primary" />
                                <span>{{ __('site.profile.meetings.date_label') }}</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="datetime-local"
                                wire:model="meetingDate"
                                class="{{ $inputClass }}"
                            />
                            @error('meetingDate')
                                <p class="mt-1 text-[11px] font-semibold text-rose-600 flex items-center gap-1">
                                    <x-icon name="alert-circle" class="h-3 w-3" /> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="{{ $labelClass }}">
                                <x-icon name="map-pin" class="h-3 w-3 text-primary" />
                                <span>{{ __('site.profile.meetings.location_label') }}</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="meetingLocation"
                                placeholder="{{ __('site.profile.meetings.location_placeholder') }}"
                                class="{{ $inputClass }}"
                            />
                            @error('meetingLocation')
                                <p class="mt-1 text-[11px] font-semibold text-rose-600 flex items-center gap-1">
                                    <x-icon name="alert-circle" class="h-3 w-3" /> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Points of Discussion --}}
                    <div>
                        <label class="{{ $labelClass }}">
                            <x-icon name="file-text" class="h-3 w-3 text-primary" />
                            <span>{{ __('site.profile.meetings.discussion_label') }}</span>
                        </label>
                        <textarea
                            rows="2"
                            wire:model="meetingDiscussion"
                            placeholder="{{ __('site.profile.meetings.discussion_placeholder') }}"
                            class="{{ $inputClass }} resize-none leading-relaxed"
                        ></textarea>
                    </div>

                    {{-- Additional Notes & Comments --}}
                    <div>
                        <label class="{{ $labelClass }}">
                            <x-icon name="message-square" class="h-3 w-3 text-primary" />
                            <span>{{ __('site.profile.meetings.comments_label') }}</span>
                        </label>
                        <textarea
                            rows="2"
                            wire:model="meetingComments"
                            placeholder="{{ __('site.profile.meetings.comments_placeholder') }}"
                            class="{{ $inputClass }} resize-none leading-relaxed"
                        ></textarea>
                    </div>
                </div>

            </div>

            {{-- Single Action Button Row (The ONLY button set on the page) --}}
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <a
                    href="{{ route('profile', ['tab' => 'meetings']) }}"
                    class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-lg border border-border bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all cursor-pointer shadow-2xs"
                >
                    {{ __('site.profile.meetings.cancel') }}
                </a>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-1.5 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 disabled:opacity-50 transition-all cursor-pointer"
                >
                    <x-icon name="check" class="h-3.5 w-3.5" wire:loading.remove wire:target="saveMeeting" />
                    <span wire:loading.remove wire:target="saveMeeting">{{ $meetingId ? __('site.profile.meetings.save_edit_btn') : __('site.profile.meetings.save_btn') }}</span>
                    <span wire:loading wire:target="saveMeeting" class="flex items-center gap-1">
                        <svg class="h-3 w-3 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        {{ __('site.profile.meetings.saving') }}
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

