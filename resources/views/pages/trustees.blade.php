<x-layouts.app :title="__('site.trustees.title') . ' | Sabha'" :description="__('site.trustees.subtitle')">
    <div class="bg-background font-outfit">
        <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8 space-y-6">
            {{-- Title Row --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between border-b border-slate-200 pb-5">
                <div>
                    <div class="mb-1.5 flex items-center gap-2">
                        <span class="h-3.5 w-1.5 rounded-full bg-primary"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-primary">{{ __('site.trustees.kicker') }}</span>
                    </div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">{{ __('site.trustees.title') }}</h1>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">{{ __('site.trustees.subtitle') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-xl bg-blue-50 border border-blue-200 px-3 py-1 text-xs font-bold text-primary shadow-2xs">
                        {{ $trustees->count() }} {{ $trustees->count() === 1 ? 'Trustee' : 'Trustees' }}
                    </span>
                </div>
            </div>

            {{-- Trustees Grid --}}
            @if ($trustees->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 py-20 text-center bg-slate-50/50">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                        <x-icon name="users" class="h-6 w-6" />
                    </div>
                    <h3 class="text-base font-bold text-slate-800">{{ __('site.trustees.empty') }}</h3>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5">
                    @foreach ($trustees as $trustee)
                        @php
                            $member = $trustee->user;
                            $biz = $member?->business?->status === 'approved' ? $member->business : null;
                        @endphp
                        <div class="glass-card flex h-full flex-col justify-between p-3.5 rounded-2xl border border-slate-200 hover:border-primary/40 hover:shadow-md transition-all duration-300 bg-white space-y-2.5">
                            {{-- Top Header: Avatar + Details --}}
                            <div class="flex items-start gap-2.5">
                                <div class="h-11 w-11 rounded-xl overflow-hidden border border-primary/20 shadow-2xs shrink-0 bg-slate-50 flex items-center justify-center">
                                    <x-safe-image
                                        :src="media_url($member?->avatar)"
                                        :alt="$member?->name ?? 'Trustee'"
                                        :title="$member?->name ?? 'Trustee'"
                                        fallback-type="avatar"
                                        img-class="h-full w-full object-cover"
                                    />
                                </div>

                                <div class="min-w-0 flex-1 space-y-0.5">
                                    <span class="inline-flex items-center gap-0.5 rounded-full bg-blue-50 border border-blue-200 px-2 py-0.2 text-[9px] font-black uppercase tracking-wider text-primary">
                                        <x-icon name="shield-check" class="h-2.5 w-2.5 text-primary" />
                                        {{ $trustee->position ?: 'Trustee' }}
                                    </span>
                                    <h3 class="text-sm font-black text-slate-900 tracking-tight truncate">
                                        {{ $member?->name ?: 'Community Leader' }}
                                    </h3>
                                    <div class="space-y-0.5 pt-0.5">
                                        @if ($member?->city)
                                            <p class="text-[10px] text-slate-500 font-semibold flex items-center gap-1">
                                                <x-icon name="map-pin" class="h-2.5 w-2.5 text-slate-400 shrink-0" />
                                                <span class="truncate">{{ $member->city }}</span>
                                            </p>
                                        @endif
                                        @if ($member?->phone)
                                            <p class="text-[10px] text-slate-600 font-bold flex items-center gap-1">
                                                <x-icon name="phone" class="h-2.5 w-2.5 text-primary shrink-0" />
                                                <a href="tel:{{ $member->phone }}" class="hover:text-primary transition-colors">{{ $member->phone }}</a>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Business Info Card / Box --}}
                            <div class="pt-2 border-t border-slate-100">
                                @if ($biz)
                                    <a href="/businesses/{{ $biz->id }}" class="block p-2 rounded-xl bg-slate-50/80 hover:bg-blue-50/50 border border-slate-200/80 hover:border-primary/30 transition-all group">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <div class="h-8 w-8 rounded-lg overflow-hidden border border-slate-200 bg-white flex items-center justify-center shrink-0 p-0.5">
                                                    <x-safe-image
                                                        :src="media_url($biz->logo)"
                                                        :alt="$biz->name"
                                                        :title="$biz->name"
                                                        fallback-type="business"
                                                        img-class="h-full w-full object-contain"
                                                    />
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-black text-slate-900 group-hover:text-primary transition-colors truncate">{{ $biz->name }}</p>
                                                    <p class="text-[10px] text-primary font-bold truncate">{{ $biz->category }}</p>
                                                </div>
                                            </div>
                                            <x-icon name="arrow-right" class="h-3.5 w-3.5 text-slate-400 group-hover:text-primary group-hover:translate-x-0.5 transition-all shrink-0" />
                                        </div>
                                    </a>
                                @else
                                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-1.5 text-[11px] text-slate-500 font-medium">
                                        <x-icon name="award" class="h-3.5 w-3.5 text-amber-500 shrink-0" />
                                        <span class="truncate">Board Member</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
