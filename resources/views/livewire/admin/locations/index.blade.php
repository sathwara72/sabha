<div class="space-y-4 font-outfit">
    {{-- Top Header & Add City Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">Cities & Areas</h1>
        </div>
        <button
            type="button"
            wire:click="openCreateCityModal"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2 text-xs font-bold text-white shadow-sm shadow-primary/20 transition-all hover:opacity-95 active:scale-[0.98] cursor-pointer self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" />
            <span>Add City</span>
        </button>
    </div>

    {{-- Success Notification Banner --}}
    @if ($successMsg)
        <div
            wire:key="loc-success-{{ md5($successMsg) }}"
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

    {{-- Search & Stats Toolbar --}}
    <div class="space-y-3.5">
        <div class="flex items-center justify-between gap-3 bg-white p-2.5 sm:p-3 rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="relative flex-1 max-w-md">
                {{-- Search Icon (Normal) --}}
                <x-icon
                    name="search"
                    class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400 transition-colors"
                    wire:loading.remove
                    wire:target="search"
                />

                {{-- Rotating Spinner Loader (Loading) --}}
                <x-icon
                    name="loader-2"
                    class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-primary animate-spin"
                    wire:loading
                    wire:target="search"
                />

                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search cities..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50/50 py-1.5 pl-9 pr-8 text-xs font-medium text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10"
                />

                {{-- Right Searching indicator --}}
                <div
                    wire:loading
                    wire:target="search"
                    class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1 text-[10px] font-bold text-primary select-none pointer-events-none"
                >
                    <span class="animate-pulse">Searching...</span>
                </div>

                {{-- Right Clear 'X' Button --}}
                @if ($search !== '')
                    <button
                        type="button"
                        wire:loading.remove
                        wire:target="search"
                        wire:click="$set('search', '')"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer"
                        title="Clear search"
                    >
                        <x-icon name="x" class="h-3 w-3" />
                    </button>
                @endif
            </div>
            <div class="text-[11px] font-bold text-slate-600 bg-slate-100 rounded-lg px-2.5 py-1.5 border border-slate-200/80 shrink-0">
                Total Cities: <span class="text-primary font-black">{{ $totalCount }}</span>
            </div>
        </div>

        {{-- Cities Grid with Loading Overlay --}}
        <div wire:loading.class="opacity-50 pointer-events-none" wire:target="search" class="transition-opacity duration-200">
            @if ($cities->isEmpty())
                <div class="py-16 text-center text-slate-500 text-xs bg-white rounded-xl border border-dashed border-slate-200 shadow-2xs">
                    <p>{{ $search ? 'No cities matching your search query.' : "No cities defined yet. Click 'Add City' above to create one." }}</p>
                </div>
            @else
                <div class="space-y-3.5">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($cities as $city)
                            <div class="p-3.5 rounded-xl border border-slate-200/90 bg-white flex items-center justify-between shadow-2xs hover:shadow-sm hover:border-primary/40 transition-all group">
                                <div class="flex items-center gap-3 min-w-0 pr-2">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 shadow-2xs">
                                        <x-icon name="map-pin" class="h-3.5 w-3.5" />
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-bold text-slate-900 truncate group-hover:text-primary transition-colors">{{ $city->name }}</h3>
                                        <p class="text-[11px] font-medium text-slate-500 mt-0.5">
                                            {{ $city->areas_count }} {{ $city->areas_count === 1 ? 'area' : 'areas' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 shrink-0">
                                    <a
                                        href="{{ route('admin.locations.areas', $city->id) }}"
                                        class="h-7 px-2.5 rounded-lg border border-primary/20 bg-primary/5 text-primary flex items-center gap-1 text-[11px] font-bold transition-all hover:bg-primary hover:text-white active:scale-[0.95] cursor-pointer shadow-2xs"
                                        title="Manage Areas inside {{ $city->name }}"
                                    >
                                        <span>Areas</span>
                                        <x-icon name="arrow-right" class="h-2.5 w-2.5" />
                                    </a>
                                    <button
                                        type="button"
                                        wire:click="openEditCityModal({{ $city->id }})"
                                        class="h-7 w-7 rounded-lg border border-slate-200 bg-white text-slate-600 flex items-center justify-center transition-all hover:bg-slate-50 hover:text-primary hover:border-primary/40 active:scale-[0.95] cursor-pointer shadow-2xs"
                                        title="Edit City"
                                    >
                                        <x-icon name="pencil" class="h-3 w-3" />
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="openDeleteCity({{ $city->id }}, {{ Illuminate\Support\Js::from($city->name) }})"
                                        class="h-7 w-7 rounded-lg border border-slate-200 bg-white text-slate-500 flex items-center justify-center transition-all hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 active:scale-[0.95] cursor-pointer shadow-2xs"
                                        title="Delete City"
                                    >
                                        <x-icon name="trash-2" class="h-3 w-3" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <x-pagination :paginator="$cities" item-label="cities" />
                </div>
            @endif
        </div>
    </div>

    {{-- ===================== ADD / EDIT CITY POPUP MODAL ===================== --}}
    @if ($showCityModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                wire:click="closeCityModal"
            ></div>

            {{-- Modal Card --}}
            <div
                x-data
                x-show="true"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md transform rounded-2xl bg-white p-5 sm:p-6 shadow-2xl transition-all border border-slate-200 z-10 space-y-4"
            >
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <x-icon name="map-pin" class="h-4 w-4" />
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-black text-slate-900">
                                {{ $cityId ? 'Edit City' : 'Add New City' }}
                            </h3>
                            <p class="text-[11px] text-slate-500 font-medium">Enter Gujarat city or district name</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="closeCityModal"
                        class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer"
                    >
                        <x-icon name="x" class="h-4 w-4" />
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit="saveCity" class="space-y-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            City Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative group">
                            <x-icon name="map-pin" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400 group-focus-within:text-primary transition-colors" />
                            <input
                                type="text"
                                wire:model="name"
                                autofocus
                                placeholder="e.g. Ahmedabad, Surat, Rajkot, Vadodara, Mehsana..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 outline-none transition-all focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10"
                            />
                        </div>
                        @error('name')
                            <p class="text-xs font-semibold text-red-600 mt-1 flex items-center gap-1">
                                <x-icon name="alert-circle" class="h-3.5 w-3.5 shrink-0" />
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button
                            type="button"
                            wire:click="closeCityModal"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-95 active:scale-[0.98] disabled:opacity-50 cursor-pointer"
                        >
                            <span wire:loading.remove wire:target="saveCity">{{ $cityId ? 'Update City' : 'Save City' }}</span>
                            <span wire:loading wire:target="saveCity">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ===================== DELETE CONFIRMATION MODAL ===================== --}}
    <x-admin.confirm-modal
        :show="$deletingCityId !== null"
        title="Delete City"
        :message="'Are you sure you want to delete &quot;' . $deletingCityName . '&quot;? This also deletes all its associated areas.'"
        confirm-label="Delete City"
        variant="danger"
        confirm-action="confirmDeleteCity"
        cancel-action="cancelDeleteCity"
    />
</div>
