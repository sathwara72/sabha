<div class="bg-background font-outfit">
    <div class="mx-auto max-w-7xl px-6 py-3 lg:px-4 space-y-4">
        {{-- Title row --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between border-b border-slate-200 pb-3">
            <div>
                <div class="mb-1.5 flex items-center gap-2">
                    <span class="h-3.5 w-1.5 rounded-full bg-primary"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-primary">{{ __('site.directory.label') }}</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">{{ __('site.directory.title') }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">{{ __('site.directory.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-xl bg-blue-50 border border-blue-200 px-3 py-1 text-xs font-bold text-primary shadow-2xs">
                    {{ $businesses->total() }} {{ $businesses->total() === 1 ? __('site.directory.business') : __('site.directory.showing') }}
                </span>
            </div>
        </div>

        {{-- Search & Filter Card with Real Select Dropdowns --}}
        <div class="glass-card p-2.5 sm:p-3 rounded-xl border border-slate-200/90 shadow-2xs bg-white space-y-2">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 sm:gap-2.5 items-end">
                {{-- Search Box (Wider) --}}
                <div class="md:col-span-6 space-y-0.5">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                        {{ __('site.directory.search_label') }}
                    </label>
                    <div class="relative">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            wire:model.live.debounce.350ms="search"
                            placeholder="{{ __('site.directory.search_placeholder') }}"
                            class="w-full rounded-lg border border-slate-200 bg-slate-50/50 py-1.5 sm:py-2 pl-8 pr-8 text-xs font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:border-primary shadow-2xs"
                        />
                        @if ($search)
                            <button type="button" wire:click="$set('search', '')" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer">
                                <x-icon name="x" class="h-3 w-3" />
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Category Dropdown (Compact) --}}
                <div class="md:col-span-3 space-y-0.5">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                        {{ __('site.directory.filter_category') }}
                    </label>
                    <div class="relative">
                        <x-icon name="tag" class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-primary" />
                        <select
                            wire:model.live="category"
                            class="w-full appearance-none rounded-lg border border-slate-200 bg-slate-50/50 py-1.5 sm:py-2 pl-8 pr-8 text-xs font-semibold text-slate-800 outline-none transition-all focus:bg-white focus:border-primary shadow-2xs cursor-pointer hover:bg-slate-100/60"
                        >
                            <option value="All Categories">{{ __('site.directory.all_categories') }}</option>
                            @foreach ($categories as $cat)
                                @if ($cat !== 'All Categories' && $cat !== 'All')
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endif
                            @endforeach
                        </select>
                        <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                    </div>
                </div>

                {{-- Location / Area Dropdown (Compact) --}}
                <div class="md:col-span-3 space-y-0.5">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                        {{ __('site.directory.filter_location') }}
                    </label>
                    <div class="relative">
                        <x-icon name="map-pin" class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-primary" />
                        <select
                            wire:model.live="area"
                            class="w-full appearance-none rounded-lg border border-slate-200 bg-slate-50/50 py-1.5 sm:py-2 pl-8 pr-8 text-xs font-semibold text-slate-800 outline-none transition-all focus:bg-white focus:border-primary shadow-2xs cursor-pointer hover:bg-slate-100/60"
                        >
                            <option value="All Areas">{{ __('site.directory.all_areas') }}</option>
                            @foreach ($areas as $a)
                                @if ($a !== 'All Areas' && $a !== 'All')
                                    <option value="{{ $a }}">{{ $a }}</option>
                                @endif
                            @endforeach
                        </select>
                        <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                    </div>
                </div>
            </div>

            @if ($search !== '' || ($category !== '' && $category !== 'All Categories') || ($area !== '' && $area !== 'All Areas'))
                <div class="flex flex-wrap items-center justify-between gap-1.5 pt-1.5 border-t border-slate-100">
                    <div class="flex flex-wrap items-center gap-1 text-[11px] text-slate-500">
                        <span class="font-bold text-slate-700 text-[10px] uppercase tracking-wider">{{ __('site.directory.active_filters') }}:</span>
                        @if ($search !== '')
                            <span class="inline-flex items-center gap-1 bg-primary-soft text-primary px-1.5 py-0.5 rounded text-[10px] font-semibold">
                                "{{ $search }}"
                                <button type="button" wire:click="$set('search', '')" class="hover:opacity-75 cursor-pointer"><x-icon name="x" class="h-2.5 w-2.5" /></button>
                            </span>
                        @endif
                        @if ($category !== '' && $category !== 'All Categories')
                            <span class="inline-flex items-center gap-1 bg-primary-soft text-primary px-1.5 py-0.5 rounded text-[10px] font-semibold">
                                {{ $category }}
                                <button type="button" wire:click="$set('category', 'All Categories')" class="hover:opacity-75 cursor-pointer"><x-icon name="x" class="h-2.5 w-2.5" /></button>
                            </span>
                        @endif
                        @if ($area !== '' && $area !== 'All Areas')
                            <span class="inline-flex items-center gap-1 bg-primary-soft text-primary px-1.5 py-0.5 rounded text-[10px] font-semibold">
                                {{ $area }}
                                <button type="button" wire:click="$set('area', 'All Areas')" class="hover:opacity-75 cursor-pointer"><x-icon name="x" class="h-2.5 w-2.5" /></button>
                            </span>
                        @endif
                    </div>
                    <button
                        type="button"
                        wire:click="$set('search', ''); $set('category', 'All Categories'); $set('area', 'All Areas')"
                        class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-600 hover:text-rose-700 hover:underline cursor-pointer ml-auto"
                    >
                        <x-icon name="rotate-ccw" class="h-2.5 w-2.5" />
                        {{ __('site.directory.clear_all') }}
                    </button>
                </div>
            @endif
        </div>

        {{-- Results Grid (Clean 3-column cards matching reference design) --}}
        <div class="relative">
            <x-loading-state target="search, category, area, gotoPage, nextPage, previousPage" :message="__('site.directory.searching')" />

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($businesses as $business)
                    @php
                        $locationPart = $business->city ?: ($business->area ?: 'Gujarat');
                        $subline = $locationPart . ($business->category ? ' • ' . $business->category : '');
                    @endphp
                    <a href="/businesses/{{ $business->id }}" class="block h-full cursor-pointer group">
                        <div class="flex h-full flex-col justify-start rounded-2xl sm:rounded-[25px] border border-slate-100 bg-white p-6 shadow-xs transition-all duration-200 hover:shadow-md hover:border-slate-200">
                            {{-- Top Logo Box --}}
                            <div class="h-16 w-16 overflow-hidden rounded-2xl border border-slate-100 bg-white p-2 flex items-center justify-center shrink-0 shadow-xs group-hover:scale-105 transition-transform">
                                <x-safe-image
                                    :src="media_url($business->logo)"
                                    :alt="$business->name"
                                    :title="$business->name"
                                    fallback-type="business"
                                    img-class="h-full w-full object-contain"
                                />
                            </div>

                            {{-- Business Title --}}
                            <h3 class="mt-5 text-base sm:text-lg font-bold text-slate-900 tracking-tight transition-colors group-hover:text-primary">
                                {{ $business->name }}
                            </h3>

                            {{-- Location & Category Line --}}
                            <div class="mt-1.5 flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                                <x-icon name="map-pin" class="h-3.5 w-3.5 text-primary shrink-0" />
                                <span class="truncate">{{ $subline }}</span>
                            </div>

                            {{-- Description (Multi-line clamp) --}}
                            @if (!empty($business->description))
                                <p class="mt-3.5 text-xs text-slate-600 leading-relaxed line-clamp-3 font-normal">
                                    {{ $business->description }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($businesses->total() === 0)
                <div class="rounded-2xl border border-dashed border-slate-200 py-16 text-center bg-slate-50/50">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                        <x-icon name="building" class="h-6 w-6" />
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">{{ __('site.directory.no_businesses_found') }}</h3>
                    <p class="mx-auto mt-1 max-w-xs text-xs text-slate-500">{{ __('site.directory.no_businesses_desc') }}</p>
                </div>
            @endif

            @if ($businesses->hasPages())
                <div class="mt-6">
                    <x-pagination :paginator="$businesses" item-label="{{ __('site.directory.showing') }}" />
                </div>
            @endif
        </div>
    </div>
</div>
