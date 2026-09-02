@php
    $inputClass = 'w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold';
    $labelClass = 'text-[12px] font-bold text-muted-foreground uppercase tracking-wide mb-0.5 block';

    $statusBadge = [
        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
        'in_progress' => 'bg-sky-50 text-sky-700 border-sky-200',
        'closed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'rejected' => 'bg-rose-50 text-rose-600 border-rose-200',
    ];
    $statusLabel = [
        'pending' => __('site.profile.referrals.status_pending'),
        'in_progress' => __('site.profile.referrals.status_in_progress'),
        'closed' => __('site.profile.referrals.status_closed'),
        'rejected' => __('site.profile.referrals.status_rejected'),
    ];
    $ratingBadge = [
        'hot' => 'bg-rose-50 text-rose-600 border-rose-200',
        'warm' => 'bg-amber-50 text-amber-700 border-amber-200',
        'cold' => 'bg-sky-50 text-sky-700 border-sky-200',
    ];
    $ratingLabel = [
        'hot' => __('site.profile.referrals.rating_hot'),
        'warm' => __('site.profile.referrals.rating_warm'),
        'cold' => __('site.profile.referrals.rating_cold'),
    ];
@endphp

<div class="space-y-4">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold text-foreground">{{ $direction === 'given' ? __('site.profile.referrals.title_given') : __('site.profile.referrals.title_received') }}</h3>
            <p class="text-[12px] text-muted">
                {{ $direction === 'given' ? __('site.profile.referrals.subtitle_given') : __('site.profile.referrals.subtitle_received') }}
            </p>
        </div>
        @if ($direction === 'given')
            <button
                wire:click="openGive"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-3.5 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer shadow-sm shrink-0"
            >
                <x-icon name="plus" class="h-3.5 w-3.5" /> {{ __('site.profile.referrals.give_btn') }}
            </button>
        @endif
    </div>

    @if ($successMsg)
        <div x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 3000)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700 flex items-center gap-2">
            <x-icon name="check-circle-2" class="h-3.5 w-3.5 text-emerald-600" /> {{ $successMsg }}
        </div>
    @endif

    @if ($referrals->isEmpty())
        <div class="py-16 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic">
            {{ $direction === 'given' ? __('site.profile.referrals.empty_given') : __('site.profile.referrals.empty_received') }}
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
            @foreach ($referrals as $ref)
                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-primary/30 transition-all flex flex-col justify-between space-y-3">
                    <div class="space-y-2.5">
                        {{-- Top: User & Badges --}}
                        <div class="border-b border-slate-100 pb-2.5 space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-xl bg-primary-soft text-primary border border-primary/10 flex items-center justify-center font-bold text-xs shrink-0">
                                    @php
                                        $memberName = $direction === 'given' ? ($ref->receiver?->name ?? 'Member') : ($ref->giver?->name ?? 'Member');
                                    @endphp
                                    {{ mb_substr($memberName, 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block leading-none mb-0.5">
                                        {{ $direction === 'given' ? __('site.profile.referrals.to') : __('site.profile.referrals.from') }}
                                    </span>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 truncate" title="{{ $memberName }}">
                                        {{ $memberName }}
                                    </h4>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $ratingBadge[$ref->lead_rating] ?? $ratingBadge['warm'] }}">{{ $ratingLabel[$ref->lead_rating] ?? $ref->lead_rating }}</span>
                                <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $statusBadge[$ref->status] ?? $statusBadge['pending'] }}">{{ $statusLabel[$ref->status] ?? $ref->status }}</span>
                            </div>
                        </div>

                        {{-- Lead Info --}}
                        <div class="rounded-xl bg-slate-50/80 p-2.5 space-y-1.5 border border-slate-100">
                            <div class="flex items-center justify-between text-xs font-semibold text-slate-850 gap-2">
                                <span class="truncate">{{ $ref->contact_name }}</span>
                                <span class="text-[11px] text-slate-500 shrink-0 font-medium">{{ $ref->contact_number }}</span>
                            </div>
                            @if ($ref->company_details)
                                <div class="flex items-center justify-between text-[11px] gap-2 pt-0.5 border-t border-slate-200/50">
                                    <span class="text-slate-500 font-medium shrink-0">{{ __('site.profile.referrals.company_label') }}</span>
                                    <span class="text-slate-800 font-semibold truncate text-right">{{ $ref->company_details }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Requirement --}}
                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed" title="{{ $ref->business_requirement }}">
                            {{ $ref->business_requirement }}
                        </p>

                        @if ($ref->giver_comments)
                            <p class="text-[11px] text-slate-400 italic line-clamp-1">"{{ $ref->giver_comments }}"</p>
                        @endif

                        @if ($ref->status === 'closed' && $ref->amount)
                            <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-[11px] font-bold text-emerald-700 flex items-center justify-between">
                                <span>{{ __('site.profile.referrals.closed_amount') }}:</span>
                                <span>₹{{ number_format((float) $ref->amount) }}</span>
                            </div>
                        @endif
                        @if ($ref->receiver_comments)
                            <p class="text-[11px] text-slate-500 line-clamp-1"><span class="font-bold text-slate-700">{{ __('site.profile.referrals.receiver_notes') }}:</span> {{ $ref->receiver_comments }}</p>
                        @endif
                    </div>

                    {{-- Bottom Footer --}}
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold {{ $ref->contact_status === 'connected' ? 'text-emerald-600' : 'text-slate-400' }}">
                            <x-icon name="{{ $ref->contact_status === 'connected' ? 'check-circle-2' : 'clock' }}" class="h-3 w-3" />
                            {{ $ref->contact_status === 'connected' ? __('site.profile.referrals.contacted') : __('site.profile.referrals.not_contacted') }}
                        </span>

                        @if ($direction === 'given' && $ref->status === 'pending')
                            <button wire:click="openDelete({{ $ref->id }})" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 hover:underline cursor-pointer flex items-center gap-1">
                                <x-icon name="trash-2" class="h-3 w-3" /> {{ __('site.profile.referrals.withdraw') }}
                            </button>
                        @elseif ($direction === 'received')
                            <button wire:click="openUpdate({{ $ref->id }})" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 border border-blue-100 px-2.5 py-1 text-[11px] font-bold text-primary hover:bg-blue-100 cursor-pointer shadow-2xs">
                                <x-icon name="pencil" class="h-2.5 w-2.5" /> {{ __('site.profile.referrals.update') }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <x-pagination :paginator="$referrals" item-label="referrals" :page-name="'page-' . $direction" />
    @endif

    {{-- Give Referral Modal --}}
    @if ($isGiveModalOpen)
        <template x-teleport="body">
            <div
                class="fixed inset-0 z-[99999] overflow-y-auto p-3 sm:p-4 flex min-h-full items-center justify-center font-outfit"
                x-on:keydown.escape.window="$wire.cancelGive()"
            >
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" wire:click="cancelGive"></div>

                <div class="relative z-10 w-full max-w-lg bg-white rounded-3xl p-5 sm:p-6 shadow-2xl border border-slate-200 my-auto flex flex-col space-y-4">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-soft text-primary border border-primary/10 shadow-2xs">
                                <x-icon name="send" class="h-4 w-4" />
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">{{ __('site.profile.referrals.modal_give_title') }}</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Pass a business opportunity to a member</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="cancelGive"
                            class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer shadow-2xs"
                            title="Close"
                        >
                            <x-icon name="x" class="h-3.5 w-3.5" />
                        </button>
                    </div>

                    <form wire:submit="giveReferral" class="space-y-3">
                        {{-- Row 1: Member --}}
                        <div>
                            <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">{{ __('site.profile.referrals.give_to_label') }} <span class="text-rose-500">*</span></label>
                            <x-searchable-select
                                wire-model="receiverMemberId"
                                :options="$memberLabels"
                                :value-map="$memberValueMap"
                                :allow-custom="false"
                                :placeholder="__('site.profile.meetings.member_placeholder')"
                                leading-icon="user"
                                wire-key="give-referral-member"
                            />
                            @error('receiverMemberId')
                                <p class="mt-0.5 text-[10px] font-semibold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Row 2: Contact Name & Phone --}}
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">{{ __('site.profile.referrals.contact_name_label') }} <span class="text-rose-500">*</span></label>
                                <input
                                    type="text"
                                    wire:model="contactName"
                                    placeholder="{{ __('site.profile.referrals.contact_name_placeholder') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 px-3 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors"
                                />
                                @error('contactName') <p class="mt-0.5 text-[10px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">{{ __('site.profile.referrals.contact_number_label') }} <span class="text-rose-500">*</span></label>
                                <input
                                    type="tel"
                                    inputmode="numeric"
                                    maxlength="10"
                                    wire:model="contactNumber"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                    placeholder="{{ __('site.profile.referrals.contact_number_placeholder') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 px-3 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors"
                                />
                                @error('contactNumber') <p class="mt-0.5 text-[10px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Row 3: Company & Rating --}}
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">{{ __('site.profile.referrals.company_details_label') }}</label>
                                <input
                                    type="text"
                                    wire:model="companyDetails"
                                    placeholder="{{ __('site.profile.referrals.company_details_placeholder') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 px-3 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors"
                                />
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">{{ __('site.profile.referrals.rating_label') }}</label>
                                <div class="flex gap-1.5">
                                    @foreach (\App\Livewire\Profile\Referrals::LEAD_RATINGS as $rating)
                                        <button
                                            type="button"
                                            wire:click="$set('leadRating', '{{ $rating }}')"
                                            class="flex-1 rounded-lg border py-1.5 px-1 text-[11px] font-bold uppercase tracking-tight transition-all cursor-pointer text-center {{ $leadRating === $rating ? ($ratingBadge[$rating] ?? '') . ' ring-1.5 ring-primary/40 font-black shadow-2xs' : 'border-slate-200 bg-slate-50/60 text-slate-600 hover:bg-slate-100' }}"
                                        >
                                            {{ $rating === 'hot' ? '🔥 Hot' : ($rating === 'warm' ? '🟡 Warm' : '❄️ Cold') }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Row 4: Requirement --}}
                        <div>
                            <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">{{ __('site.profile.referrals.requirement_label') }} <span class="text-rose-500">*</span></label>
                            <textarea
                                rows="2"
                                wire:model="businessRequirement"
                                placeholder="{{ __('site.profile.referrals.requirement_placeholder') }}"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 px-3 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors resize-none"
                            ></textarea>
                            @error('businessRequirement') <p class="mt-0.5 text-[10px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Row 5: Notes --}}
                        <div>
                            <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">{{ __('site.profile.referrals.comments_label') }}</label>
                            <input
                                type="text"
                                wire:model="giverComments"
                                placeholder="{{ __('site.profile.referrals.comments_placeholder') }}"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 px-3 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors"
                            />
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                            <button
                                type="button"
                                wire:click="cancelGive"
                                class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 active:scale-[0.98] transition-all cursor-pointer shadow-xs"
                            >
                                {{ __('site.profile.referrals.cancel') }}
                            </button>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer shrink-0"
                            >
                                <span wire:loading.remove wire:target="giveReferral">{{ __('site.profile.referrals.give_submit') }}</span>
                                <span wire:loading wire:target="giveReferral">{{ __('site.profile.referrals.saving') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

    {{-- Update Referral Modal (receiver) --}}
    @if ($updatingId !== null)
        <template x-teleport="body">
            <div
                class="fixed inset-0 z-[99999] overflow-y-auto p-3 sm:p-4 flex min-h-full items-center justify-center font-outfit"
                x-on:keydown.escape.window="$wire.cancelUpdate()"
            >
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" wire:click="cancelUpdate"></div>

                <div class="relative z-10 w-full max-w-lg bg-white rounded-3xl p-5 sm:p-6 shadow-2xl border border-slate-200 my-auto flex flex-col space-y-4">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">{{ __('site.profile.referrals.modal_update_title') }}</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Update contact progress, closed deal value, and feedback</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="cancelUpdate"
                            class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer shadow-2xs"
                            title="Close"
                        >
                            <x-icon name="x" class="h-3.5 w-3.5" />
                        </button>
                    </div>

                    <form wire:submit="updateReferral" class="space-y-3">
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">{{ __('site.profile.referrals.contact_status_label') }}</label>
                                <select
                                    wire:model="updateContactStatus"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 px-3 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors cursor-pointer"
                                >
                                    <option value="not_connected">{{ __('site.profile.referrals.contact_status_not_connected') }}</option>
                                    <option value="connected">{{ __('site.profile.referrals.contact_status_connected') }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">{{ __('site.profile.referrals.referral_status_label') }}</label>
                                <select
                                    wire:model.live="updateStatus"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 px-3 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors cursor-pointer"
                                >
                                    <option value="in_progress">{{ __('site.profile.referrals.status_in_progress') }}</option>
                                    <option value="closed">{{ __('site.profile.referrals.status_closed') }}</option>
                                    <option value="rejected">{{ __('site.profile.referrals.status_rejected') }}</option>
                                </select>
                            </div>
                        </div>

                        @if ($updateStatus === 'closed')
                            <div>
                                <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">{{ __('site.profile.referrals.amount_label') }} (₹) <span class="text-rose-500">*</span></label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model="updateAmount"
                                    placeholder="{{ __('site.profile.referrals.amount_placeholder') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 px-3 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors"
                                />
                                @error('updateAmount') <p class="mt-0.5 text-[10px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">{{ __('site.profile.referrals.notes_label') }}</label>
                            <input
                                type="text"
                                wire:model="updateReceiverComments"
                                placeholder="{{ __('site.profile.referrals.notes_placeholder') }}"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 px-3 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors"
                            />
                        </div>

                        @if ($updateStatus === 'closed')
                            <div class="space-y-1.5 border-t border-slate-100 pt-2">
                                <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">{{ __('site.profile.referrals.testimonial_label') }}</label>
                                <textarea
                                    rows="2"
                                    wire:model="updateTestimonial"
                                    placeholder="{{ __('site.profile.referrals.testimonial_placeholder') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 px-3 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors resize-none"
                                ></textarea>
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer pt-0.5">
                                    <input
                                        type="checkbox"
                                        wire:model="updateDisplayTestimonial"
                                        class="rounded border-slate-300 text-primary focus:ring-primary h-3.5 w-3.5"
                                    />
                                    <span class="text-[11px]">{{ __('site.profile.referrals.display_testimonial_label') }}</span>
                                </label>
                            </div>
                        @endif

                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                            <button
                                type="button"
                                wire:click="cancelUpdate"
                                class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 active:scale-[0.98] transition-all cursor-pointer shadow-xs"
                            >
                                {{ __('site.profile.referrals.cancel') }}
                            </button>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer shrink-0"
                            >
                                <span wire:loading.remove wire:target="updateReferral">{{ __('site.profile.referrals.save_update') }}</span>
                                <span wire:loading wire:target="updateReferral">{{ __('site.profile.referrals.saving') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

    <x-admin.confirm-modal
        :show="$deletingId !== null"
        :title="__('site.profile.referrals.withdraw_title')"
        :message="__('site.profile.referrals.withdraw_msg')"
        :confirm-label="__('site.profile.referrals.withdraw_confirm')"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
