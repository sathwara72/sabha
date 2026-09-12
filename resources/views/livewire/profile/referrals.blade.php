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
                @php
                    $memberObj = $direction === 'given' ? $ref->receiver : $ref->giver;
                    $memberName = $memberObj?->name ?? 'Member';
                    $memberBiz = $memberObj?->business?->name;
                    $memberPhone = $memberObj?->phone;
                @endphp
                <div class="bg-white rounded-2xl p-3 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-primary/30 transition-all flex flex-col justify-between space-y-2">
                    <div class="space-y-2">
                        {{-- Top Header: Member Info, Small Chips, Date & Contacted --}}
                        <div class="border-b border-slate-100 pb-2 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="h-7 w-7 rounded-lg bg-primary-soft text-primary border border-primary/10 flex items-center justify-center font-bold text-[11px] shrink-0">
                                        {{ mb_substr($memberName, 0, 1) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block leading-none">
                                            {{ $direction === 'given' ? __('site.profile.referrals.to') : __('site.profile.referrals.from') }}
                                        </span>
                                        <div class="flex items-center gap-1.5 mt-0.5 min-w-0">
                                            <h4 class="text-xs font-bold text-slate-900 truncate leading-tight" title="{{ $memberName }}">
                                                {{ $memberName }}
                                            </h4>
                                            @if ($direction === 'received' && $memberPhone)
                                                <a href="tel:{{ $memberPhone }}" class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 transition-colors shrink-0 shadow-2xs" title="Call Giver ({{ $memberPhone }})">
                                                    <x-icon name="phone" class="h-2.5 w-2.5" />
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Top Right: Date --}}
                                <span class="text-[10px] text-slate-400 font-medium shrink-0">{{ $ref->created_at->format('d M, Y') }}</span>
                            </div>

                            {{-- Small Chips directly inside header --}}
                            <div class="flex items-center gap-1 flex-wrap">
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide border {{ $ratingBadge[$ref->lead_rating] ?? $ratingBadge['warm'] }}">
                                    {{ $ref->lead_rating === 'hot' ? '🔥 ' : ($ref->lead_rating === 'warm' ? '🟡 ' : '❄️ ') }}{{ $ratingLabel[$ref->lead_rating] ?? $ref->lead_rating }}
                                </span>
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide border {{ $statusBadge[$ref->status] ?? $statusBadge['pending'] }}">
                                    {{ $statusLabel[$ref->status] ?? $ref->status }}
                                </span>
                            </div>
                        </div>

                        {{-- ALL LEAD DETAILS TOGETHER IN ONE INNER CARD --}}
                        <div class="rounded-xl bg-slate-50/80 p-2 space-y-1 border border-slate-100 text-xs">
                            {{-- Line 1: Contact Name & Phone --}}
                            <div class="flex items-center justify-between gap-2 font-semibold">
                                <span class="truncate font-bold text-slate-900 text-xs flex items-center gap-1">
                                    <x-icon name="user" class="h-2.5 w-2.5 text-slate-400" /> {{ $ref->contact_name }}
                                </span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <a href="tel:{{ $ref->contact_number }}" class="text-[10px] font-semibold text-primary hover:underline flex items-center gap-0.5" title="Call Lead">
                                        <x-icon name="phone" class="h-2 w-2" /> {{ $ref->contact_number }}
                                    </a>
                                    <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $ref->contact_number) }}" target="_blank" rel="noopener" class="text-emerald-600 hover:opacity-80 flex items-center" title="WhatsApp Lead">
                                        <x-icon name="message-circle" class="h-2.5 w-2.5" />
                                    </a>
                                </div>
                            </div>

                            {{-- Line 2: Company Details --}}
                            @if ($ref->company_details)
                                <div class="flex items-center justify-between text-[10px] gap-2 pt-0.5 border-t border-slate-200/50">
                                    <span class="text-slate-500 font-medium shrink-0">{{ __('site.profile.referrals.company_label') }}</span>
                                    <span class="text-slate-800 font-semibold truncate text-right">{{ $ref->company_details }}</span>
                                </div>
                            @endif

                            {{-- Line 3: Requirement --}}
                            <div class="pt-0.5 border-t border-slate-200/50 space-y-0.5">
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block">Requirement</span>
                                <p class="text-[11px] text-slate-700 font-normal line-clamp-2 leading-tight">
                                    {{ $ref->business_requirement }}
                                </p>
                            </div>

                            {{-- Line 4: Giver Note (if any) --}}
                            @if ($ref->giver_comments)
                                <div class="pt-0.5 border-t border-slate-200/50 text-[10px] text-slate-500 italic truncate">
                                    <span class="font-bold text-slate-600 not-italic">Note:</span> "{{ $ref->giver_comments }}"
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Bottom Footer: Contacted Badge on Left, Actions on Right --}}
                    <div class="flex items-center justify-between gap-2 pt-1.5 border-t border-slate-100">
                        {{-- Left: Contacted badge --}}
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[9px] font-bold {{ $ref->contact_status === 'connected' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                            <x-icon name="{{ $ref->contact_status === 'connected' ? 'check-circle-2' : 'clock' }}" class="h-2.5 w-2.5" />
                            {{ $ref->contact_status === 'connected' ? __('site.profile.referrals.contacted') : __('site.profile.referrals.not_contacted') }}
                        </span>

                        {{-- Right: Action Buttons --}}
                        <div class="flex items-center gap-1.5">
                            @if ($direction === 'given')
                                @if ($ref->status === 'closed')
                                    <button wire:click="openOutcomeModal({{ $ref->id }})" class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-[11px] font-bold text-emerald-800 hover:bg-emerald-100 cursor-pointer shadow-2xs">
                                        <x-icon name="eye" class="h-2.5 w-2.5 text-emerald-600" /> View Outcome
                                    </button>
                                @elseif ($ref->status === 'pending')
                                    <button wire:click="openDelete({{ $ref->id }})" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 hover:underline cursor-pointer flex items-center gap-1">
                                        <x-icon name="trash-2" class="h-3 w-3" /> {{ __('site.profile.referrals.withdraw') }}
                                    </button>
                                @endif
                            @elseif ($direction === 'received')
                                <button wire:click="openUpdate({{ $ref->id }})" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 border border-blue-100 px-2 py-1 text-[11px] font-bold text-primary hover:bg-blue-100 cursor-pointer shadow-2xs">
                                    <x-icon name="pencil" class="h-2.5 w-2.5" /> {{ __('site.profile.referrals.update') }}
                                </button>
                            @endif
                        </div>
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

    {{-- Giver Testimonial Modal (in Referrals Given for closed deals) --}}
    @if ($giverTestimonialId !== null)
        <template x-teleport="body">
            <div
                class="fixed inset-0 z-[99999] overflow-y-auto p-3 sm:p-4 flex min-h-full items-center justify-center font-outfit"
                x-on:keydown.escape.window="$wire.cancelGiverTestimonial()"
            >
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" wire:click="cancelGiverTestimonial"></div>

                <div class="relative z-10 w-full max-w-lg bg-white rounded-3xl p-5 sm:p-6 shadow-2xl border border-slate-200 my-auto flex flex-col space-y-4">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs">
                                <x-icon name="message-square" class="h-4 w-4" />
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">Referral Testimonial</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Add or edit testimonial for this completed referral</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="cancelGiverTestimonial"
                            class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer shadow-2xs"
                            title="Close"
                        >
                            <x-icon name="x" class="h-3.5 w-3.5" />
                        </button>
                    </div>

                    <form wire:submit="saveGiverTestimonial" class="space-y-3">
                        <div>
                            <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-0.5 block">
                                Testimonial / Feedback <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                rows="3"
                                wire:model="giverTestimonialText"
                                placeholder="Share your experience and feedback on this referral deal..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2 px-3 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary transition-colors resize-none"
                            ></textarea>
                            @error('giverTestimonialText') <p class="mt-0.5 text-[10px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer pt-0.5">
                            <input
                                type="checkbox"
                                wire:model="giverDisplayTestimonial"
                                class="rounded border-slate-300 text-primary focus:ring-primary h-3.5 w-3.5"
                            />
                            <span class="text-[11px]">Display this testimonial publicly on business page</span>
                        </label>

                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                            <button
                                type="button"
                                wire:click="cancelGiverTestimonial"
                                class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 active:scale-[0.98] transition-all cursor-pointer shadow-xs"
                            >
                                {{ __('site.profile.referrals.cancel') }}
                            </button>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer shrink-0"
                            >
                                <span wire:loading.remove wire:target="saveGiverTestimonial">Save Testimonial</span>
                                <span wire:loading wire:target="saveGiverTestimonial">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

    {{-- Referral Outcome Modal (Closed Value & Testimonial popup) --}}
    @if ($outcomeModalId !== null && $selectedOutcomeRef)
        <template x-teleport="body">
            <div
                class="fixed inset-0 z-[99999] overflow-y-auto p-3 sm:p-4 flex min-h-full items-center justify-center font-outfit"
                x-on:keydown.escape.window="$wire.closeOutcomeModal()"
            >
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" wire:click="closeOutcomeModal"></div>

                <div class="relative z-10 w-full max-w-md bg-white rounded-3xl p-5 sm:p-6 shadow-2xl border border-slate-200 my-auto flex flex-col space-y-4">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                <x-icon name="check-circle-2" class="h-4 w-4 text-emerald-600" />
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">Referral Outcome</h3>
                                <p class="text-[11px] text-slate-500 font-medium">{{ $selectedOutcomeRef->contact_name }} • {{ $selectedOutcomeRef->receiver?->name }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="closeOutcomeModal"
                            class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer shadow-2xs"
                            title="Close"
                        >
                            <x-icon name="x" class="h-3.5 w-3.5" />
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="space-y-3">
                        {{-- Closed Deal Amount --}}
                        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-3.5 flex items-center justify-between shadow-2xs">
                            <span class="text-xs font-bold text-emerald-900 flex items-center gap-1.5">
                                <x-icon name="trending-up" class="h-3.5 w-3.5 text-emerald-600" /> {{ __('site.profile.referrals.closed_amount') }}
                            </span>
                            <span class="text-base font-black text-emerald-700">₹{{ number_format((float) ($selectedOutcomeRef->amount ?? 0)) }}</span>
                        </div>

                        {{-- Testimonial --}}
                        @if ($selectedOutcomeRef->testimonial)
                            <div class="rounded-2xl bg-amber-50/90 border border-amber-200/80 p-3.5 space-y-2 text-xs shadow-2xs">
                                <div class="flex items-center justify-between gap-1.5">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-900 flex items-center gap-1">
                                        <x-icon name="message-square" class="h-3.5 w-3.5 text-amber-600" /> Testimonial
                                    </span>
                                    <button
                                        type="button"
                                        wire:click="toggleWebsiteDisplay({{ $selectedOutcomeRef->id }})"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold border transition-all cursor-pointer {{ $selectedOutcomeRef->display_testimonial ? 'bg-emerald-100 text-emerald-800 border-emerald-200 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}"
                                        title="Click to toggle display on public website"
                                    >
                                        <x-icon name="{{ $selectedOutcomeRef->display_testimonial ? 'globe' : 'eye-off' }}" class="h-2.5 w-2.5" />
                                        {{ $selectedOutcomeRef->display_testimonial ? 'Website: Yes' : 'Website: No' }}
                                    </button>
                                </div>
                                <p class="text-xs text-slate-700 font-medium italic leading-relaxed">"{{ $selectedOutcomeRef->testimonial }}"</p>
                            </div>
                        @else
                            <div class="rounded-2xl bg-slate-50 border border-slate-200/70 p-3 text-center text-xs text-slate-500 italic">
                                No testimonial has been provided for this referral.
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end pt-2 border-t border-slate-100">
                        <button
                            type="button"
                            wire:click="closeOutcomeModal"
                            class="px-4 py-1.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 active:scale-[0.98] transition-all cursor-pointer shadow-xs"
                        >
                            Close
                        </button>
                    </div>
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
