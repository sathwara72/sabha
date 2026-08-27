@php
    $inputClass = 'w-full rounded-lg border border-border bg-white px-3 py-1.5 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary font-semibold';
    $labelClass = 'text-[12px] font-bold text-muted-foreground uppercase tracking-wide mb-0.5 block';
    $avatarSrc = $avatarFile ? $avatarFile->temporaryUrl() : media_url($user->avatar);
@endphp

<div class="bg-background font-outfit py-3 px-2">
    <div class="mx-auto max-w-6xl space-y-5">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-2 items-start">
            {{-- ===== Side Menu ===== --}}
            <aside class="lg:col-span-3 lg:sticky lg:top-20 space-y-3">
                <div class="glass-card p-4 flex flex-col items-center text-center">
                    <div class="relative">
                        <div class="h-16 w-16 rounded-xl overflow-hidden bg-primary/10 border border-border flex items-center justify-center shadow-sm">
                            @if ($avatarSrc)
                                <img src="{{ $avatarSrc }}" alt="Profile" class="h-full w-full object-cover" />
                            @else
                                <span class="text-2xl font-bold text-primary uppercase">{{ mb_substr($user->name ?? '?', 0, 1) }}</span>
                            @endif
                        </div>
                        <label class="absolute -bottom-1.5 -right-1.5 h-6 w-6 rounded-full bg-primary text-white flex items-center justify-center cursor-pointer shadow-md transition-opacity hover:opacity-90" title="Upload photo">
                            <x-icon name="camera" class="h-[11px] w-[11px]" />
                            <input type="file" accept="image/*" wire:model="avatarFile" x-on:change="$wire.set('activeTab', 'profile')" class="hidden" />
                        </label>
                    </div>
                    <h2 class="mt-2 text-sm font-bold text-foreground leading-tight">{{ $user->name }}</h2>
                    <p class="text-[12px] text-muted font-medium truncate max-w-full">{{ $user->email }}</p>
                    <span class="mt-1.5 inline-flex items-center gap-1 rounded-full bg-primary-soft border border-primary/10 px-2 py-0.5 text-[12px] font-bold uppercase tracking-wide text-primary">
                        {{ $user->role === 'admin' ? __('site.profile.administrator') : __('site.profile.member') }}
                    </span>
                    <x-member-title-badge :title="$user->memberTitle" class="mt-1.5" />
                    @if ($avatarFile)
                        <p class="mt-1 text-[12px] font-semibold text-amber-600">{{ __('site.profile.photo_pending') }}</p>
                    @endif
                </div>

                <nav class="glass-card p-1.5 space-y-0.5">
                    <button type="button" wire:click="setTab('profile')" class="w-full flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-semibold transition-colors cursor-pointer {{ $activeTab === 'profile' ? 'bg-primary text-white shadow-sm' : 'text-foreground hover:bg-surface' }}">
                        <x-icon name="user" class="h-[14px] w-[14px] {{ $activeTab === 'profile' ? 'text-white' : 'text-primary' }}" />
                        <span class="flex-1 text-left">{{ __('site.profile.tab_profile') }}</span>
                        <x-icon name="chevron-right" class="h-[13px] w-[13px] {{ $activeTab === 'profile' ? 'text-white/80' : 'text-muted-foreground' }}" />
                    </button>

                    <button type="button" wire:click="setTab('business')" class="w-full flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-semibold transition-colors cursor-pointer {{ $activeTab === 'business' ? 'bg-primary text-white shadow-sm' : 'text-foreground hover:bg-surface' }}">
                        <x-icon name="briefcase" class="h-[14px] w-[14px] {{ $activeTab === 'business' ? 'text-white' : 'text-primary' }}" />
                        <span class="flex-1 text-left">{{ __('site.profile.tab_business') }}</span>
                        @if ($business)
                            <span class="h-1.5 w-1.5 rounded-full {{ $business->status === 'approved' ? 'bg-emerald-500' : ($business->status === 'rejected' ? 'bg-red-500' : 'bg-amber-500') }}" title="{{ $business->status }}"></span>
                        @endif
                        <x-icon name="chevron-right" class="h-[13px] w-[13px] {{ $activeTab === 'business' ? 'text-white/80' : 'text-muted-foreground' }}" />
                    </button>

                    <button type="button" wire:click="setTab('events')" class="w-full flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-semibold transition-colors cursor-pointer {{ $activeTab === 'events' ? 'bg-primary text-white shadow-sm' : 'text-foreground hover:bg-surface' }}">
                        <x-icon name="calendar" class="h-[14px] w-[14px] {{ $activeTab === 'events' ? 'text-white' : 'text-primary' }}" />
                        <span class="flex-1 text-left">{{ __('site.profile.tab_events') }}</span>
                        <span class="inline-flex h-4 items-center justify-center rounded-full px-1.5 text-[12px] font-bold {{ $activeTab === 'events' ? 'bg-white/20 text-white' : 'bg-primary-soft text-primary border border-primary/10' }}">{{ $registeredEvents->count() }}</span>
                        <x-icon name="chevron-right" class="h-[13px] w-[13px] {{ $activeTab === 'events' ? 'text-white/80' : 'text-muted-foreground' }}" />
                    </button>

                    <button type="button" wire:click="setTab('analytics')" class="w-full flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-semibold transition-colors cursor-pointer {{ $activeTab === 'analytics' ? 'bg-primary text-white shadow-sm' : 'text-foreground hover:bg-surface' }}">
                        <x-icon name="trending-up" class="h-[14px] w-[14px] {{ $activeTab === 'analytics' ? 'text-white' : 'text-primary' }}" />
                        <span class="flex-1 text-left">{{ __('site.profile.tab_analytics') }}</span>
                        <x-icon name="chevron-right" class="h-[13px] w-[13px] {{ $activeTab === 'analytics' ? 'text-white/80' : 'text-muted-foreground' }}" />
                    </button>

                    <button type="button" wire:click="setTab('meetings')" class="w-full flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-semibold transition-colors cursor-pointer {{ $activeTab === 'meetings' ? 'bg-primary text-white shadow-sm' : 'text-foreground hover:bg-surface' }}">
                        <x-icon name="users" class="h-[14px] w-[14px] {{ $activeTab === 'meetings' ? 'text-white' : 'text-primary' }}" />
                        <span class="flex-1 text-left">{{ __('site.profile.tab_meetings') }}</span>
                        <x-icon name="chevron-right" class="h-[13px] w-[13px] {{ $activeTab === 'meetings' ? 'text-white/80' : 'text-muted-foreground' }}" />
                    </button>

                    <button type="button" wire:click="setTab('referrals-given')" class="w-full flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-semibold transition-colors cursor-pointer {{ $activeTab === 'referrals-given' ? 'bg-primary text-white shadow-sm' : 'text-foreground hover:bg-surface' }}">
                        <x-icon name="send" class="h-[14px] w-[14px] {{ $activeTab === 'referrals-given' ? 'text-white' : 'text-primary' }}" />
                        <span class="flex-1 text-left">{{ __('site.profile.tab_referrals_given') }}</span>
                        <x-icon name="chevron-right" class="h-[13px] w-[13px] {{ $activeTab === 'referrals-given' ? 'text-white/80' : 'text-muted-foreground' }}" />
                    </button>

                    <button type="button" wire:click="setTab('referrals-received')" class="w-full flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-semibold transition-colors cursor-pointer {{ $activeTab === 'referrals-received' ? 'bg-primary text-white shadow-sm' : 'text-foreground hover:bg-surface' }}">
                        <x-icon name="download" class="h-[14px] w-[14px] {{ $activeTab === 'referrals-received' ? 'text-white' : 'text-primary' }}" />
                        <span class="flex-1 text-left">{{ __('site.profile.tab_referrals_received') }}</span>
                        <x-icon name="chevron-right" class="h-[13px] w-[13px] {{ $activeTab === 'referrals-received' ? 'text-white/80' : 'text-muted-foreground' }}" />
                    </button>

                    <div class="my-1 border-t border-border"></div>

                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-semibold text-red-600 transition-colors hover:bg-red-50 cursor-pointer">
                            <x-icon name="log-out" class="h-[14px] w-[14px]" />
                            <span class="flex-1 text-left">{{ __('site.profile.logout') }}</span>
                        </button>
                    </form>
                </nav>
            </aside>

            {{-- ===== Content ===== --}}
            <div class="lg:col-span-9 space-y-3">

                {{-- Personal Settings --}}
                @if ($activeTab === 'profile')
                    <div class="glass-card p-4 space-y-4 h-fit">
                        <div class="flex items-center gap-2 border-b border-border pb-2">
                            <x-icon name="user" class="h-[15px] w-[15px] text-primary" />
                            <div>
                                <h2 class="text-sm font-bold text-foreground leading-tight">{{ __('site.profile.profile_title') }}</h2>
                                <p class="text-[12px] text-muted font-medium">{{ __('site.profile.profile_subtitle') }}</p>
                            </div>
                        </div>

                        @if ($profileError)
                            <div class="rounded-xl bg-red-50 border border-red-100 p-3 text-center text-xs font-semibold text-red-600">{{ $profileError }}</div>
                        @endif
                        @if ($profileSuccess)
                            <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center text-xs font-semibold text-emerald-600">{{ $profileSuccess }}</div>
                        @endif

                        <form wire:submit="updateProfile" class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="{{ $labelClass }}">{{ __('site.profile.full_name') }}</label>
                                    <div class="relative">
                                        <x-icon name="user" class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                                        <input type="text" required wire:model.blur="profileName" class="{{ $inputClass }} pl-8" />
                                    </div>
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}">{{ __('site.profile.email') }}</label>
                                    <div class="relative">
                                        <x-icon name="mail" class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                                        <input type="email" required wire:model.blur="profileEmail" class="{{ $inputClass }} pl-8" />
                                    </div>
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}">{{ __('site.profile.phone') }}</label>
                                    <div class="relative">
                                        <x-icon name="phone" class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                                        <input type="text" maxlength="10" wire:model.live="profilePhone" placeholder="10-digit mobile number" class="{{ $inputClass }} pl-8" />
                                    </div>
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}">{{ __('site.profile.city') }}</label>
                                    <x-searchable-select
                                        wire-model="profileCity"
                                        :options="$cityOptions"
                                        :value="$profileCity"
                                        placeholder="e.g. Ahmedabad"
                                        leading-icon="map-pin"
                                        class="{{ $inputClass }}"
                                    />
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}">Native City (વતન)</label>
                                    <x-searchable-select
                                        wire-model="profileNativeCity"
                                        :options="$cityOptions"
                                        :value="$profileNativeCity"
                                        placeholder="e.g. Surendranagar"
                                        class="{{ $inputClass }}"
                                    />
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}">Birth Date (જન્મ તારીખ)</label>
                                    <input type="date" wire:model.blur="profileBirthDate" class="{{ $inputClass }}" />
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}">Marriage / Anniversary Date</label>
                                    <input type="date" wire:model.blur="profileAnniversaryDate" class="{{ $inputClass }}" />
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}">Residence Address Area / Location</label>
                                    <input type="text" wire:model.blur="profileResidenceAddress" placeholder="e.g. Nikol, Ahmedabad" class="{{ $inputClass }}" />
                                </div>
                            </div>

                            <div>
                                <label class="{{ $labelClass }}">{{ __('site.profile.password') }}</label>
                                <div class="relative">
                                    <x-icon name="lock" class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                                    <input type="password" placeholder="New password" wire:model.blur="profilePassword" class="{{ $inputClass }} pl-8" />
                                </div>
                            </div>

                            <button type="submit" wire:loading.attr="disabled" wire:target="updateProfile" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer">
                                <span wire:loading.remove wire:target="updateProfile">{{ __('site.profile.save_btn') }}</span>
                                <span wire:loading wire:target="updateProfile">{{ __('site.profile.saving_btn') }}</span>
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Business Details Section --}}
                @if ($activeTab === 'business')
                    <div class="space-y-3">
                        <div class="glass-card p-4 space-y-4">
                            <div class="flex items-center justify-between border-b border-border pb-2">
                                <h2 class="text-sm font-bold text-foreground flex items-center gap-2">
                                    <x-icon name="briefcase" class="h-[15px] w-[15px] text-primary" /> {{ __('site.profile.business_title') }}
                                </h2>
                                @if ($business)
                                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ $business->status === 'approved' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200/50' : ($business->status === 'rejected' ? 'bg-red-50 text-red-600 border border-red-200/50' : 'bg-amber-50 text-amber-600 border border-amber-200/50') }}">
                                        @if ($business->status === 'approved') <x-icon name="shield-check" class="h-[14px] w-[14px]" />
                                        @elseif ($business->status === 'rejected') <x-icon name="x-circle" class="h-[14px] w-[14px]" />
                                        @else <x-icon name="clock" class="h-[14px] w-[14px]" /> @endif
                                        <span class="capitalize">{{ $business->status }}</span>
                                    </span>
                                @endif
                            </div>

                            {{-- Progress Steps --}}
                            <div class="flex items-center gap-0 mb-2">
                                <div class="flex flex-col items-center flex-1">
                                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-colors {{ !$business ? 'bg-primary border-primary text-white' : 'bg-emerald-500 border-emerald-500 text-white' }}">
                                        @if ($business) <x-icon name="check-circle-2" class="h-4 w-4" /> @else 1 @endif
                                    </div>
                                    <p class="text-[12px] font-bold text-center mt-1 text-foreground leading-tight">{{ __('site.profile.step_payment') }}</p>
                                </div>
                                <div class="flex-1 h-0.5 -mt-4 mx-1 rounded {{ $business ? 'bg-emerald-400' : 'bg-border' }}"></div>
                                <div class="flex flex-col items-center flex-1">
                                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-colors {{ $business?->status === 'approved' ? 'bg-emerald-500 border-emerald-500 text-white' : ($business ? 'bg-amber-400 border-amber-400 text-white' : 'bg-white border-border text-muted-foreground') }}">
                                        @if ($business?->status === 'approved') <x-icon name="check-circle-2" class="h-4 w-4" /> @else <x-icon name="clock" class="h-[14px] w-[14px]" /> @endif
                                    </div>
                                    <p class="text-[12px] font-bold text-center mt-1 leading-tight {{ $business?->status === 'approved' ? 'text-foreground' : 'text-muted' }}">{{ __('site.profile.step_approval') }}</p>
                                </div>
                                <div class="flex-1 h-0.5 -mt-4 mx-1 rounded {{ $business?->status === 'approved' ? 'bg-emerald-400' : 'bg-border' }}"></div>
                                <div class="flex flex-col items-center flex-1">
                                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-colors {{ $business?->status === 'approved' && ($business?->description || !$isEditingBusiness) ? 'bg-emerald-500 border-emerald-500 text-white' : ($business?->status === 'approved' ? 'bg-primary border-primary text-white' : 'bg-white border-border text-muted-foreground') }}">
                                        @if ($business?->status === 'approved' && ($business?->description || !$isEditingBusiness)) <x-icon name="check-circle-2" class="h-4 w-4" /> @else 3 @endif
                                    </div>
                                    <p class="text-[12px] font-bold text-center mt-1 leading-tight {{ $business?->status === 'approved' ? 'text-foreground' : 'text-muted' }}">{{ __('site.profile.step_details') }}</p>
                                </div>
                            </div>

                            @if ($bizError)
                                <div class="rounded-xl bg-red-50 border border-red-100 p-3 text-center text-xs font-semibold text-red-600">{{ $bizError }}</div>
                            @endif
                            @if ($bizSuccess)
                                <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center text-xs font-semibold text-emerald-600">{{ $bizSuccess }}</div>
                            @endif

                            {{-- STEP 1: No business yet --}}
                            @if (!$business)
                                <div class="space-y-6">
                                    <div class="rounded-xl bg-blue-50 border border-blue-100 p-4 flex items-start gap-3">
                                        <x-icon name="alert-circle" class="h-5 w-5 text-blue-500 shrink-0 mt-0.5" />
                                        <div>
                                            <p class="text-sm font-bold text-blue-800">{{ __('site.profile.how_it_works') }}</p>
                                            <ol class="mt-1.5 space-y-1 text-xs text-blue-700 font-medium list-decimal list-inside">
                                                <li>{{ __('site.profile.biz_step1') }}</li>
                                                <li>{{ __('site.profile.biz_step2') }}</li>
                                                <li>{{ __('site.profile.biz_step3') }}</li>
                                            </ol>
                                        </div>
                                    </div>

                                    <form wire:submit="submitBusiness" class="space-y-5">
                                        <div class="space-y-2">
                                            <h4 class="text-sm font-bold text-foreground flex items-center gap-2">
                                                <x-icon name="upload" class="h-[15px] w-[15px] text-primary" /> {{ __('site.profile.upload_payment_title') }}
                                            </h4>
                                            <p class="text-xs text-muted font-medium">{{ __('site.profile.upload_payment_desc') }}</p>

                                            <label class="mt-3 flex flex-col items-center justify-center border-2 border-dashed border-primary/30 rounded-2xl p-10 bg-primary-soft/20 hover:bg-primary-soft/30 transition-colors cursor-pointer relative group">
                                                <input type="file" accept="image/*" required wire:model="paymentFile" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                                                @if ($paymentFile)
                                                    <div class="flex flex-col items-center gap-2">
                                                        <div class="h-24 w-36 rounded-xl border border-border overflow-hidden bg-slate-900 shadow-sm">
                                                            <img src="{{ $paymentFile->temporaryUrl() }}" alt="Payment Preview" class="h-full w-full object-cover" />
                                                        </div>
                                                        <span class="text-sm font-bold text-foreground text-center truncate max-w-[220px]">{{ $paymentFile->getClientOriginalName() }}</span>
                                                        <span class="text-xs text-emerald-600 font-semibold">{{ __('site.profile.screenshot_selected') }} ✓</span>
                                                    </div>
                                                @else
                                                    <div class="h-14 w-14 rounded-2xl bg-primary-soft flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                                                        <x-icon name="upload" class="h-7 w-7 text-primary" />
                                                    </div>
                                                    <span class="text-sm font-semibold text-foreground text-center">{{ __('site.profile.click_upload_receipt') }}</span>
                                                    <span class="text-[12px] text-muted mt-1">{{ __('site.profile.file_hint') }}</span>
                                                @endif
                                            </label>
                                        </div>

                                        <button type="submit" wire:loading.attr="disabled" wire:target="submitBusiness,paymentFile" {{ !$paymentFile ? 'disabled' : '' }} class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-50 cursor-pointer">
                                            <span wire:loading.remove wire:target="submitBusiness">{{ __('site.profile.biz_submit_payment') }}</span>
                                            <span wire:loading wire:target="submitBusiness">{{ __('site.profile.biz_submitting') }}</span>
                                        </button>
                                    </form>
                                </div>
                            @endif

                            {{-- STEP 2: pending --}}
                            @if ($business && $business->status === 'pending')
                                <div class="space-y-4">
                                    <div class="rounded-xl bg-amber-50 border border-amber-100 p-5 flex items-start gap-3">
                                        <x-icon name="clock" class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" />
                                        <div>
                                            <p class="font-bold text-amber-800">{{ __('site.profile.payment_review_title') }}</p>
                                            <p class="font-medium text-xs mt-1 text-amber-700">{{ __('site.profile.payment_review_desc') }}</p>
                                        </div>
                                    </div>

                                    <div class="glass-card p-5 rounded-2xl border border-border/80 shadow-xs">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-center">
                                            @if (media_url($business->payment_screenshot))
                                                <div class="md:col-span-5 space-y-1.5">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-[12px] uppercase font-bold text-muted tracking-wider">{{ __('site.profile.submitted_screenshot') }}</span>
                                                        <a href="{{ media_url($business->payment_screenshot) }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-1 text-[12px] font-bold text-primary hover:opacity-85 transition-opacity">
                                                            <x-icon name="eye" class="h-3 w-3" /> {{ __('site.profile.view_full') }}
                                                        </a>
                                                    </div>
                                                    <div class="relative rounded-xl border border-border overflow-hidden h-40 sm:h-44 w-full bg-slate-900 group shadow-sm">
                                                        <img src="{{ media_url($business->payment_screenshot) }}" alt="Payment Screenshot" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                                        <a href="{{ media_url($business->payment_screenshot) }}" target="_blank" rel="noreferrer" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-xs font-bold gap-1.5 backdrop-blur-[2px]">
                                                            <x-icon name="eye" class="h-4 w-4" /> {{ __('site.profile.view_full') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="{{ media_url($business->payment_screenshot) ? 'md:col-span-7' : 'md:col-span-12' }}">
                                                <div class="rounded-xl border border-dashed border-border/80 bg-surface/30 p-6 flex flex-col items-center md:items-start text-center md:text-left gap-2.5">
                                                    <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shadow-xs">
                                                        <x-icon name="briefcase" class="h-5 w-5" />
                                                    </div>
                                                    <div>
                                                        <h3 class="text-sm font-bold text-foreground">{{ __('site.profile.details_locked_title') }}</h3>
                                                        <p class="text-xs text-muted font-medium mt-1 leading-relaxed max-w-sm">{{ __('site.profile.details_locked_desc') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- REJECTED --}}
                            @if ($business && $business->status === 'rejected')
                                <div class="space-y-5">
                                    <div class="rounded-xl bg-red-50 border border-red-100 p-4 flex items-start gap-3">
                                        <x-icon name="x-circle" class="h-5 w-5 text-red-600 shrink-0 mt-0.5" />
                                        <div>
                                            <p class="font-bold text-red-800">{{ __('site.profile.payment_rejected_title') }}</p>
                                            <p class="font-bold text-xs mt-1 text-red-700">{{ __('site.profile.biz_reason') }}: <span class="underline">{{ $business->rejection_reason ?: __('site.profile.rejected_default_reason') }}</span></p>
                                            <p class="font-medium text-xs mt-2 text-red-700">{{ __('site.profile.rejected_resubmit_hint') }}</p>
                                        </div>
                                    </div>
                                    <form wire:submit="submitBusiness" class="space-y-4">
                                        <label class="flex flex-col items-center justify-center border-2 border-dashed border-red-200 rounded-2xl p-8 bg-red-50/30 hover:bg-red-50/50 transition-colors cursor-pointer relative">
                                            <input type="file" accept="image/*" required wire:model="paymentFile" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                                            <x-icon name="upload" class="h-8 w-8 text-red-400 mb-2" />
                                            <span class="text-sm font-semibold text-foreground">{{ $paymentFile ? $paymentFile->getClientOriginalName() : __('site.profile.upload_new_screenshot') }}</span>
                                            <span class="text-[12px] text-muted mt-1">{{ __('site.profile.file_hint') }}</span>
                                        </label>
                                        <button type="submit" {{ !$paymentFile ? 'disabled' : '' }} wire:loading.attr="disabled" wire:target="submitBusiness" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white disabled:opacity-50 hover:opacity-90">
                                            <span wire:loading.remove wire:target="submitBusiness">{{ __('site.profile.biz_resubmit_payment') }}</span>
                                            <span wire:loading wire:target="submitBusiness">{{ __('site.profile.biz_resubmitting') }}</span>
                                        </button>
                                    </form>
                                </div>
                            @endif

                            {{-- APPROVED --}}
                            @if ($business && $business->status === 'approved')
                                <div class="space-y-6">
                                    @if (!$business->description)
                                        <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 flex items-start gap-3">
                                            <x-icon name="check-circle-2" class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" />
                                            <div>
                                                <p class="font-bold text-emerald-800">{{ __('site.profile.approved_title') }}</p>
                                                <p class="font-medium text-xs mt-1 text-emerald-700">{{ __('site.profile.approved_desc') }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if (!$isEditingBusiness)
                                        {{-- View mode --}}
                                        <div class="space-y-8">
                                            <div class="relative rounded-2xl overflow-hidden border border-border aspect-[3.2/1] max-h-[320px] min-h-[160px] bg-slate-900 shadow-sm">
                                                <x-safe-image :src="media_url($business->cover_image)" alt="Business Cover" :title="$business->name" fallback-type="banner" img-class="h-full w-full object-cover opacity-85" />
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                                                <div class="absolute bottom-4 left-6 flex items-end gap-4">
                                                    <div class="h-16 w-16 sm:h-20 sm:w-20 rounded-xl bg-white border border-border p-1.5 shadow-md flex items-center justify-center overflow-hidden shrink-0">
                                                        <x-safe-image :src="media_url($business->logo)" alt="Logo" :title="$business->name" fallback-type="business" img-class="h-full w-full object-contain" />
                                                    </div>
                                                    <div class="text-white pb-1 min-w-0 pr-4">
                                                        <h3 class="text-lg sm:text-xl font-bold truncate">{{ $business->name ?: __('site.profile.your_business') }}</h3>
                                                        <p class="text-xs font-semibold text-white/80 break-words [overflow-wrap:anywhere]">{{ $business->tagline ?: __('site.profile.verified_member') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                <div class="glass-card p-4 flex flex-col gap-1 border border-border/60 min-w-0">
                                                    <span class="text-[12px] uppercase font-bold text-muted-foreground flex items-center gap-1"><x-icon name="briefcase" class="h-3 w-3 text-primary" /> {{ __('site.profile.biz_category') }}</span>
                                                    <p class="text-xs font-extrabold text-foreground break-words">{{ $business->category ?: '—' }}</p>
                                                </div>
                                                <div class="glass-card p-4 flex flex-col gap-1 border border-border/60 min-w-0">
                                                    <span class="text-[12px] uppercase font-bold text-muted-foreground flex items-center gap-1"><x-icon name="map-pin" class="h-3 w-3 text-primary" /> {{ __('site.profile.biz_location') }}</span>
                                                    <p class="text-xs font-extrabold text-foreground break-words">{{ $business->area ?: '—' }}</p>
                                                </div>
                                                <div class="glass-card p-4 flex flex-col gap-1 border border-border/60 min-w-0">
                                                    <span class="text-[12px] uppercase font-bold text-muted-foreground flex items-center gap-1"><x-icon name="clock" class="h-3 w-3 text-primary" /> {{ __('site.profile.biz_hours') }}</span>
                                                    <p class="text-xs font-extrabold text-foreground break-words">{{ $business->hours ?: '—' }}</p>
                                                </div>
                                            </div>

                                            @if ($business->description)
                                                <div class="min-w-0">
                                                    <span class="text-[12px] uppercase font-bold text-muted">{{ __('site.profile.about_company') }}</span>
                                                    <p class="text-xs leading-relaxed text-muted mt-1 font-medium bg-surface/30 p-4 rounded-xl border border-border/60 break-words [overflow-wrap:anywhere] whitespace-pre-wrap">{{ $business->description }}</p>
                                                </div>
                                            @endif

                                            @if (!empty($business->services))
                                                <div>
                                                    <span class="text-[12px] uppercase font-bold text-muted block mb-2">{{ __('site.profile.core_services') }}</span>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach ($business->services as $s)
                                                            <span class="rounded-full bg-primary-soft border border-primary/10 px-3 py-1 text-xs font-semibold text-primary">{{ is_array($s) ? ($s['title'] ?? '') : $s }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="pt-4 border-t border-border">
                                                <button type="button" wire:click="$set('isEditingBusiness', true)" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 cursor-pointer">
                                                    {{ __('site.profile.biz_edit') }}
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Edit form --}}
                                        <form wire:submit="submitBusiness" class="space-y-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <label class="{{ $labelClass }}">{{ __('site.profile.biz_name') }}</label>
                                                    <input type="text" required wire:model.blur="bizName" placeholder="E.g. Vertex Solutions" class="{{ $inputClass }}" />
                                                </div>
                                                <div>
                                                    <label class="{{ $labelClass }}">Designation in Business</label>
                                                    <input type="text" wire:model.blur="bizDesignation" placeholder="E.g. Managing Director / Owner" class="{{ $inputClass }}" />
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <label class="{{ $labelClass }}">{{ __('site.profile.biz_category') }}</label>
                                                    <x-searchable-select
                                                        wire-model="bizCategory"
                                                        :options="$categories"
                                                        :value="$bizCategory"
                                                        :allow-custom="false"
                                                        placeholder="Search categories..."
                                                        class="{{ $inputClass }}"
                                                    />
                                                </div>
                                                <div>
                                                    <label class="{{ $labelClass }}">{{ __('site.profile.biz_tagline') }}</label>
                                                    <input type="text" wire:model.blur="bizTagline" placeholder="E.g. Enterprise Cloud Architecture" class="{{ $inputClass }}" />
                                                </div>
                                            </div>

                                            <div class="space-y-4 border-t border-border pt-4">
                                                <h4 class="text-xs font-extrabold text-foreground uppercase tracking-wider flex items-center gap-1.5">
                                                    <x-icon name="map-pin" class="h-3.5 w-3.5 text-primary" /> Address & Location Details
                                                </h4>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="{{ $labelClass }}">Street / Building Address</label>
                                                        <input type="text" wire:model.blur="bizAddress" placeholder="e.g. 402, Wall Street Business Park" class="{{ $inputClass }}" />
                                                    </div>
                                                    <div>
                                                        <label class="{{ $labelClass }}">City</label>
                                                        <x-searchable-select
                                                            wire-model="bizCity"
                                                            :options="$cityOptions"
                                                            :value="$bizCity"
                                                            placeholder="e.g. Ahmedabad"
                                                            class="{{ $inputClass }}"
                                                        />
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="{{ $labelClass }}">Area / Landmark</label>
                                                        <x-searchable-select
                                                            wire-model="bizArea"
                                                            :options="$areaOptions"
                                                            :value="$bizArea"
                                                            :wire-key="'biz-area-' . $bizCity"
                                                            placeholder="{{ $bizCity ? 'e.g. Navrangpura' : 'Select a city first' }}"
                                                            class="{{ $inputClass }}"
                                                        />
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="{{ $labelClass }}">State</label>
                                                        <input type="text" wire:model.blur="bizState" placeholder="e.g. Gujarat" class="{{ $inputClass }}" />
                                                    </div>
                                                    <div>
                                                        <label class="{{ $labelClass }}">Pincode</label>
                                                        <input type="text" wire:model.live="bizPincode" placeholder="e.g. 380009" class="{{ $inputClass }}" />
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="{{ $labelClass }}">Google Maps Embed Iframe Code or Map URL</label>
                                                    <textarea rows="3" wire:model.blur="bizMapIframe" placeholder='Paste Google Maps Embed HTML iframe code or map URL' class="{{ $inputClass }} resize-none font-mono text-[12px]"></textarea>
                                                    <p class="text-[12px] text-muted-foreground mt-1 font-medium">💡 Tip: Open Google Maps &gt; Search your location &gt; Share &gt; Embed a map &gt; Copy HTML and paste it here!</p>
                                                    @if (parse_google_maps_iframe_src($bizMapIframe))
                                                        <div class="mt-2.5 rounded-xl border border-border overflow-hidden bg-slate-900 h-44 w-full shadow-sm">
                                                            <iframe src="{{ parse_google_maps_iframe_src($bizMapIframe) }}" class="w-full h-full border-0" allowfullscreen loading="lazy"></iframe>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <label class="{{ $labelClass }}">{{ __('site.profile.biz_website') }}</label>
                                                    <div class="relative">
                                                        <x-icon name="globe" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                                                        <input type="url" wire:model.blur="bizWebsite" placeholder="e.g. https://vertex.solutions" class="{{ $inputClass }} pl-10" />
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="{{ $labelClass }}">{{ __('site.profile.biz_hours') }}</label>
                                                    <div class="relative">
                                                        <x-icon name="clock" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                                                        <input type="text" wire:model.blur="bizHours" placeholder="e.g. 9:00 AM - 6:00 PM (Mon - Fri)" class="{{ $inputClass }} pl-10" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="{{ $labelClass }}">{{ __('site.profile.biz_founded') }}</label>
                                                    <input type="text" wire:model.blur="bizFounded" placeholder="e.g. 2018" class="{{ $inputClass }}" />
                                                </div>
                                                <div>
                                                    <label class="{{ $labelClass }}">{{ __('site.profile.biz_team') }}</label>
                                                    <input type="text" wire:model.blur="bizTeamSize" placeholder="e.g. 45+ engineers" class="{{ $inputClass }}" />
                                                </div>
                                            </div>

                                            <div class="space-y-1.5">
                                                <label class="{{ $labelClass }}">{{ __('site.profile.about_company') }}</label>
                                                <textarea rows="4" required wire:model.blur="bizDescription" placeholder="Provide a comprehensive summary of your services, goals, and history..." class="{{ $inputClass }} resize-none"></textarea>
                                            </div>

                                            <div class="space-y-3">
                                                <div class="flex justify-between items-center">
                                                    <label class="{{ $labelClass }}">{{ __('site.profile.biz_services') }} (Max 4)</label>
                                                    @if (count($bizServices) < 4)
                                                        <button type="button" wire:click="addService" class="inline-flex items-center gap-1 text-[12px] font-extrabold text-primary hover:opacity-85">
                                                            <x-icon name="plus" class="h-3 w-3" /> {{ __('site.profile.add_service') }}
                                                        </button>
                                                    @endif
                                                </div>

                                                @if (empty($bizServices))
                                                    <div class="rounded-xl border border-dashed border-border p-4 text-center text-xs text-muted-foreground font-medium">
                                                        No services added yet. Click "Add Service" above to list your core services.
                                                    </div>
                                                @else
                                                    <div class="space-y-3">
                                                        @foreach ($bizServices as $idx => $service)
                                                            <div class="glass-card p-3 border border-border/60 relative space-y-2">
                                                                <button type="button" wire:click="removeService({{ $idx }})" class="absolute right-3 top-3 text-muted hover:text-red-500 transition-colors" title="Remove Service">
                                                                    <x-icon name="trash-2" class="h-[13px] w-[13px]" />
                                                                </button>
                                                                <div class="grid grid-cols-1 gap-2 pr-6">
                                                                    <div>
                                                                        <label class="text-[12px] font-bold text-muted-foreground uppercase mb-0.5 block">Service Title</label>
                                                                        <input type="text" required wire:model.blur="bizServices.{{ $idx }}.title" placeholder="e.g. Cloud Migration" class="{{ $inputClass }}" />
                                                                    </div>
                                                                    <div>
                                                                        <label class="text-[12px] font-bold text-muted-foreground uppercase mb-0.5 block">Service Description</label>
                                                                        <textarea rows="2" wire:model.blur="bizServices.{{ $idx }}.desc" placeholder="e.g. End-to-end cloud strategy and deployment services." class="{{ $inputClass }} resize-none"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="space-y-4 border-t border-border pt-4">
                                                <h4 class="text-sm font-bold text-foreground">{{ __('site.profile.contact_channels') }}</h4>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div>
                                                        <label class="{{ $labelClass }}">Business Email</label>
                                                        <input type="email" wire:model.blur="bizEmail" placeholder="{{ $user->email ?: 'e.g. contact@vertex.solutions' }}" class="{{ $inputClass }}" />
                                                    </div>
                                                    <div>
                                                        <label class="{{ $labelClass }}">Business Phone</label>
                                                        <input type="text" maxlength="10" wire:model.live="bizPhone" placeholder="{{ $user->phone ?: '10-digit mobile number' }}" class="{{ $inputClass }}" />
                                                    </div>
                                                    <div>
                                                        <label class="{{ $labelClass }}">Alternate Phone</label>
                                                        <input type="text" maxlength="10" wire:model.live="bizPhone2" placeholder="Optional 2nd number" class="{{ $inputClass }}" />
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div>
                                                        <label class="{{ $labelClass }}">{{ __('site.profile.biz_instagram') }}</label>
                                                        <div class="relative"><x-brand-icon name="instagram" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" /><input type="url" wire:model.blur="bizInstagram" placeholder="https://instagram.com/handle" class="{{ $inputClass }} pl-10" /></div>
                                                    </div>
                                                    <div>
                                                        <label class="{{ $labelClass }}">{{ __('site.profile.biz_youtube') }}</label>
                                                        <div class="relative"><x-brand-icon name="youtube" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" /><input type="url" wire:model.blur="bizYoutube" placeholder="https://youtube.com/c/channel" class="{{ $inputClass }} pl-10" /></div>
                                                    </div>
                                                    <div>
                                                        <label class="{{ $labelClass }}">{{ __('site.profile.biz_twitter') }}</label>
                                                        <div class="relative"><x-brand-icon name="twitter" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" /><input type="url" wire:model.blur="bizTwitter" placeholder="https://twitter.com/handle" class="{{ $inputClass }} pl-10" /></div>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="{{ $labelClass }}">{{ __('site.profile.biz_linkedin') }}</label>
                                                        <div class="relative"><x-brand-icon name="linkedin" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" /><input type="url" wire:model.blur="bizLinkedin" placeholder="https://linkedin.com/company/name" class="{{ $inputClass }} pl-10" /></div>
                                                    </div>
                                                    <div>
                                                        <label class="{{ $labelClass }}">WhatsApp Number</label>
                                                        <div class="relative"><x-icon name="message-circle" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" /><input type="text" maxlength="10" wire:model.live="bizWhatsapp" placeholder="10-digit WhatsApp number" class="{{ $inputClass }} pl-10" /></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="space-y-4 border-t border-border pt-4">
                                                <h4 class="text-sm font-bold text-foreground">{{ __('site.profile.branding_photos') }}</h4>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <div>
                                                        <label class="{{ $labelClass }}">{{ __('site.profile.biz_logo') }}</label>
                                                        <div
                                                            class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-4 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative min-h-[140px]"
                                                            x-data
                                                            x-on:click="$refs.logoPicker.click()"
                                                        >
                                                            <input
                                                                x-ref="logoPicker"
                                                                type="file"
                                                                accept="image/*"
                                                                class="hidden"
                                                                x-on:change="
                                                                    const f = $event.target.files[0];
                                                                    if (f) {
                                                                        window.dispatchEvent(new CustomEvent('open-cropper', { detail: { src: URL.createObjectURL(f), aspectRatio: 1, title: 'Crop Logo Image', target: 'logoFile' } }));
                                                                        $event.target.value = '';
                                                                    }
                                                                "
                                                            />
                                                            @if ($logoPreview)
                                                                <div class="flex flex-col items-center gap-2">
                                                                    <div class="h-16 w-16 rounded-xl border border-border overflow-hidden bg-white p-1 shadow-sm">
                                                                        <img src="{{ $logoFile ? $logoFile->temporaryUrl() : $logoPreview }}" alt="Logo Preview" class="h-full w-full object-contain" />
                                                                    </div>
                                                                    <span class="text-[12px] font-bold text-primary">Click to change</span>
                                                                </div>
                                                            @else
                                                                <x-icon name="upload" class="h-6 w-6 text-primary mb-2" />
                                                                <span class="text-[12px] font-semibold text-foreground text-center">{{ __('site.profile.choose_logo') }}</span>
                                                                <span class="text-[12px] text-muted-foreground mt-1">{{ __('site.profile.logo_hint') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="{{ $labelClass }}">{{ __('site.profile.biz_cover') }}</label>
                                                        <div
                                                            class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-4 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative min-h-[140px]"
                                                            x-data
                                                            x-on:click="$refs.coverPicker.click()"
                                                        >
                                                            <input
                                                                x-ref="coverPicker"
                                                                type="file"
                                                                accept="image/*"
                                                                class="hidden"
                                                                x-on:change="
                                                                    const f = $event.target.files[0];
                                                                    if (f) {
                                                                        window.dispatchEvent(new CustomEvent('open-cropper', { detail: { src: URL.createObjectURL(f), aspectRatio: 3.2/1, title: 'Crop Cover Banner Image', target: 'coverFile' } }));
                                                                        $event.target.value = '';
                                                                    }
                                                                "
                                                            />
                                                            @if ($coverPreview)
                                                                <div class="flex flex-col items-center gap-2 w-full">
                                                                    <div class="h-20 w-full max-w-[240px] rounded-xl border border-border overflow-hidden bg-slate-900 shadow-sm">
                                                                        <img src="{{ $coverFile ? $coverFile->temporaryUrl() : $coverPreview }}" alt="Cover Preview" class="h-full w-full object-cover" />
                                                                    </div>
                                                                    <span class="text-[12px] font-bold text-primary">Click to crop & change</span>
                                                                </div>
                                                            @else
                                                                <x-icon name="upload" class="h-6 w-6 text-primary mb-2" />
                                                                <span class="text-[12px] font-semibold text-foreground text-center">{{ __('site.profile.choose_cover') }}</span>
                                                                <span class="text-[12px] text-muted-foreground mt-1">{{ __('site.profile.cover_hint') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex gap-4 pt-4 border-t border-border">
                                                <button type="button" wire:click="$set('isEditingBusiness', false)" class="flex-1 rounded-xl border border-border bg-white px-4 py-3 text-sm font-semibold text-foreground transition-colors hover:bg-surface cursor-pointer">
                                                    {{ __('site.profile.biz_cancel') }}
                                                </button>
                                                <button type="submit" wire:loading.attr="disabled" wire:target="submitBusiness" class="flex-[2] inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer">
                                                    <span wire:loading.remove wire:target="submitBusiness">{{ __('site.profile.biz_save_details') }}</span>
                                                    <span wire:loading wire:target="submitBusiness">{{ __('site.profile.biz_saving') }}</span>
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Registered Events Section --}}
                @if ($activeTab === 'events')
                    <div class="space-y-3">
                        <div class="glass-card p-4 space-y-4">
                            <div>
                                <h2 class="text-sm font-bold text-foreground flex items-center gap-2 border-b border-border pb-2">
                                    <x-icon name="calendar" class="h-[15px] w-[15px] text-primary" /> {{ __('site.profile.events_title') }}
                                </h2>
                                <p class="mt-1 text-xs text-muted">{{ __('site.profile.events_subtitle') }}</p>
                            </div>

                            @if ($registeredEvents->isEmpty())
                                <div class="rounded-xl border border-dashed border-border py-12 text-center text-sm text-muted">{{ __('site.profile.no_events') }}</div>
                            @else
                                <div class="space-y-4">
                                    @foreach ($registeredEvents as $evt)
                                        @continue(!$evt->event)
                                        @php
                                            $eventDetails = $evt->event;
                                            $ticketPrice = (float) $evt->amount_paid === 0.0 ? 'Free' : '₹' . number_format((float) $evt->amount_paid);
                                            $isApproved = in_array($evt->status, ['approved', 'confirmed'], true);
                                            $isRejected = $evt->status === 'rejected';
                                        @endphp
                                        <a href="/profile/events/{{ $evt->id }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-xl border border-border bg-slate-50/50 hover:bg-white hover:shadow-sm hover:border-primary/20 transition-all group">
                                            <div class="h-16 w-28 rounded-lg overflow-hidden shrink-0 bg-slate-100 border border-border">
                                                <x-safe-image :src="media_url($eventDetails->image)" :alt="$eventDetails->title" :title="$eventDetails->title" :date="$eventDetails->date" fallback-type="event" img-class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-sm font-bold text-foreground truncate group-hover:text-primary transition-colors">{{ $eventDetails->title }}</h4>
                                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-[12px] text-muted font-medium mt-1">
                                                    <span class="inline-flex items-center gap-1 flex-wrap"><x-icon name="calendar" class="h-3 w-3" /> {{ $eventDetails->date->format('M j, Y') }}</span>
                                                    <span class="inline-flex items-center gap-1 flex-wrap"><x-icon name="map-pin" class="h-3 w-3" /> {{ $eventDetails->location }}</span>
                                                    <span class="font-semibold text-foreground">{{ __('site.profile.paid') }}: {{ $ticketPrice }}</span>
                                                </div>
                                            </div>
                                            <div class="shrink-0 flex flex-col sm:items-end gap-2 pt-2 sm:pt-0">
                                                <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-[12px] font-bold uppercase tracking-wide border {{ $isApproved ? 'bg-emerald-50 text-emerald-700 border-emerald-200/50' : ($isRejected ? 'bg-red-50 text-red-700 border-red-200/50' : 'bg-amber-50 text-amber-700 border-amber-200/50') }}">
                                                    @if ($isApproved)
                                                        <x-icon name="check-circle-2" class="h-3 w-3" /> {{ __('site.profile.event_confirmed') }}
                                                    @elseif ($isRejected)
                                                        <x-icon name="x-circle" class="h-3 w-3" /> {{ __('site.bookingDetail.status_rejected') }}
                                                    @else
                                                        <x-icon name="clock" class="h-3 w-3" /> {{ __('site.profile.event_pending') }}
                                                    @endif
                                                </span>
                                                @if ($evt->is_attended)
                                                    <span class="inline-flex items-center gap-0.5 rounded-full bg-emerald-100/70 border border-emerald-200/50 px-2 py-0.5 text-[12px] font-bold text-emerald-800 uppercase tracking-wider self-start sm:self-end">
                                                        <x-icon name="check-circle-2" class="h-[10px] w-[10px]" /> Attended
                                                    </span>
                                                @endif
                                                <span class="text-[12px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-0.5">
                                                    {{ __('site.profile.view_details') }} <x-icon name="chevron-right" class="h-3 w-3" />
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- My Analytics Section --}}
                @if ($activeTab === 'analytics')
                    <div class="glass-card p-4">
                        @livewire('profile.analytics')
                    </div>
                @endif

                {{-- 1-to-1 Meetings Section --}}
                @if ($activeTab === 'meetings')
                    <div class="glass-card p-4">
                        @livewire('profile.meetings')
                    </div>
                @endif

                {{-- Referrals Given Section --}}
                @if ($activeTab === 'referrals-given')
                    <div class="glass-card p-4">
                        @livewire('profile.referrals', ['direction' => 'given'], 'referrals-given')
                    </div>
                @endif

                {{-- Referrals Received Section --}}
                @if ($activeTab === 'referrals-received')
                    <div class="glass-card p-4">
                        @livewire('profile.referrals', ['direction' => 'received'], 'referrals-received')
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-image-cropper />
</div>
