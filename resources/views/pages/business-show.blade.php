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
                    window.location.href = '/login';
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

        {{-- Split Body Layout (Matches Event Details Design) --}}
        <div class="mx-auto max-w-7xl px-6 py-6 lg:px-4">
            <div class="grid grid-cols-1 gap-2 lg:grid-cols-3">

                {{-- Left Column (Main Content) --}}
                <div class="space-y-3 lg:col-span-2">
                    @if ($business->description)
                        <section class="glass-card p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.businessDetail.about_company') }}</h2>
                            </div>
                            <p class="text-xs sm:text-sm leading-relaxed text-muted font-medium break-words [overflow-wrap:anywhere] whitespace-pre-wrap">{{ $business->description }}</p>
                        </section>
                    @endif

                    @if ($business->founded || $business->team_size || $business->category)
                        <section class="glass-card p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.businessDetail.company_overview') }}</h2>
                            </div>
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                @if ($business->founded)
                                    <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                                            <x-icon name="calendar" class="h-4 w-4" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold text-muted uppercase">{{ __('site.businessDetail.founded') }}</p>
                                            <p class="text-xs font-bold text-foreground leading-tight">{{ $business->founded }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if ($business->team_size)
                                    <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                                            <x-icon name="users" class="h-4 w-4" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold text-muted uppercase">{{ __('site.businessDetail.team_size') }}</p>
                                            <p class="text-xs font-bold text-foreground leading-tight">{{ $business->team_size }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if ($business->category)
                                    <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                                            <x-icon name="briefcase" class="h-4 w-4" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold text-muted uppercase">{{ __('site.businessDetail.industry') }}</p>
                                            <p class="text-xs font-bold text-foreground truncate leading-tight">{{ explode(' & ', $business->category)[0] }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </section>
                    @endif

                    @if ($services->isNotEmpty())
                        <section class="glass-card p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.businessDetail.core_services') }}</h2>
                            </div>
                            <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                                @foreach ($services as $service)
                                    <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                                            <x-icon name="zap" class="h-4 w-4" />
                                        </div>
                                        <div class="min-w-0 flex-1 space-y-0.5">
                                            <h3 class="text-xs font-bold text-foreground">{{ $service['title'] }}</h3>
                                            @if ($service['desc'])
                                                <p class="text-[12px] text-muted leading-relaxed font-medium">{{ $service['desc'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Reviews list & submit review --}}
                    <section class="glass-card p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.businessDetail.member_recommendations') }}</h2>
                            </div>
                            @if ($business->rating)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
                                    <x-icon name="star" class="h-3 w-3 fill-current text-amber-500" /> {{ $business->rating }} ({{ $business->reviews_count }})
                                </span>
                            @endif
                        </div>

                        <div class="rounded-xl border border-slate-200/80 bg-slate-50/70 p-4 space-y-3">
                            <h3 class="text-xs font-bold text-foreground inline-flex items-center gap-1.5">
                                <x-icon name="message-square" class="h-4 w-4 text-primary" /> {{ __('site.businessDetail.recommend_business') }}
                            </h3>
                            <div x-show="hasUserReviewed" x-cloak class="rounded-xl border border-dashed border-emerald-200 bg-emerald-50/50 p-4 text-center text-xs font-semibold text-emerald-800 flex items-center justify-center gap-1.5">
                                <x-icon name="check-circle-2" class="h-4 w-4 text-emerald-600" /> {{ __('site.businessDetail.already_reviewed') }}
                            </div>
                            <form x-show="!hasUserReviewed" x-cloak x-on:submit.prevent="submitReview" class="space-y-3">
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div class="space-y-1">
                                        <label class="text-[11px] font-bold text-muted uppercase">{{ __('site.businessDetail.your_name') }}</label>
                                        <input type="text" placeholder="John Doe" value="{{ auth()->user()->name ?? '' }}" @disabled(auth()->check()) class="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary disabled:bg-slate-50 disabled:text-muted font-medium" />
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[11px] font-bold text-muted uppercase">{{ __('site.businessDetail.rating') }}</label>
                                        <select x-model="reviewRating" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary font-medium">
                                            @foreach ([5, 4, 3, 2, 1] as $r)
                                                <option value="{{ $r }}">{{ $r }} {{ __('site.businessDetail.stars') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] font-bold text-muted uppercase">{{ __('site.businessDetail.recommendation_text') }}</label>
                                    <textarea required rows="3" placeholder="{{ __('site.businessDetail.recommendation_placeholder') }}" x-model="reviewContent" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary resize-none font-medium"></textarea>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-3">
                                        <button type="submit" :disabled="reviewSubmitting" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-opacity hover:opacity-90 disabled:opacity-50 cursor-pointer shadow-sm">
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

                        <div class="space-y-2.5 pt-1">
                            <template x-for="(rev, idx) in reviews" :key="idx">
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2.5">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center gap-2.5">
                                            <div class="h-8 w-8 rounded-full bg-white border border-slate-200 flex items-center justify-center font-bold text-xs text-primary shrink-0 select-none shadow-2xs" x-text="(rev.reviewer || '{{ __('site.businessDetail.anonymous_member') }}').charAt(0)"></div>
                                            <div>
                                                <h4 class="text-xs font-bold text-foreground leading-tight" x-text="rev.reviewer || '{{ __('site.businessDetail.anonymous_member') }}'"></h4>
                                                <span class="text-[11px] text-muted leading-tight mt-0.5 inline-block" x-text="rev.role || '{{ __('site.businessDetail.verified_member') }}'"></span>
                                            </div>
                                        </div>
                                        <div class="flex gap-0.5 text-amber-500 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                                            <template x-for="i in 5" :key="i">
                                                <x-icon name="star" class="h-3 w-3" x-bind:class="i <= rev.rating ? 'fill-current' : 'text-amber-200'" />
                                            </template>
                                        </div>
                                    </div>
                                    <p class="text-xs text-muted leading-relaxed font-medium bg-white p-2.5 rounded-lg border border-slate-100" x-text="'&quot;' + rev.content + '&quot;'"></p>
                                </div>
                            </template>
                        </div>
                    </section>

                    @if ($testimonials->isNotEmpty())
                        <section class="glass-card p-4 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Referral Testimonials</h2>
                            </div>
                            <p class="text-xs text-muted -mt-1 font-medium">Network-verified feedback from members who received a referral from this business and closed the deal</p>

                            <div class="space-y-2.5">
                                @foreach ($testimonials as $t)
                                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center font-bold text-xs text-emerald-800 shrink-0 select-none">
                                                {{ $t->receiver ? mb_substr($t->receiver->name, 0, 1) : '?' }}
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-bold text-foreground leading-tight">{{ $t->receiver?->name ?? 'SABHA Member' }}</h4>
                                                <span class="text-[11px] text-emerald-700 leading-tight mt-0.5 inline-flex items-center gap-1 font-semibold">
                                                    <x-icon name="check-circle-2" class="h-3 w-3" /> Verified Referral Partner
                                                </span>
                                            </div>
                                        </div>
                                        <p class="text-xs text-muted leading-relaxed font-medium bg-white p-2.5 rounded-lg border border-slate-100">&quot;{{ $t->testimonial }}&quot;</p>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                {{-- Right Column (Sticky Sidebar - Matches Event Details Sidebar) --}}
                <div class="space-y-4 lg:sticky lg:top-20 h-fit">
                    @if ($business->user)
                        <div class="glass-card p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.businessDetail.listed_by') }}</h3>
                            </div>
                            <div class="flex items-center gap-3.5 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <img src="{{ $memberAvatar }}" alt="{{ $business->user->name }}" class="h-12 w-12 rounded-full object-contain bg-slate-100 border-2 border-primary/20 shadow-xs shrink-0" />
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 truncate">{{ $business->user->name }}</h4>
                                    @if ($business->user->memberTitle)
                                        <div class="mt-0.5">
                                            <x-member-title-badge :title="$business->user->memberTitle" />
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 mt-0.5">
                                            <x-icon name="shield-check" class="h-3 w-3" /> {{ __('site.businessDetail.verified_member') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($business->website || $business->business_email || $business->business_phone || $business->phone2 || $business->linkedin || $business->instagram || $business->youtube || $business->twitter || $business->whatsapp)
                        <div class="glass-card p-4 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.businessDetail.contact_channels') }}</h3>
                            </div>
                            <div class="space-y-2">
                                @if ($business->website)
                                    <a href="{{ $business->website }}" target="_blank" class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 border border-slate-100 hover:bg-primary-soft transition-colors group">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-primary border border-slate-200">
                                            <x-icon name="globe" class="h-4 w-4" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[11px] font-semibold text-muted uppercase leading-none">{{ __('site.businessDetail.website') }}</p>
                                            <p class="mt-0.5 text-xs font-bold text-foreground group-hover:text-primary transition-colors truncate">
                                                {{ preg_replace('#^https?://#', '', $business->website) }}
                                            </p>
                                        </div>
                                    </a>
                                @endif
                                @if ($business->business_email)
                                    <a href="mailto:{{ $business->business_email }}" class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 border border-slate-100 hover:bg-primary-soft transition-colors group">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-primary border border-slate-200">
                                            <x-icon name="mail" class="h-4 w-4" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[11px] font-semibold text-muted uppercase leading-none">{{ __('site.businessDetail.email_address') }}</p>
                                            <p class="mt-0.5 text-xs font-bold text-foreground group-hover:text-primary transition-colors truncate">{{ $business->business_email }}</p>
                                        </div>
                                    </a>
                                @endif
                                @if ($business->business_phone)
                                    <a href="tel:{{ $business->business_phone }}" class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 border border-slate-100 hover:bg-primary-soft transition-colors group">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-primary border border-slate-200">
                                            <x-icon name="phone" class="h-4 w-4" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[11px] font-semibold text-muted uppercase leading-none">{{ __('site.businessDetail.direct_phone') }}</p>
                                            <p class="mt-0.5 text-xs font-bold text-foreground group-hover:text-primary transition-colors truncate">{{ $business->business_phone }}</p>
                                        </div>
                                    </a>
                                @endif
                                @if ($business->phone2)
                                    <a href="tel:{{ $business->phone2 }}" class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 border border-slate-100 hover:bg-primary-soft transition-colors group">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-primary border border-slate-200">
                                            <x-icon name="phone" class="h-4 w-4" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[11px] font-semibold text-muted uppercase leading-none">{{ __('site.businessDetail.alternate_phone') }}</p>
                                            <p class="mt-0.5 text-xs font-bold text-foreground group-hover:text-primary transition-colors truncate">{{ $business->phone2 }}</p>
                                        </div>
                                    </a>
                                @endif
                                @if ($business->linkedin)
                                    <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 border border-slate-100">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-primary border border-slate-200">
                                            <x-icon name="share-2" class="h-4 w-4" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[11px] font-semibold text-muted uppercase leading-none">{{ __('site.businessDetail.linkedin_url') }}</p>
                                            <span class="mt-0.5 text-xs font-bold text-foreground block truncate">{{ $business->linkedin }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($business->instagram || $business->youtube || $business->twitter || $business->linkedin || $business->whatsapp)
                                <div class="pt-2.5 border-t border-slate-100">
                                    <p class="text-[11px] font-bold text-muted uppercase tracking-wider mb-2">{{ __('site.businessDetail.social_channels') }}</p>
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($business->instagram)
                                            <a href="{{ $business->instagram }}" target="_blank" rel="noreferrer" class="h-8 w-8 rounded-lg bg-gradient-to-br from-pink-500 via-red-500 to-yellow-500 text-white flex items-center justify-center transition-transform hover:scale-110 shadow-xs" title="Instagram">
                                                <x-brand-icon name="instagram" class="h-4 w-4" />
                                            </a>
                                        @endif
                                        @if ($business->youtube)
                                            <a href="{{ $business->youtube }}" target="_blank" rel="noreferrer" class="h-8 w-8 rounded-lg bg-red-600 text-white flex items-center justify-center transition-transform hover:scale-110 shadow-xs" title="YouTube">
                                                <x-brand-icon name="youtube" class="h-4 w-4" />
                                            </a>
                                        @endif
                                        @if ($business->twitter)
                                            <a href="{{ $business->twitter }}" target="_blank" rel="noreferrer" class="h-8 w-8 rounded-lg bg-black text-white flex items-center justify-center transition-transform hover:scale-110 shadow-xs" title="Twitter / X">
                                                <x-brand-icon name="twitter" class="h-4 w-4" />
                                            </a>
                                        @endif
                                        @if ($business->linkedin)
                                            <a href="{{ $business->linkedin }}" target="_blank" rel="noreferrer" class="h-8 w-8 rounded-lg bg-[#0A66C2] text-white flex items-center justify-center transition-transform hover:scale-110 shadow-xs" title="LinkedIn">
                                                <x-brand-icon name="linkedin" class="h-4 w-4" />
                                            </a>
                                        @endif
                                        @if ($business->whatsapp)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}" target="_blank" rel="noreferrer" class="h-8 w-8 rounded-lg bg-[#25D366] text-white flex items-center justify-center transition-transform hover:scale-110 shadow-xs" title="WhatsApp">
                                                <x-brand-icon name="whatsapp" class="h-4 w-4" />
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($hasAddressDetails)
                        <div class="glass-card p-4 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="h-4 w-[3px] rounded-full bg-primary"></span>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">{{ __('site.businessDetail.geographic_location') }}</h3>
                            </div>
                            @if ($fullAddr)
                                <div class="flex items-start gap-2.5 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <x-icon name="map-pin" class="h-4 w-4 text-primary shrink-0 mt-0.5" />
                                    <p class="text-xs font-bold text-slate-900 leading-snug">{{ $fullAddr }}</p>
                                </div>
                            @endif
                            @if ($mapSrc)
                                <div class="h-44 w-full rounded-xl border border-border overflow-hidden relative shadow-2xs bg-slate-900">
                                    <iframe src="{{ $mapSrc }}" class="w-full h-full border-0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="rounded-2xl p-4 bg-gradient-to-br from-[#0F3459] to-[#00379D] text-white border border-blue-900/30 shadow-md space-y-2.5">
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/20 text-white">
                                <x-icon name="shield-check" class="h-4 w-4" />
                            </div>
                            <h4 class="text-xs font-extrabold text-white">{{ __('site.businessDetail.vetted_member') }}</h4>
                        </div>
                        <p class="text-[11px] leading-relaxed text-white/90 font-medium">{{ __('site.businessDetail.vetted_desc') }}</p>
                        <div class="inline-flex items-center gap-1.5 rounded-lg bg-white/20 px-2.5 py-1 text-[11px] font-bold text-white">
                            <x-icon name="check-circle-2" class="h-3.5 w-3.5 text-emerald-300" />
                            <span>{{ __('site.businessDetail.verified_profile') }}</span>
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
