<div class="bg-background font-outfit">
    <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
        {{-- Compact title row --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between border-b border-border pb-6">
            <div>
                <div class="mb-2 flex items-center gap-2.5">
                    <span class="h-4 w-1.5 rounded-full bg-accent"></span>
                    <span class="text-sm font-semibold text-accent">{{ __('site.directory.label') }}</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">{{ __('site.directory.title') }}</h1>
                <p class="mt-1 text-sm text-muted">{{ __('site.directory.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <p class="text-sm font-medium text-muted">
                    {{ $businesses->total() }} {{ $businesses->total() === 1 ? __('site.directory.business') : __('site.directory.showing') }}
                </p>
            </div>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="mb-10 mt-8 flex flex-col gap-4">
            <div class="relative max-w-2xl">
                <x-icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="{{ __('site.directory.search_placeholder') }}"
                    class="w-full rounded-xl border border-border bg-white py-3 pl-12 pr-4 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary font-semibold"
                />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="w-full max-w-[220px]">
                    <x-searchable-select
                        wire-model="category"
                        :options="$categories"
                        :value="$category"
                        :allow-custom="false"
                        placeholder="All Categories"
                        leading-icon="tag"
                        wire-key="directory-category-filter"
                    />
                </div>
                <div class="w-full max-w-[220px]">
                    <x-searchable-select
                        wire-model="area"
                        :options="$areas"
                        :value="$area"
                        :allow-custom="false"
                        placeholder="All Areas"
                        leading-icon="map-pin"
                        wire-key="directory-area-filter"
                    />
                </div>
            </div>
        </div>

        {{-- Results Grid --}}
        <div wire:loading.delay class="py-20 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent"></div>
            <p class="mt-3 text-sm text-muted">Loading business listings...</p>
        </div>

        <div wire:loading.remove.delay>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($businesses as $business)
                    <a href="/businesses/{{ $business->id }}" class="block h-full cursor-pointer">
                        <div class="glass-card group flex h-full flex-col p-6 hover:shadow-md transition-shadow">
                            <div class="mb-5 flex items-start justify-between">
                                <div class="h-16 w-16 overflow-hidden rounded-xl border border-border bg-white flex items-center justify-center text-primary text-xl font-bold">
                                    <x-safe-image
                                        :src="media_url($business->logo)"
                                        :alt="$business->name"
                                        :title="$business->name"
                                        fallback-type="business"
                                        img-class="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105"
                                    />
                                </div>
                                @if ($business->rating && (float) $business->rating > 0)
                                    <div class="flex flex-col items-end gap-2">
                                        <div class="inline-flex items-center gap-1.5 text-sm font-medium text-amber-500">
                                            <x-icon name="star" class="h-3.5 w-3.5 fill-current" />
                                            {{ number_format((float) $business->rating, 1) }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-foreground transition-colors group-hover:text-primary">{{ $business->name }}</h3>
                                @if ($business->user)
                                    <p class="mt-0.5 inline-flex items-center gap-1.5 text-[12px] text-muted-foreground">
                                        <x-icon name="user" class="h-[12px] w-[12px] text-muted-foreground shrink-0" />
                                        {{ $business->user->name }}
                                    </p>
                                @endif
                                <p class="mt-1 inline-flex items-center gap-1.5 text-xs text-muted">
                                    @if ($business->area)
                                        <x-icon name="map-pin" class="h-[13px] w-[13px] text-primary shrink-0" />
                                        {{ $business->area }} •
                                    @endif
                                    {{ $business->category }}
                                </p>
                                <p class="mt-3.5 text-xs leading-relaxed text-muted line-clamp-3">
                                    {{ $business->description ?: 'No description provided yet.' }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($businesses->total() === 0)
                <div class="rounded-xl border border-dashed border-border py-20 text-center">
                    <h3 class="text-lg font-semibold text-foreground">No businesses found</h3>
                    <p class="mx-auto mt-2 max-w-xs text-sm text-muted">Try a different search term, category, or area.</p>
                </div>
            @endif

            <div class="mt-8">
                <x-pagination :paginator="$businesses" item-label="businesses" />
            </div>
        </div>
    </div>
</div>
