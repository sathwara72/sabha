<div class="space-y-4 font-outfit w-full">
    {{-- Header with Navigation & City Summary --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-200/80">
        <div class="flex items-center gap-3">
            <a
                href="{{ route('admin.locations.index') }}"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-primary hover:bg-slate-50 hover:border-primary/40 transition-all cursor-pointer shadow-2xs shrink-0"
                title="Back to Cities List"
            >
                <x-icon name="arrow-left" class="h-4 w-4" />
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">
                        Areas in {{ $city->name }}
                    </h1>
                    <!-- <span class="rounded-full bg-emerald-50 border border-emerald-200/80 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">
                        Gujarat City
                    </span> -->
                </div>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Manage locality & sector zones used in member profiles and business directory listings
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <div class="text-xs font-bold text-slate-700 bg-white rounded-xl px-3.5 py-2 border border-slate-200 shadow-2xs shrink-0">
                Total Areas: <span class="text-primary font-black">{{ $totalCount }}</span>
            </div>
        </div>
    </div>

    {{-- Success Notification Banner --}}
    @if ($successMsg)
        <div
            wire:key="area-success-{{ md5($successMsg) }}"
            x-data="{ show: true }"
            x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 3500)"
            x-show="show"
            x-transition
            class="rounded-xl bg-emerald-50 border border-emerald-200/80 p-3 text-xs font-semibold text-emerald-800 flex items-center gap-2 shadow-2xs"
        >
            <x-icon name="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0" />
            <span>{{ $successMsg }}</span>
        </div>
    @endif

    {{-- Main 2-Column Space-Optimized Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        {{-- Left: Quick Add Area Card (4 Columns) --}}
        <div class="lg:col-span-4 space-y-3 sticky top-20">
            <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-primary shadow-2xs">
                        <x-icon name="plus" class="h-4 w-4" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Add New Area</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Add under {{ $city->name }}</p>
                    </div>
                </div>

                <form wire:submit="addArea" class="space-y-3">
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                            Area Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative group">
                            <x-icon name="map-pin" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400 group-focus-within:text-primary transition-colors" />
                            <input
                                type="text"
                                wire:model="addAreaName"
                                placeholder="e.g. Vastrapur, Satellite, SG Highway..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-9 pr-3 text-xs font-semibold text-slate-900 placeholder:text-slate-400 outline-none transition-all focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10"
                            />
                        </div>
                        @error('addAreaName')
                            <p class="text-[11px] font-semibold text-rose-600 mt-1 flex items-center gap-1">
                                <x-icon name="alert-circle" class="h-3.5 w-3.5 shrink-0" />
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] py-2.5 px-4 text-xs font-bold text-white shadow-sm hover:opacity-95 active:scale-[0.98] disabled:opacity-50 transition-all cursor-pointer"
                    >
                        <x-icon name="plus" class="h-3.5 w-3.5" wire:loading.remove wire:target="addArea" />
                        <span wire:loading.remove wire:target="addArea">Add Area to {{ $city->name }}</span>
                        <span wire:loading wire:target="addArea">Adding...</span>
                    </button>
                </form>

                {{-- Helpful info tip --}}
                <div class="rounded-xl bg-slate-50 border border-slate-200/70 p-3 space-y-1">
                    <p class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                        <x-icon name="info" class="h-3.5 w-3.5 text-primary" />
                        <span>Instant Auto-Suggest</span>
                    </p>
                    <p class="text-[11px] text-slate-500 leading-relaxed font-medium">
                        Areas added here appear instantly in dropdown selectors during member onboarding and business registration.
                    </p>
                </div>
            </div>
        </div>

        {{-- Right: Areas Grid & Search Filter (8 Columns) --}}
        <div class="lg:col-span-8 space-y-3">
            <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-2xs space-y-3.5">
                {{-- Search Toolbar --}}
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="relative flex-1 max-w-md">
                        {{-- Normal Search Icon --}}
                        <x-icon
                            name="search"
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400"
                            wire:loading.remove
                            wire:target="search"
                        />

                        {{-- Loading Spinner Icon --}}
                        <x-icon
                            name="loader-2"
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-primary animate-spin"
                            wire:loading
                            wire:target="search"
                        />

                        <input
                            type="text"
                            wire:model.live.debounce.250ms="search"
                            placeholder="Filter areas in {{ $city->name }}..."
                            class="w-full rounded-lg border border-slate-200 bg-slate-50/50 py-1.5 pl-9 pr-8 text-xs font-medium text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10"
                        />

                        @if ($search !== '')
                            <button
                                type="button"
                                wire:click="$set('search', '')"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer"
                                title="Clear search"
                            >
                                <x-icon name="x" class="h-3 w-3" />
                            </button>
                        @endif
                    </div>

                    <div class="text-[11px] font-bold text-slate-500 shrink-0">
                        Showing <strong class="text-slate-800">{{ $areas->count() }}</strong> of {{ $totalCount }}
                    </div>
                </div>

                {{-- Areas Grid (2-3 Columns) --}}
                <div wire:loading.class="opacity-50 pointer-events-none" wire:target="search" class="transition-opacity duration-150">
                    @if ($areas->isEmpty())
                        <div class="py-16 text-center text-slate-500 text-xs rounded-xl border border-dashed border-slate-200 bg-slate-50/40">
                            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-2">
                                <x-icon name="map-pin" class="h-5 w-5" />
                            </div>
                            <p class="font-bold text-slate-700">
                                {{ $search ? 'No areas match "' . $search . '"' : 'No areas added yet in ' . $city->name }}
                            </p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Use the left form to add your first area.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2.5">
                            @foreach ($areas as $index => $area)
                                <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200/80 bg-slate-50/50 p-2.5 sm:px-3 sm:py-2.5 transition-all duration-150 hover:bg-white hover:border-primary/40 hover:shadow-2xs group">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-white border border-slate-200 text-[10px] font-bold text-slate-500 group-hover:text-primary group-hover:border-primary/30 transition-colors shadow-2xs">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-800 group-hover:text-primary transition-colors truncate" title="{{ $area->name }}">
                                            {{ $area->name }}
                                        </span>
                                    </div>

                                    <button
                                        type="button"
                                        wire:click="openDeleteArea({{ $area->id }}, {{ Illuminate\Support\Js::from($area->name) }})"
                                        class="h-6 w-6 rounded-lg border border-transparent text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 flex items-center justify-center transition-all cursor-pointer shrink-0"
                                        title="Delete {{ $area->name }}"
                                    >
                                        <x-icon name="trash-2" class="h-3 w-3" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Area Confirmation Modal --}}
    <x-admin.confirm-modal
        :show="$deletingAreaId !== null"
        title="Delete Area"
        :message="'Are you sure you want to delete &quot;' . $deletingAreaName . '&quot; from ' . $city->name . '?'"
        confirm-label="Delete Area"
        variant="danger"
        confirm-action="confirmDeleteArea"
        cancel-action="cancelDeleteArea"
    />
</div>
