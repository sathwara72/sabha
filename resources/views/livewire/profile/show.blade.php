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
                <div class="glass-card p-3.5 space-y-2.5">
                    <div class="flex items-center gap-3">
                        {{-- Left Side: Avatar / Logo --}}
                        <div class="relative shrink-0">
                            <div class="h-16 w-16 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200/90 flex items-center justify-center shadow-2xs">
                                @if ($avatarSrc)
                                    <img src="{{ $avatarSrc }}" alt="Profile" class="h-full w-full object-contain" />
                                @else
                                    <span class="text-xl font-black text-primary uppercase">{{ mb_substr($user->name ?? '?', 0, 1) }}</span>
                                @endif
                            </div>
                            <label class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full bg-primary text-white flex items-center justify-center cursor-pointer shadow-md transition-opacity hover:opacity-90" title="Upload photo">
                                <x-icon name="camera" class="h-2.5 w-2.5" />
                                <input
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    x-on:change="
                                        const f = $event.target.files[0];
                                        if (f) {
                                            $wire.set('activeTab', 'profile');
                                            window.dispatchEvent(new CustomEvent('open-cropper', {
                                                detail: {
                                                    src: URL.createObjectURL(f),
                                                    aspectRatio: 1,
                                                    title: 'Adjust / Crop Profile Photo',
                                                    target: 'avatarFile',
                                                    componentId: $el.closest('[wire\\:id]')?.getAttribute('wire:id')
                                                }
                                            }));
                                            $event.target.value = '';
                                        }
                                    "
                                />
                            </label>
                        </div>

                        {{-- Right Side: Details --}}
                        <div class="min-w-0 flex-1 text-left">
                            <h2 class="text-sm font-bold text-foreground leading-snug truncate">{{ $user->name }}</h2>
                            <p class="text-[11px] text-muted font-medium truncate mt-0.5">{{ $user->email }}</p>
                            <div class="flex items-center gap-1.5 flex-wrap mt-1.5">
                                <span class="inline-flex items-center rounded-full bg-primary-soft border border-primary/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-primary">
                                    {{ $user->role === 'admin' ? __('site.profile.administrator') : ($user->role === 'sub_admin' ? 'Sub-Admin' : __('site.profile.member')) }}
                                </span>
                                @if ($user->memberTitle)
                                    <x-member-title-badge :title="$user->memberTitle" />
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($avatarFile)
                        <p class="text-[11px] font-semibold text-amber-600 bg-amber-50 rounded-lg px-2 py-1 border border-amber-200/60 text-center">{{ __('site.profile.photo_pending') }}</p>
                    @endif

                    @if ($user->canAccessAdminArea())
                        <a href="/admin" class="w-full flex items-center justify-center gap-2 rounded-xl px-3 py-1.5 text-xs font-bold text-white bg-gradient-to-r from-[#00379D] to-[#082e6e] shadow-md shadow-primary/20 hover:opacity-95 active:scale-95 transition-all">
                            <x-icon name="shield-check" class="h-3.5 w-3.5" />
                            <span>Go to Admin Panel</span>
                        </a>
                    @endif
                </div>

                <nav class="glass-card p-1.5 space-y-0.5">
                    <button type="button" wire:click="setTab('overview')" class="w-full flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-semibold transition-colors cursor-pointer {{ in_array($activeTab, ['overview', 'analytics'], true) ? 'bg-primary text-white shadow-sm' : 'text-foreground hover:bg-surface' }}">
                        <x-icon name="layout-dashboard" class="h-[14px] w-[14px] {{ in_array($activeTab, ['overview', 'analytics'], true) ? 'text-white' : 'text-primary' }}" />
                        <span class="flex-1 text-left">{{ __('site.profile.tab_overview') }}</span>
                        <x-icon name="chevron-right" class="h-[13px] w-[13px] {{ in_array($activeTab, ['overview', 'analytics'], true) ? 'text-white/80' : 'text-muted-foreground' }}" />
                    </button>

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

                    <button type="button" wire:click="setTab('visitor-pass')" class="w-full flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-semibold transition-colors cursor-pointer {{ $activeTab === 'visitor-pass' ? 'bg-primary text-white shadow-sm' : 'text-foreground hover:bg-surface' }}">
                        <x-icon name="ticket" class="h-[14px] w-[14px] {{ $activeTab === 'visitor-pass' ? 'text-white' : 'text-primary' }}" />
                        <span class="flex-1 text-left">Visitor Pass</span>
                        <x-icon name="chevron-right" class="h-[13px] w-[13px] {{ $activeTab === 'visitor-pass' ? 'text-white/80' : 'text-muted-foreground' }}" />
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

                        <form wire:submit="updateProfile" x-on:keydown.enter="$event.target.tagName !== 'TEXTAREA' && $event.preventDefault()" class="space-y-3">
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
                                    <label class="{{ $labelClass }}">{{ __('site.profile.native_city') }}</label>
                                    <x-searchable-select
                                        wire-model="profileNativeCity"
                                        :options="$cityOptions"
                                        :value="$profileNativeCity"
                                        placeholder="e.g. Surendranagar"
                                        class="{{ $inputClass }}"
                                    />
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}">{{ __('site.profile.birth_date') }}</label>
                                    <input type="date" wire:model.blur="profileBirthDate" class="{{ $inputClass }}" />
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}">{{ __('site.profile.anniversary_date') }}</label>
                                    <input type="date" wire:model.blur="profileAnniversaryDate" class="{{ $inputClass }}" />
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}">{{ __('site.profile.residence_address') }}</label>
                                    <input type="text" wire:model.blur="profileResidenceAddress" placeholder="{{ __('site.profile.residence_address_placeholder') }}" class="{{ $inputClass }}" />
                                </div>
                            </div>

                            <div>
                                <label class="{{ $labelClass }}">{{ __('site.profile.password') }}</label>
                                <div class="relative">
                                    <x-icon name="lock" class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                                    <input type="password" placeholder="{{ __('site.profile.new_password_placeholder') }}" wire:model.blur="profilePassword" class="{{ $inputClass }} pl-8" />
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

                            @if ($bizError)
                                <div class="rounded-xl bg-red-50 border border-red-100 p-3 text-center text-xs font-semibold text-red-600">{{ $bizError }}</div>
                            @endif
                            @if ($bizSuccess)
                                <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center text-xs font-semibold text-emerald-600">{{ $bizSuccess }}</div>
                            @endif


                            {{-- Pending: content moderation review, no payment involved --}}
                            @if ($business && $business->status === 'pending')
                                <div class="rounded-xl bg-amber-50 border border-amber-100 p-5 flex items-start gap-3">
                                    <x-icon name="clock" class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" />
                                    <div>
                                        <p class="font-bold text-amber-800">Your Business is Under Review</p>
                                        <p class="font-medium text-xs mt-1 text-amber-700">Our team is reviewing your business details. You'll be notified once it's approved.</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Rejected: reason banner, then the same form below to resubmit --}}
                            @if ($business && $business->status === 'rejected')
                                <div class="rounded-xl bg-red-50 border border-red-100 p-4 flex items-start gap-3 mb-5">
                                    <x-icon name="x-circle" class="h-5 w-5 text-red-600 shrink-0 mt-0.5" />
                                    <div>
                                        <p class="font-bold text-red-800">{{ __('site.profile.payment_rejected_title') }}</p>
                                        <p class="font-bold text-xs mt-1 text-red-700">{{ __('site.profile.biz_reason') }}: <span class="underline">{{ $business->rejection_reason ?: __('site.profile.rejected_default_reason') }}</span></p>
                                        <p class="font-medium text-xs mt-2 text-red-700">Please review and resubmit your details below.</p>
                                    </div>
                                </div>
                            @endif

                            {{-- No business yet, rejected, or approved: show the form (approved additionally gets a view/edit toggle) --}}
                            @if (! $business || $business->status !== 'pending')
                                <div class="space-y-6">
                                    @if (!$business?->description && $business?->status === 'approved')
                                        <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 flex items-start gap-3">
                                            <x-icon name="check-circle-2" class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" />
                                            <div>
                                                <p class="font-bold text-emerald-800">{{ __('site.profile.approved_title') }}</p>
                                                <p class="font-medium text-xs mt-1 text-emerald-700">{{ __('site.profile.approved_desc') }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($business && $business->status === 'approved' && !$isEditingBusiness)
                                        {{-- View mode --}}
                                        <div class="space-y-8">
                                            @php
                                                $bizBanner = category_cover_image(media_url($business->cover_image), $business->category);
                                            @endphp
                                            <div class="relative rounded-2xl overflow-hidden border border-border aspect-[3.2/1] max-h-[320px] min-h-[160px] bg-slate-900 shadow-sm">
                                                <x-safe-image :src="$bizBanner" alt="Business Cover" :title="$business->name" fallback-type="banner" img-class="h-full w-full object-cover opacity-85" />
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
                                        <form wire:submit="submitBusiness" x-on:keydown.enter="$event.target.tagName !== 'TEXTAREA' && $event.preventDefault()" class="space-y-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <label class="{{ $labelClass }}">{{ __('site.profile.biz_name') }}</label>
                                                    <input type="text" required wire:model.blur="bizName" placeholder="E.g. Vertex Solutions" class="{{ $inputClass }}" />
                                                </div>
                                                <div>
                                                    <label class="{{ $labelClass }}">{{ __('site.profile.biz_designation') }}</label>
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
                                                    <x-icon name="map-pin" class="h-3.5 w-3.5 text-primary" /> {{ __('site.profile.biz_address_section') }}
                                                </h4>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="{{ $labelClass }}">{{ __('site.profile.biz_street') }}</label>
                                                        <input type="text" wire:model.blur="bizAddress" placeholder="e.g. 402, Wall Street Business Park" class="{{ $inputClass }}" />
                                                    </div>
                                                    <div>
                                                        <label class="{{ $labelClass }}">{{ __('site.profile.biz_city') }}</label>
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
                                                        <label class="{{ $labelClass }}">{{ __('site.profile.biz_area') }}</label>
                                                        <x-searchable-select
                                                            wire-model="bizArea"
                                                            :options="$areaOptions"
                                                            :value="$bizArea"
                                                            :wire-key="'biz-area-' . $bizCity"
                                                            placeholder="{{ $bizCity ? 'e.g. Navrangpura' : __('site.profile.select_city_first') }}"
                                                            class="{{ $inputClass }}"
                                                        />
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="{{ $labelClass }}">{{ __('site.profile.biz_state') }}</label>
                                                        <input type="text" wire:model="bizState" value="Gujarat" readonly class="{{ $inputClass }} bg-slate-50 text-slate-600 font-bold cursor-not-allowed select-none" />
                                                    </div>
                                                    <div>
                                                        <label class="{{ $labelClass }}">{{ __('site.profile.biz_pincode') }}</label>
                                                        <input type="text" wire:model.live="bizPincode" placeholder="e.g. 380009" class="{{ $inputClass }}" />
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="{{ $labelClass }}">{{ __('site.profile.biz_map_iframe') }}</label>
                                                    <textarea rows="3" wire:model.blur="bizMapIframe" placeholder='Paste Google Maps Embed HTML iframe code or map URL' class="{{ $inputClass }} resize-none font-mono text-[12px]"></textarea>
                                                    <p class="text-[12px] text-muted-foreground mt-1 font-medium">{{ __('site.profile.biz_map_tip') }}</p>
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
                                                 <textarea rows="4" required wire:model.blur="bizDescription" placeholder="{{ __('site.profile.biz_desc_placeholder') }}" class="{{ $inputClass }} resize-none"></textarea>
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
                                                         {{ __('site.profile.biz_no_services') }}
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
                                                                         <label class="text-[12px] font-bold text-muted-foreground uppercase mb-0.5 block">{{ __('site.profile.service_title') }}</label>
                                                                         <input type="text" required wire:model.blur="bizServices.{{ $idx }}.title" placeholder="e.g. Cloud Migration" class="{{ $inputClass }}" />
                                                                     </div>
                                                                     <div>
                                                                         <label class="text-[12px] font-bold text-muted-foreground uppercase mb-0.5 block">{{ __('site.profile.service_description') }}</label>
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
                                                         <label class="{{ $labelClass }}">{{ __('site.profile.biz_email_label') }}</label>
                                                         <input type="email" wire:model.blur="bizEmail" placeholder="{{ $user->email ?: 'e.g. contact@vertex.solutions' }}" class="{{ $inputClass }}" />
                                                     </div>
                                                     <div>
                                                         <label class="{{ $labelClass }}">{{ __('site.profile.biz_phone_label') }}</label>
                                                         <input type="text" maxlength="10" wire:model.live="bizPhone" placeholder="{{ $user->phone ?: __('site.profile.digit_mobile_number') }}" class="{{ $inputClass }}" />
                                                     </div>
                                                     <div>
                                                         <label class="{{ $labelClass }}">{{ __('site.profile.biz_phone2_label') }}</label>
                                                         <input type="text" maxlength="10" wire:model.live="bizPhone2" placeholder="{{ __('site.profile.optional_2nd_number') }}" class="{{ $inputClass }}" />
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
                                                         <label class="{{ $labelClass }}">{{ __('site.profile.biz_whatsapp_label') }}</label>
                                                         <div class="relative"><x-brand-icon name="whatsapp" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" /><input type="text" maxlength="10" wire:model.live="bizWhatsapp" placeholder="{{ __('site.profile.digit_whatsapp_number') }}" class="{{ $inputClass }} pl-10" /></div>
                                                     </div>
                                                 </div>
                                             </div>

                                             <div class="space-y-4 border-t border-border pt-4">
                                                 <h4 class="text-sm font-bold text-foreground">{{ __('site.profile.branding_photos') }}</h4>
                                                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                     {{-- Business Logo Box --}}
                                                     <div>
                                                         <label class="{{ $labelClass }}">{{ __('site.profile.biz_logo') }}</label>
                                                         <div
                                                             class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-4 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative min-h-[160px]"
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
                                                                         window.dispatchEvent(new CustomEvent('open-cropper', {
                                                                             detail: {
                                                                                 src: URL.createObjectURL(f),
                                                                                 aspectRatio: 1,
                                                                                 title: 'Adjust / Crop Business Logo',
                                                                                 target: 'logoFile',
                                                                                 componentId: $el.closest('[wire\\:id]')?.getAttribute('wire:id')
                                                                             }
                                                                         }));
                                                                         $event.target.value = '';
                                                                     }
                                                                 "
                                                             />

                                                             <div wire:loading wire:target="logoFile" class="flex flex-col items-center gap-2 py-4">
                                                                 <x-icon name="loader-2" class="h-6 w-6 text-primary animate-spin" />
                                                                 <span class="text-xs font-bold text-primary">Applying & uploading logo...</span>
                                                             </div>

                                                             <div wire:loading.remove wire:target="logoFile" class="w-full flex flex-col items-center">
                                                                 @if ($logoFile)
                                                                     <div class="flex flex-col items-center gap-2">
                                                                         <div class="h-20 w-20 rounded-2xl border border-primary/40 overflow-hidden bg-white p-1.5 shadow-sm shrink-0 flex items-center justify-center">
                                                                             <img src="{{ $logoFile->temporaryUrl() }}" alt="Cropped Logo Preview" class="max-h-full max-w-full object-contain rounded-xl" />
                                                                         </div>
                                                                         <span class="text-[11px] font-bold text-primary bg-primary-soft px-2 py-0.5 rounded-full">{{ __('site.profile.logo_adjusted') }}</span>
                                                                     </div>
                                                                 @elseif ($logoPreview)
                                                                     <div class="flex flex-col items-center gap-2">
                                                                         <div class="h-20 w-20 rounded-2xl border border-border overflow-hidden bg-white p-1.5 shadow-sm shrink-0 flex items-center justify-center">
                                                                             <img src="{{ $logoPreview }}" alt="Current Logo" class="max-h-full max-w-full object-contain rounded-xl" />
                                                                         </div>
                                                                         <span class="text-[11px] font-bold text-slate-600 hover:text-primary">{{ __('site.profile.click_to_crop_replace') }}</span>
                                                                     </div>
                                                                 @else
                                                                     <x-icon name="upload" class="h-6 w-6 text-primary mb-2" />
                                                                     <span class="text-[12px] font-semibold text-foreground text-center">{{ __('site.profile.choose_logo') }}</span>
                                                                     <span class="text-[11px] text-muted mt-1">{{ __('site.profile.logo_hint') }}</span>
                                                                 @endif
                                                             </div>
                                                         </div>
                                                         @error('logoFile') <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                                                     </div>

                                                     {{-- Business Cover Banner Box --}}
                                                     <div>
                                                         <label class="{{ $labelClass }}">{{ __('site.profile.biz_cover') }}</label>
                                                         <div
                                                             class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-4 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative min-h-[160px]"
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
                                                                         window.dispatchEvent(new CustomEvent('open-cropper', {
                                                                             detail: {
                                                                                 src: URL.createObjectURL(f),
                                                                                 aspectRatio: 3.2 / 1,
                                                                                 title: 'Adjust / Crop Cover Banner',
                                                                                 target: 'coverFile',
                                                                                 componentId: $el.closest('[wire\\:id]')?.getAttribute('wire:id')
                                                                             }
                                                                         }));
                                                                         $event.target.value = '';
                                                                     }
                                                                 "
                                                             />

                                                             <div wire:loading wire:target="coverFile" class="flex flex-col items-center gap-2 py-4">
                                                                 <x-icon name="loader-2" class="h-6 w-6 text-primary animate-spin" />
                                                                 <span class="text-xs font-bold text-primary">Applying & uploading cover...</span>
                                                             </div>

                                                             <div wire:loading.remove wire:target="coverFile" class="w-full flex flex-col items-center">
                                                                 @if ($coverFile)
                                                                     <div class="flex flex-col items-center gap-2 w-full">
                                                                         <div class="h-20 w-full max-w-[240px] rounded-2xl border border-primary/40 overflow-hidden bg-slate-900 shadow-sm shrink-0 flex items-center justify-center">
                                                                             <img src="{{ $coverFile->temporaryUrl() }}" alt="Cropped Cover Preview" class="h-full w-full object-cover" />
                                                                         </div>
                                                                         <span class="text-[11px] font-bold text-primary bg-primary-soft px-2 py-0.5 rounded-full">{{ __('site.profile.banner_adjusted') }}</span>
                                                                     </div>
                                                                 @elseif ($coverPreview)
                                                                     <div class="flex flex-col items-center gap-2 w-full">
                                                                         <div class="h-20 w-full max-w-[240px] rounded-2xl border border-border overflow-hidden bg-slate-900 shadow-sm shrink-0 flex items-center justify-center">
                                                                             <img src="{{ $coverPreview }}" alt="Current Cover" class="h-full w-full object-cover" />
                                                                         </div>
                                                                         <span class="text-[11px] font-bold text-slate-600 hover:text-primary">{{ __('site.profile.click_to_crop_replace') }}</span>
                                                                     </div>
                                                                 @else
                                                                     <x-icon name="upload" class="h-6 w-6 text-primary mb-2" />
                                                                     <span class="text-[12px] font-semibold text-foreground text-center">{{ __('site.profile.choose_cover') }}</span>
                                                                     <span class="text-[11px] text-muted mt-1">{{ __('site.profile.cover_hint') }}</span>
                                                                 @endif
                                                             </div>
                                                         </div>
                                                         @error('coverFile') <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                                                     </div>
                                                 </div>
                                             </div>

                                             <div class="flex gap-4 pt-4 border-t border-border">
                                                 @if ($business && $business->status === 'approved')
                                                     <button type="button" wire:click="$set('isEditingBusiness', false)" class="flex-1 rounded-xl border border-border bg-white px-4 py-3 text-sm font-semibold text-foreground transition-colors hover:bg-surface cursor-pointer">
                                                         {{ __('site.profile.biz_cancel') }}
                                                     </button>
                                                 @endif
                                                 <button type="submit" wire:loading.attr="disabled" wire:target="submitBusiness" class="flex-[2] inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer">
                                                     <span wire:loading.remove wire:target="submitBusiness">
                                                         @if ($business && $business->status === 'approved') {{ __('site.profile.biz_save_details') }}
                                                         @elseif ($business && $business->status === 'rejected') {{ __('site.profile.resubmit_review') }}
                                                         @else {{ __('site.profile.submit_review') }} @endif
                                                     </span>
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
                    <div class="space-y-4">
                        <div class="glass-card p-4 sm:p-5 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-border pb-3">
                                <div>
                                    <h2 class="text-sm sm:text-base font-bold text-foreground flex items-center gap-2">
                                        <x-icon name="calendar" class="h-4 w-4 text-primary" /> {{ __('site.profile.events_title') }}
                                        <span class="inline-flex items-center justify-center rounded-full bg-primary-soft text-primary border border-primary/10 px-2 py-0.5 text-[11px] font-bold">
                                            {{ $registeredEvents->count() }}
                                        </span>
                                    </h2>
                                    <p class="mt-0.5 text-xs text-muted">{{ __('site.profile.events_subtitle') }}</p>
                                </div>
                                <a href="/events" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:text-primary-dark transition-colors self-start sm:self-auto">
                                    <span>Browse All Events</span>
                                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                                </a>
                            </div>

                            @if ($registeredEvents->isEmpty())
                                <div class="rounded-2xl border border-dashed border-border py-12 px-4 text-center space-y-3 bg-surface/20">
                                    <div class="h-12 w-12 rounded-2xl bg-primary-soft text-primary flex items-center justify-center mx-auto">
                                        <x-icon name="calendar" class="h-6 w-6" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-foreground">{{ __('site.profile.no_events') }}</p>
                                        <p class="text-xs text-muted mt-1">You have not registered for any upcoming events yet.</p>
                                    </div>
                                    <a href="/events" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-95 transition-all">
                                        <span>Explore Events</span>
                                        <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                                    </a>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach ($registeredEvents as $evt)
                                         @continue(!$evt->event)
                                         @php
                                             $eventDetails = $evt->event;
                                             $ticketPrice = (float) $evt->amount_paid === 0.0 ? 'Free' : '₹' . number_format((float) $evt->amount_paid);
                                             $isApproved = in_array($evt->status, ['approved', 'confirmed'], true);
                                             $isRejected = $evt->status === 'rejected';
                                         @endphp
                                         <div class="rounded-2xl border border-border bg-white hover:border-primary/40 hover:shadow-sm transition-all duration-200 overflow-hidden flex flex-col justify-between group">
                                             <div>
                                                 {{-- Event Image: Compact Height with blur-backdrop and object-contain --}}
                                                 <div class="relative w-full h-32 sm:h-36 bg-slate-950 overflow-hidden flex items-center justify-center">
                                                     <x-safe-image
                                                         :src="media_url($eventDetails->image)"
                                                         :alt="$eventDetails->title"
                                                         :title="$eventDetails->title"
                                                         :date="$eventDetails->date"
                                                         :blur-backdrop="true"
                                                         fallback-type="event"
                                                     />

                                                     {{-- Date Badge Overlay --}}
                                                     <div class="absolute top-2 left-2 flex flex-col items-center justify-center rounded-md bg-white/95 backdrop-blur-sm border border-white/60 shadow-xs px-1.5 py-0.5 min-w-[36px]">
                                                         <span class="text-[8px] font-extrabold uppercase tracking-wider text-primary">{{ $eventDetails->date?->format('M') }}</span>
                                                         <span class="text-xs font-black text-slate-900 leading-none">{{ $eventDetails->date?->format('d') }}</span>
                                                     </div>

                                                     {{-- Status Badges Floating Top Right --}}
                                                     <div class="absolute top-2 right-2 flex flex-col items-end gap-1">
                                                         <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider shadow-xs {{ $isApproved ? 'bg-emerald-600 text-white' : ($isRejected ? 'bg-red-600 text-white' : 'bg-amber-500 text-white') }}">
                                                             @if ($isApproved)
                                                                 <x-icon name="check-circle-2" class="h-2.5 w-2.5" /> {{ __('site.profile.event_confirmed') }}
                                                             @elseif ($isRejected)
                                                                 <x-icon name="x-circle" class="h-2.5 w-2.5" /> {{ __('site.bookingDetail.status_rejected') }}
                                                             @else
                                                                 <x-icon name="clock" class="h-2.5 w-2.5" /> {{ __('site.profile.event_pending') }}
                                                             @endif
                                                         </span>

                                                         @if ($evt->is_attended)
                                                             <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider shadow-xs">
                                                                 <x-icon name="check-circle-2" class="h-2 w-2 text-emerald-700" /> Attended
                                                             </span>
                                                         @endif
                                                     </div>

                                                     @if ($eventDetails->type)
                                                         <span class="absolute bottom-2 left-2 inline-flex items-center rounded bg-black/70 backdrop-blur-sm text-white text-[9px] font-bold px-1.5 py-0.5 border border-white/20">
                                                             {{ $eventDetails->type }}
                                                         </span>
                                                     @endif
                                                 </div>

                                                 {{-- Card Content Body --}}
                                                 <div class="p-3 space-y-2">
                                                     <div>
                                                         <h3 class="text-xs sm:text-sm font-bold text-foreground group-hover:text-primary transition-colors line-clamp-1 leading-snug" title="{{ $eventDetails->title }}">
                                                             {{ $eventDetails->title }}
                                                         </h3>
                                                         <p class="text-[10px] text-muted-foreground font-medium mt-0.5">
                                                             Booked on {{ $evt->created_at?->format('M d, Y') ?? 'Recent' }}
                                                         </p>
                                                     </div>

                                                     <div class="grid grid-cols-2 gap-1.5 text-xs">
                                                         <div class="rounded-lg border border-border/80 bg-surface/40 p-1.5 px-2 space-y-0.5">
                                                             <span class="text-[9px] font-bold text-muted uppercase tracking-wider flex items-center gap-1">
                                                                 <x-icon name="map-pin" class="h-2.5 w-2.5 text-primary" /> Location
                                                             </span>
                                                             <p class="text-[11px] font-bold text-foreground truncate" title="{{ $eventDetails->location }}">
                                                                 {{ $eventDetails->location ?: 'Online / TBA' }}
                                                             </p>
                                                         </div>

                                                         <div class="rounded-lg border border-border/80 bg-surface/40 p-1.5 px-2 space-y-0.5">
                                                             <span class="text-[9px] font-bold text-muted uppercase tracking-wider flex items-center gap-1">
                                                                 <x-icon name="credit-card" class="h-2.5 w-2.5 text-primary" /> Amount
                                                             </span>
                                                             <p class="text-[11px] font-extrabold {{ (float) $evt->amount_paid > 0 ? 'text-emerald-600' : 'text-foreground' }}">
                                                                 {{ $ticketPrice }}
                                                             </p>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>

                                             {{-- Footer CTA --}}
                                             <div class="p-3 pt-0">
                                                 <a
                                                     href="/profile/events/{{ $evt->id }}"
                                                     class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white shadow-xs hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer"
                                                 >
                                                     <span>{{ __('site.profile.view_details') }} / Pass</span>
                                                     <x-icon name="arrow-right" class="h-3 w-3 transition-transform group-hover:translate-x-0.5" />
                                                 </a>
                                             </div>
                                         </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Visitor Pass Section --}}
                @if ($activeTab === 'visitor-pass')
                    <div class="glass-card p-4">
                        @livewire('profile.visitor-passes')
                    </div>
                @endif

                {{-- My Overview Section --}}
                @if ($activeTab === 'overview' || $activeTab === 'analytics')
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
