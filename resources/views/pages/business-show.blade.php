@unless ($business)
    <x-layouts.app :title="__('site.businessDetail.not_found_title') . ' | Sabha'" :noindex="true">
        <div class="min-h-screen flex flex-col items-center justify-center bg-background font-outfit text-center p-6">
            <h2 class="text-2xl font-bold text-foreground">{{ __('site.businessDetail.not_found_title') }}</h2>
            <p class="mt-2 text-sm text-muted">{{ __('site.businessDetail.not_found_desc') }}</p>
            <a href="/businesses" class="mt-6 inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98]">
                {{ __('site.businessDetail.back_to_directory') }}
            </a>
        </div>
    </x-layouts.app>
@else
@php
    $bannerImage = category_cover_image(media_url($business->cover_image), $business->category);
    $logoUrl = media_url($business->logo);
    $memberAvatar = $business->user && $business->user->avatar
        ? media_url($business->user->avatar)
        : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=150&auto=format&fit=crop';
    $mapSrc = parse_google_maps_iframe_src($business->map_iframe);
    $addressParts = array_filter([$business->address, $business->area, $business->state]);
    $fullAddr = count($addressParts) > 0
        ? implode(', ', $addressParts) . ($business->pincode ? " - {$business->pincode}" : '')
        : null;
    $hasAddressDetails = filled($fullAddr) || filled($mapSrc);
    $metaDescription = $business->tagline ?: \Illuminate\Support\Str::limit(strip_tags((string) $business->description), 160);
@endphp

<x-layouts.app
    :title="$business->name . ' | Sabha'"
    :description="$metaDescription ?: __('site.directory.subtitle')"
    :image="$logoUrl ?: $bannerImage"
>
    <div
        class="min-h-screen bg-background font-outfit"
        x-data="{
            reviewSubmitting: false,
            reviewSubmitted: false,
            reviewError: '',
            reviewContent: '',
            reviewRating: 5,
            hasUserReviewed: {{ $hasUserReviewed ? 'true' : 'false' }},
            reviews: {{ Illuminate\Support\Js::from($reviews->map(fn ($r) => ['reviewer' => $r->user->name ?? null, 'role' => null, 'content' => $r->content, 'rating' => $r->rating])) }},
            async submitReview() {
                @if (!auth()->check())
                    $store.auth.openLogin();
                    return;
                @endif
                if (!this.reviewContent.trim() || this.reviewContent.trim().length < 5) {
                    this.reviewError = 'Recommendation text must be at least 5 characters long.';
                    return;
                }
                this.reviewSubmitting = true;
                this.reviewError = '';
                try {
                    const res = await fetch('/businesses/{{ $business->id }}/reviews', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ rating: this.reviewRating, content: this.reviewContent }),
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Failed to submit review.');
                    this.reviews.unshift(data.review);
                    this.reviewSubmitted = true;
                    this.reviewContent = '';
                    this.hasUserReviewed = true;
                    setTimeout(() => { this.reviewSubmitted = false }, 5000);
                } catch (e) {
                    this.reviewError = e.message;
                } finally {
                    this.reviewSubmitting = false;
                }
            },
        }"
    >
        {{-- Cover Banner with Profile Bar --}}
        <div class="mx-auto max-w-7xl sm:px-6 sm:pt-6">
            <section class="relative w-full aspect-[3.2/1] sm:rounded-3xl overflow-hidden bg-slate-950 flex items-center justify-center shadow-lg border border-border/40">
                <x-safe-image :src="$bannerImage" :alt="$business->name . ' workspace'" :title="$business->name" fallback-type="banner" img-class="h-full w-full object-cover object-center" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent pointer-events-none"></div>

                <div class="absolute top-6 left-6 z-10">
                    <a href="/businesses" class="group inline-flex items-center gap-1.5 rounded-lg bg-black/30 backdrop-blur-md px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:bg-black/50 cursor-pointer">
                        <x-icon name="arrow-left" class="h-4 w-4 transition-transform group-hover:-translate-x-0.5" />
                        {{ __('site.businessDetail.back_to_directory') }}
                    </a>
                </div>

                <div class="absolute bottom-0 left-0 right-0 z-10">
                    <div class="px-6 pb-6">
                        <div class="flex flex-col md:flex-row md:items-end gap-5">
                            <div class="h-24 w-24 sm:h-28 sm:w-28 rounded-2xl overflow-hidden bg-white text-primary text-4xl sm:text-5xl font-extrabold flex items-center justify-center border-4 border-white/20 shadow-2xl shrink-0 select-none backdrop-blur-sm">
                                <x-safe-image :src="$logoUrl" :alt="$business->name" :title="$business->name" fallback-type="business" img-class="h-full w-full object-contain" />
                            </div>

                            <div class="flex-1 space-y-1.5 md:pb-1">
                                <div class="flex flex-wrap items-center gap-2.5">
                                    @if ($business->is_verified)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-white/15 backdrop-blur-sm px-2.5 py-0.5 text-[12px] font-bold text-white border border-white/20">
                                            <x-icon name="shield-check" class="h-3 w-3" /> {{ __('site.businessDetail.verified') }}
                                        </span>
                                    @endif
                                    @if ($business->rating)
                                        <span class="inline-flex items-center gap-1 text-[12px] font-bold text-amber-300 bg-amber-500/15 backdrop-blur-sm px-2.5 py-0.5 rounded-full border border-amber-400/20">
                                            <x-icon name="star" class="h-3 w-3 fill-current" /> {{ $business->rating }} ({{ $business->reviews_count }})
                                        </span>
                                    @endif
                                </div>
                                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight drop-shadow-lg">{{ $business->name }}</h1>
                                @if ($business->tagline)
                                    <p class="text-sm font-semibold text-white/70">{{ $business->tagline }}</p>
                                @endif
                                <div class="flex flex-wrap gap-x-4 gap-y-1 text-[12px] text-white/60 font-medium">
                                    @if ($business->user)
                                        <span class="inline-flex items-center gap-1"><x-icon name="user" class="h-3 w-3" /> {{ __('site.businessDetail.owner_prefix') }} {{ $business->user->name }}</span>
                                    @endif
                                    @if ($business->area)
                                        <span class="inline-flex items-center gap-1"><x-icon name="map-pin" class="h-3 w-3" /> {{ $business->area }}</span>
                                    @endif
                                    @if ($business->category)
                                        <span class="inline-flex items-center gap-1"><x-icon name="briefcase" class="h-3 w-3" /> {{ $business->category }}</span>
                                    @endif
                                    @if ($business->hours)
                                        <span class="inline-flex items-center gap-1"><x-icon name="clock" class="h-3 w-3" /> {{ __('site.businessDetail.open') }} {{ explode(' (', $business->hours)[0] }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex gap-2 shrink-0 md:pb-1">
                                @if ($business->business_phone)
                                    <a href="tel:{{ $business->business_phone }}" class="group inline-flex items-center justify-center gap-1.5 rounded-xl bg-white px-5 py-3 text-xs font-bold text-slate-900 shadow-lg transition-all hover:bg-primary hover:text-white active:scale-[0.98]">
                                        <x-icon name="phone" class="h-3.5 w-3.5" /> {{ __('site.businessDetail.connect_now') }}
                                    </a>
                                @endif
                                @if ($business->business_email)
                                    <a href="mailto:{{ $business->business_email }}" class="flex items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm border border-white/20 px-3.5 py-3 text-white transition-colors hover:bg-white/25 shadow-sm" aria-label="Email" title="Send Email">
                                        <x-icon name="mail" class="h-4 w-4" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- Main Grid Content --}}
        <div class="mx-auto max-w-7xl px-6 py-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- Main Column --}}
                <div class="space-y-6 lg:col-span-2">
                    @if ($business->description)
                        <section class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="h-4 w-1 rounded-full bg-primary"></span>
                                <h2 class="text-base font-bold text-foreground">{{ __('site.businessDetail.about_company') }}</h2>
                            </div>
                            <p class="text-xs leading-relaxed text-muted break-words [overflow-wrap:anywhere] whitespace-pre-wrap">{{ $business->description }}</p>
                        </section>
                    @endif

                    @if ($business->founded || $business->team_size || $business->category)
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 pt-2">
                            @if ($business->founded)
                                <div class="glass-card p-3 text-center">
                                    <span class="text-[12px] font-semibold uppercase tracking-wider text-muted">{{ __('site.businessDetail.founded') }}</span>
                                    <p class="mt-0.5 text-xs font-bold text-foreground">{{ $business->founded }}</p>
                                </div>
                            @endif
                            @if ($business->team_size)
                                <div class="glass-card p-3 text-center">
                                    <span class="text-[12px] font-semibold uppercase tracking-wider text-muted">{{ __('site.businessDetail.team_size') }}</span>
                                    <p class="mt-0.5 text-xs font-bold text-foreground">{{ $business->team_size }}</p>
                                </div>
                            @endif
                            @if ($business->category)
                                <div class="glass-card p-3 text-center">
                                    <span class="text-[12px] font-semibold uppercase tracking-wider text-muted">{{ __('site.businessDetail.industry') }}</span>
                                    <p class="mt-0.5 text-xs font-bold text-foreground truncate">{{ explode(' & ', $business->category)[0] }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($services->isNotEmpty())
                        <section class="space-y-2.5">
                            <div class="flex items-center gap-2">
                                <span class="h-4 w-1 rounded-full bg-primary"></span>
                                <h2 class="text-base font-bold text-foreground">{{ __('site.businessDetail.core_services') }}</h2>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach ($services as $service)
                                    <div class="glass-card flex items-start gap-2.5 p-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                                            <x-icon name="zap" class="h-[14px] w-[14px]" />
                                        </div>
                                        <div class="space-y-0.5">
                                            <h3 class="text-xs font-bold text-foreground">{{ $service['title'] }}</h3>
                                            @if ($service['desc'])
                                                <p class="text-[12px] text-muted leading-normal">{{ $service['desc'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Reviews list & submit review --}}
                    <section class="space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="h-4 w-1 rounded-full bg-primary"></span>
                            <h2 class="text-base font-bold text-foreground">{{ __('site.businessDetail.member_recommendations') }}</h2>
                        </div>

                        <div class="glass-card p-4 space-y-3 bg-surface/30">
                            <h3 class="text-xs font-bold text-foreground inline-flex items-center gap-1.5">
                                <x-icon name="message-square" class="h-4 w-4 text-primary" /> {{ __('site.businessDetail.recommend_business') }}
                            </h3>
                            <div x-show="hasUserReviewed" x-cloak class="rounded-xl border border-dashed border-emerald-200 bg-emerald-50/50 p-4 text-center text-xs font-semibold text-emerald-800 flex items-center justify-center gap-1.5">
                                <x-icon name="check-circle-2" class="h-4 w-4 text-emerald-600" /> {{ __('site.businessDetail.already_reviewed') }}
                            </div>
                            <form x-show="!hasUserReviewed" x-cloak x-on:submit.prevent="submitReview" class="space-y-3">
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-muted">{{ __('site.businessDetail.your_name') }}</label>
                                        <input type="text" placeholder="John Doe" value="{{ auth()->user()->name ?? '' }}" @disabled(auth()->check()) class="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary disabled:bg-slate-50 disabled:text-muted" />
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-muted">{{ __('site.businessDetail.rating') }}</label>
                                        <select x-model="reviewRating" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary">
                                            @foreach ([5, 4, 3, 2, 1] as $r)
                                                <option value="{{ $r }}">{{ $r }} {{ __('site.businessDetail.stars') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-muted">{{ __('site.businessDetail.recommendation_text') }}</label>
                                    <textarea required rows="3" placeholder="{{ __('site.businessDetail.recommendation_placeholder') }}" x-model="reviewContent" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary resize-none"></textarea>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-3">
                                        <button type="submit" :disabled="reviewSubmitting" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white transition-opacity hover:opacity-90 disabled:opacity-50">
                                            <span x-text="reviewSubmitting ? '{{ __('site.common.loading') }}' : '{{ __('site.businessDetail.submit_review') }}'"></span>
                                        </button>
                                        <span x-show="reviewSubmitted" x-cloak x-transition class="text-xs text-green-600 font-semibold inline-flex items-center gap-1">
                                            <x-icon name="check-circle-2" class="h-[14px] w-[14px]" /> {{ __('site.businessDetail.review_submitted') }}
                                        </span>
                                    </div>
                                    <p x-show="reviewError" x-cloak x-text="reviewError" class="text-xs text-red-600 font-semibold"></p>
                                </div>
                            </form>
                        </div>

                        <div class="space-y-3.5">
                            <template x-for="(rev, idx) in reviews" :key="idx">
                                <div class="glass-card p-5 space-y-3">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-full bg-slate-100 flex items-center justify-center font-bold text-xs text-primary shrink-0 select-none" x-text="(rev.reviewer || '{{ __('site.businessDetail.anonymous_member') }}').charAt(0)"></div>
                                            <div>
                                                <h4 class="text-sm font-bold text-foreground leading-none" x-text="rev.reviewer || '{{ __('site.businessDetail.anonymous_member') }}'"></h4>
                                                <span class="text-[12px] text-muted leading-none mt-0.5 inline-block" x-text="rev.role || '{{ __('site.businessDetail.verified_member') }}'"></span>
                                            </div>
                                        </div>
                                        <div class="flex gap-0.5 text-amber-500 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200/30">
                                            <template x-for="i in 5" :key="i">
                                                <x-icon name="star" class="h-3 w-3" x-bind:class="i <= rev.rating ? 'fill-current' : 'text-amber-200'" />
                                            </template>
                                        </div>
                                    </div>
                                    <p class="text-xs text-muted leading-relaxed font-medium bg-surface/40 p-3 rounded-lg" x-text="'&quot;' + rev.content + '&quot;'"></p>
                                </div>
                            </template>
                        </div>
                    </section>

                    @if ($testimonials->isNotEmpty())
                        <section class="space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="h-4 w-1 rounded-full bg-primary"></span>
                                <h2 class="text-base font-bold text-foreground">Referral Testimonials</h2>
                            </div>
                            <p class="text-xs text-muted -mt-2">Network-verified feedback from members who received a referral from this business and closed the deal</p>

                            <div class="space-y-3.5">
                                @foreach ($testimonials as $t)
                                    <div class="glass-card p-5 space-y-3">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-full bg-emerald-50 flex items-center justify-center font-bold text-xs text-emerald-700 shrink-0 select-none">
                                                {{ $t->receiver ? mb_substr($t->receiver->name, 0, 1) : '?' }}
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-foreground leading-none">{{ $t->receiver?->name ?? 'SABHA Member' }}</h4>
                                                <span class="text-[12px] text-emerald-700 leading-none mt-0.5 inline-flex items-center gap-1">
                                                    <x-icon name="check-circle-2" class="h-2.5 w-2.5" /> Verified Referral Partner
                                                </span>
                                            </div>
                                        </div>
                                        <p class="text-xs text-muted leading-relaxed font-medium bg-surface/40 p-3 rounded-lg">&quot;{{ $t->testimonial }}&quot;</p>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                {{-- Sidebar Column --}}
                <div class="space-y-4">
                    @if ($business->user)
                        <div class="glass-card p-4 space-y-3">
                            <h3 class="border-b border-border pb-2.5 text-xs font-bold text-foreground">{{ __('site.businessDetail.listed_by') }}</h3>
                            <div class="flex items-center gap-4">
                                <img src="{{ $memberAvatar }}" alt="{{ $business->user->name }}" class="h-14 w-14 rounded-full object-cover border border-border shadow-sm shrink-0" />
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-bold text-slate-900 truncate">{{ $business->user->name }}</h4>
                                    <div class="mt-0.5">
                                        <x-member-title-badge :title="$business->user->memberTitle" fallback="SABHA Member" />
                                    </div>
                                    <span class="inline-flex items-center gap-1 text-[12px] text-muted-foreground mt-1">
                                        <x-icon name="shield-check" class="h-3 w-3 text-green-500" /> {{ __('site.businessDetail.verified_member') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($business->website || $business->business_email || $business->business_phone || $business->linkedin || $business->area)
                        <div class="glass-card p-4">
                            <h3 class="border-b border-border pb-2.5 text-xs font-bold text-foreground">{{ __('site.businessDetail.contact_channels') }}</h3>
                            <div class="mt-4 space-y-3">
                                @if ($business->website)
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="globe" class="h-[18px] w-[18px]" /></div>
                                        <div class="min-w-0">
                                            <p class="text-[12px] font-semibold text-muted leading-none">{{ __('site.businessDetail.website') }}</p>
                                            <a href="{{ $business->website }}" target="_blank" class="mt-1 text-xs font-semibold text-foreground transition-colors hover:text-primary truncate block">
                                                {{ preg_replace('#^https?://#', '', $business->website) }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                @if ($business->business_email)
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="mail" class="h-[18px] w-[18px]" /></div>
                                        <div class="min-w-0">
                                            <p class="text-[12px] font-semibold text-muted leading-none">{{ __('site.businessDetail.email_address') }}</p>
                                            <a href="mailto:{{ $business->business_email }}" class="mt-1 text-xs font-semibold text-foreground transition-colors hover:text-primary truncate block">{{ $business->business_email }}</a>
                                        </div>
                                    </div>
                                @endif
                                @if ($business->business_phone)
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="phone" class="h-[18px] w-[18px]" /></div>
                                        <div class="min-w-0">
                                            <p class="text-[12px] font-semibold text-muted leading-none">{{ __('site.businessDetail.direct_phone') }}</p>
                                            <a href="tel:{{ $business->business_phone }}" class="mt-1 text-xs font-semibold text-foreground transition-colors hover:text-primary truncate block">{{ $business->business_phone }}</a>
                                        </div>
                                    </div>
                                @endif
                                @if ($business->phone2)
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="phone" class="h-[18px] w-[18px]" /></div>
                                        <div class="min-w-0">
                                            <p class="text-[12px] font-semibold text-muted leading-none">Alternate Phone</p>
                                            <a href="tel:{{ $business->phone2 }}" class="mt-1 text-xs font-semibold text-foreground transition-colors hover:text-primary truncate block">{{ $business->phone2 }}</a>
                                        </div>
                                    </div>
                                @endif
                                @if ($business->linkedin)
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary"><x-icon name="share-2" class="h-[18px] w-[18px]" /></div>
                                        <div class="min-w-0">
                                            <p class="text-[12px] font-semibold text-muted leading-none">{{ __('site.businessDetail.linkedin_url') }}</p>
                                            <span class="mt-1 text-xs font-semibold text-foreground block truncate">{{ $business->linkedin }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($business->instagram || $business->youtube || $business->twitter || $business->linkedin || $business->whatsapp)
                                <div class="mt-5 border-t border-border pt-4">
                                    <p class="text-[12px] font-semibold text-muted mb-3 uppercase tracking-wider">{{ __('site.businessDetail.social_channels') }}</p>
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        @if ($business->instagram)
                                            <a href="{{ $business->instagram }}" target="_blank" rel="noreferrer" class="h-10 w-10 rounded-xl bg-gradient-to-br from-pink-500 via-red-500 to-yellow-500 text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Instagram">
                                                <x-brand-icon name="instagram" class="h-[18px] w-[18px]" />
                                            </a>
                                        @endif
                                        @if ($business->youtube)
                                            <a href="{{ $business->youtube }}" target="_blank" rel="noreferrer" class="h-10 w-10 rounded-xl bg-red-600 text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Youtube">
                                                <x-brand-icon name="youtube" class="h-[18px] w-[18px]" />
                                            </a>
                                        @endif
                                        @if ($business->twitter)
                                            <a href="{{ $business->twitter }}" target="_blank" rel="noreferrer" class="h-10 w-10 rounded-xl bg-black text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Twitter / X">
                                                <x-brand-icon name="twitter" class="h-[18px] w-[18px]" />
                                            </a>
                                        @endif
                                        @if ($business->linkedin)
                                            <a href="{{ $business->linkedin }}" target="_blank" rel="noreferrer" class="h-10 w-10 rounded-xl bg-[#0A66C2] text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="LinkedIn">
                                                <x-brand-icon name="linkedin" class="h-[18px] w-[18px]" />
                                            </a>
                                        @endif
                                        @if ($business->whatsapp)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}" target="_blank" rel="noreferrer" class="h-10 w-10 rounded-xl bg-[#25D366] text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="WhatsApp">
                                                <x-brand-icon name="whatsapp" class="h-[18px] w-[18px]" />
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if ($hasAddressDetails)
                                <div class="mt-6 border-t border-border pt-5 space-y-3">
                                    <p class="text-[12px] font-bold text-muted uppercase tracking-wider flex items-center gap-1">
                                        <x-icon name="map-pin" class="h-[14px] w-[14px] text-primary" /> {{ __('site.businessDetail.geographic_location') }}
                                    </p>
                                    @if ($fullAddr)
                                        <div class="flex items-start gap-2.5 bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 shadow-xs">
                                            <x-icon name="map-pin" class="h-4 w-4 text-primary shrink-0 mt-0.5" />
                                            <div class="space-y-0.5">
                                                <p class="text-xs font-extrabold text-slate-900 leading-snug">{{ $fullAddr }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($mapSrc)
                                        <div class="h-52 w-full rounded-xl border border-border overflow-hidden relative shadow-sm bg-slate-900">
                                            <iframe src="{{ $mapSrc }}" class="w-full h-full border-0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="rounded-2xl border border-border bg-primary p-5 text-white space-y-3.5">
                        <div class="flex items-center gap-2">
                            <x-icon name="award" class="h-5 w-5 text-white" />
                            <h4 class="text-sm font-bold">{{ __('site.businessDetail.vetted_member') }}</h4>
                        </div>
                        <p class="text-xs leading-relaxed text-white/80">{{ __('site.businessDetail.vetted_desc') }}</p>
                        <div class="inline-flex items-center gap-1.5 rounded-lg bg-white/15 px-3 py-1.5">
                            <x-icon name="shield-check" class="h-4 w-4 text-white" />
                            <span class="text-[12px] font-bold">{{ __('site.businessDetail.verified_profile') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($business->whatsapp || $business->business_phone)
            <a
                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->whatsapp ?: $business->business_phone) }}?text=Hi, I found your business on Sabha and would like to connect!"
                target="_blank"
                rel="noreferrer"
                class="fixed bottom-6 right-6 z-50 flex items-center gap-2.5 rounded-full bg-[#25D366] pl-4 pr-5 py-3.5 text-white shadow-xl hover:shadow-2xl transition-all hover:scale-105 active:scale-95"
                title="Chat on WhatsApp"
            >
                <span class="absolute inset-0 rounded-full bg-[#25D366] animate-ping opacity-20"></span>
                <x-brand-icon name="whatsapp" class="h-[22px] w-[22px]" />
                <span class="text-sm font-bold relative">WhatsApp</span>
            </a>
        @endif
    </div>
</x-layouts.app>
@endunless
