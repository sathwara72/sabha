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
        <div class="space-y-3">
            @foreach ($referrals as $ref)
                <div class="glass-card p-4 space-y-2.5">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-foreground">
                                {{ $direction === 'given' ? __('site.profile.referrals.to') . ' ' . $ref->receiver?->name : __('site.profile.referrals.from') . ' ' . $ref->giver?->name }}
                            </h4>
                            <p class="mt-0.5 text-[12px] text-muted-foreground">{{ $ref->contact_name }} &middot; {{ $ref->contact_number }}</p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[12px] font-bold uppercase tracking-wide {{ $ratingBadge[$ref->lead_rating] ?? $ratingBadge['warm'] }}">{{ $ratingLabel[$ref->lead_rating] ?? $ref->lead_rating }}</span>
                            <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[12px] font-bold uppercase tracking-wide {{ $statusBadge[$ref->status] ?? $statusBadge['pending'] }}">{{ $statusLabel[$ref->status] ?? $ref->status }}</span>
                        </div>
                    </div>

                    @if ($ref->company_details)
                        <p class="text-[12px] text-muted-foreground"><span class="font-bold text-foreground">{{ __('site.profile.referrals.company_label') }}:</span> {{ $ref->company_details }}</p>
                    @endif
                    <p class="text-xs text-foreground">{{ $ref->business_requirement }}</p>
                    @if ($ref->giver_comments)
                        <p class="text-[12px] text-muted-foreground italic">"{{ $ref->giver_comments }}"</p>
                    @endif

                    @if ($ref->status === 'closed' && $ref->amount)
                        <p class="text-[12px] font-bold text-emerald-700">{{ __('site.profile.referrals.closed_amount') }}: ₹{{ number_format((float) $ref->amount) }}</p>
                    @endif
                    @if ($ref->receiver_comments)
                        <p class="text-[12px] text-muted-foreground"><span class="font-bold text-foreground">{{ __('site.profile.referrals.receiver_notes') }}:</span> {{ $ref->receiver_comments }}</p>
                    @endif

                    <div class="flex items-center justify-between gap-2 pt-1">
                        <span class="inline-flex items-center gap-1 text-[12px] font-semibold {{ $ref->contact_status === 'connected' ? 'text-emerald-600' : 'text-slate-400' }}">
                            <x-icon name="{{ $ref->contact_status === 'connected' ? 'check-circle-2' : 'clock' }}" class="h-3 w-3" />
                            {{ $ref->contact_status === 'connected' ? __('site.profile.referrals.contacted') : __('site.profile.referrals.not_contacted') }}
                        </span>

                        @if ($direction === 'given' && $ref->status === 'pending')
                            <button wire:click="openDelete({{ $ref->id }})" class="text-[12px] font-bold text-rose-600 hover:underline cursor-pointer">{{ __('site.profile.referrals.withdraw') }}</button>
                        @elseif ($direction === 'received')
                            <button wire:click="openUpdate({{ $ref->id }})" class="inline-flex items-center gap-1 rounded-lg bg-primary-soft px-2.5 py-1 text-[12px] font-bold text-primary hover:opacity-80 cursor-pointer">
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
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm" wire:click="cancelGive"></div>
            <div x-data x-show="true" x-transition class="relative z-50 w-full max-w-lg bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4 max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <x-icon name="send" class="h-4.5 w-4.5 text-primary" /> {{ __('site.profile.referrals.modal_give_title') }}
                    </h3>
                    <button wire:click="cancelGive" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                <form wire:submit="giveReferral" class="space-y-4">
                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.referrals.give_to_label') }}</label>
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
                            <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $labelClass }}">{{ __('site.profile.referrals.contact_name_label') }}</label>
                            <input type="text" wire:model="contactName" placeholder="{{ __('site.profile.referrals.contact_name_placeholder') }}" class="{{ $inputClass }}" />
                            @error('contactName') <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">{{ __('site.profile.referrals.contact_number_label') }}</label>
                            <input type="text" wire:model="contactNumber" placeholder="{{ __('site.profile.referrals.contact_number_placeholder') }}" class="{{ $inputClass }}" />
                            @error('contactNumber') <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.referrals.company_details_label') }}</label>
                        <input type="text" wire:model="companyDetails" placeholder="{{ __('site.profile.referrals.company_details_placeholder') }}" class="{{ $inputClass }}" />
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.referrals.requirement_label') }}</label>
                        <textarea rows="3" wire:model="businessRequirement" placeholder="{{ __('site.profile.referrals.requirement_placeholder') }}" class="{{ $inputClass }} resize-none"></textarea>
                        @error('businessRequirement') <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.referrals.rating_label') }}</label>
                        <div class="flex gap-2">
                            @foreach (\App\Livewire\Profile\Referrals::LEAD_RATINGS as $rating)
                                <button
                                    type="button"
                                    wire:click="$set('leadRating', '{{ $rating }}')"
                                    class="flex-1 rounded-xl border px-3 py-2 text-xs font-bold uppercase tracking-wide transition-all cursor-pointer {{ $leadRating === $rating ? ($ratingBadge[$rating] ?? '') . ' ring-1 ring-offset-1' : 'border-border text-slate-500 hover:bg-slate-50' }}"
                                >{{ $ratingLabel[$rating] ?? $rating }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.referrals.comments_label') }}</label>
                        <textarea rows="2" wire:model="giverComments" placeholder="{{ __('site.profile.referrals.comments_placeholder') }}" class="{{ $inputClass }} resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="cancelGive" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">{{ __('site.profile.referrals.cancel') }}</button>
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 disabled:opacity-50 transition-all cursor-pointer">
                            <span wire:loading.remove wire:target="giveReferral">{{ __('site.profile.referrals.give_submit') }}</span>
                            <span wire:loading wire:target="giveReferral">{{ __('site.profile.referrals.saving') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Update Referral Modal (receiver) --}}
    @if ($updatingId !== null)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm" wire:click="cancelUpdate"></div>
            <div x-data x-show="true" x-transition class="relative z-50 w-full max-w-lg bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4 max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <x-icon name="pencil" class="h-4.5 w-4.5 text-primary" /> {{ __('site.profile.referrals.modal_update_title') }}
                    </h3>
                    <button wire:click="cancelUpdate" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                <form wire:submit="updateReferral" class="space-y-4">
                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.referrals.contact_status_label') }}</label>
                        <select wire:model="updateContactStatus" class="{{ $inputClass }}">
                            <option value="not_connected">{{ __('site.profile.referrals.contact_status_not_connected') }}</option>
                            <option value="connected">{{ __('site.profile.referrals.contact_status_connected') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.referrals.referral_status_label') }}</label>
                        <select wire:model.live="updateStatus" class="{{ $inputClass }}">
                            <option value="in_progress">{{ __('site.profile.referrals.status_in_progress') }}</option>
                            <option value="closed">{{ __('site.profile.referrals.status_closed') }}</option>
                            <option value="rejected">{{ __('site.profile.referrals.status_rejected') }}</option>
                        </select>
                    </div>

                    @if ($updateStatus === 'closed')
                        <div>
                            <label class="{{ $labelClass }}">{{ __('site.profile.referrals.amount_label') }}</label>
                            <input type="number" step="0.01" min="0" wire:model="updateAmount" placeholder="{{ __('site.profile.referrals.amount_placeholder') }}" class="{{ $inputClass }}" />
                            @error('updateAmount') <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div>
                        <label class="{{ $labelClass }}">{{ __('site.profile.referrals.notes_label') }}</label>
                        <textarea rows="2" wire:model="updateReceiverComments" placeholder="{{ __('site.profile.referrals.notes_placeholder') }}" class="{{ $inputClass }} resize-none"></textarea>
                    </div>

                    @if ($updateStatus === 'closed')
                        <div class="space-y-2 border-t border-slate-100 pt-3">
                            <label class="{{ $labelClass }}">{{ __('site.profile.referrals.testimonial_label') }}</label>
                            <textarea rows="2" wire:model="updateTestimonial" placeholder="{{ __('site.profile.referrals.testimonial_placeholder') }}" class="{{ $inputClass }} resize-none"></textarea>
                            <label class="flex items-center gap-2 text-[12px] font-semibold text-foreground cursor-pointer">
                                <input type="checkbox" wire:model="updateDisplayTestimonial" class="rounded border-border text-primary focus:ring-primary/30" />
                                {{ __('site.profile.referrals.display_testimonial_label') }}
                            </label>
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="cancelUpdate" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">{{ __('site.profile.referrals.cancel') }}</button>
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 disabled:opacity-50 transition-all cursor-pointer">
                            <span wire:loading.remove wire:target="updateReferral">{{ __('site.profile.referrals.save_update') }}</span>
                            <span wire:loading wire:target="updateReferral">{{ __('site.profile.referrals.saving') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
