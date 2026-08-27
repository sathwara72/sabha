<x-layouts.app :title="__('site.trustees.title') . ' | Sabha'" :description="__('site.trustees.subtitle')">
    <div class="bg-background font-outfit">
        <x-page-header :kicker="__('site.trustees.kicker')" :title="__('site.trustees.title')" :subtitle="__('site.trustees.subtitle')" />

        <section class="mx-auto max-w-7xl px-6 py-20 lg:py-12">
            @if ($trustees->isEmpty())
                <div class="text-center py-20 text-sm text-muted">{{ __('site.trustees.empty') }}</div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($trustees as $trustee)
                        @php
                            $member = $trustee->user;
                            $biz = $member?->business?->status === 'approved' ? $member->business : null;
                            $avatar = $member && $member->avatar ? media_url($member->avatar) : null;
                        @endphp
                        <div class="glass-card p-6 border border-border space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-full overflow-hidden border-2 border-primary-soft shadow-sm shrink-0 bg-primary-soft flex items-center justify-center">
                                    @if ($avatar)
                                        <img src="{{ $avatar }}" alt="{{ $member->name }}" class="h-full w-full object-cover" />
                                    @else
                                        <span class="text-xl font-bold text-primary uppercase">{{ $member ? mb_substr($member->name, 0, 1) : '?' }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-extrabold text-foreground truncate">{{ $member?->name ?? '—' }}</h3>
                                    <span class="inline-flex items-center rounded-full bg-primary-soft border border-primary/10 px-2.5 py-0.5 text-[12px] font-bold uppercase tracking-wide text-primary mt-1">
                                        {{ $trustee->position }}
                                    </span>
                                </div>
                            </div>

                            <div class="border-t border-border pt-4">
                                @if ($biz)
                                    <a href="/businesses/{{ $biz->id }}" class="flex items-center gap-3 group">
                                        <div class="h-11 w-11 rounded-xl overflow-hidden border border-border bg-white flex items-center justify-center shrink-0">
                                            <x-safe-image :src="media_url($biz->logo)" :alt="$biz->name" :title="$biz->name" fallback-type="business" img-class="h-full w-full object-contain" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-bold text-foreground group-hover:text-primary transition-colors truncate">{{ $biz->name }}</p>
                                            <p class="text-[12px] text-muted-foreground truncate">{{ $biz->category }}</p>
                                        </div>
                                        <span class="text-[12px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-0.5 shrink-0">
                                            {{ __('site.trustees.view_business') }} <x-icon name="chevron-right" class="h-3 w-3" />
                                        </span>
                                    </a>
                                @else
                                    <p class="text-[12px] text-muted-foreground italic">{{ __('site.trustees.no_business') }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
