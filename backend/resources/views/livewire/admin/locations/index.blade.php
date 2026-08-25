<div class="space-y-5 font-outfit">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Cities & Areas</h1>
            <p class="text-xs text-muted">Manage the Gujarat city/area lists used by the City & Area pickers in member and business profiles</p>
        </div>
        <button
            wire:click="openAddCityModal"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer shadow-sm self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" /> Add City
        </button>
    </div>

    @if ($successMsg)
        <div wire:key="loc-success-{{ md5($successMsg) }}" x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 3000)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700 flex items-center gap-2">
            <x-icon name="check-circle-2" class="h-3.5 w-3.5 text-emerald-600" /> {{ $successMsg }}
        </div>
    @endif

    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-border shadow-xs">
            <div class="relative flex-1 max-w-md">
                <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search cities..."
                    class="w-full rounded-xl border border-border bg-slate-50/50 py-2 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary"
                />
            </div>
            <div class="text-xs font-bold text-slate-600 bg-slate-100 rounded-xl px-3 py-2 border border-slate-200 shrink-0">
                Total Cities: <span class="text-primary font-black">{{ $totalCount }}</span>
            </div>
        </div>

        @if ($cities->isEmpty())
            <div class="py-24 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic shadow-xs">
                {{ $search ? 'No cities matching your search query.' : "No cities defined yet. Click 'Add City' above to create your first one." }}
            </div>
        @else
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    @foreach ($cities as $city)
                        <div class="p-4 rounded-2xl border border-slate-200/80 bg-white flex items-center justify-between shadow-xs hover:shadow-md hover:border-primary/40 transition-all group">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary shadow-xs">
                                    <x-icon name="map-pin" class="h-3.5 w-3.5" />
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-xs font-extrabold text-slate-900 group-hover:text-primary transition-colors truncate">{{ $city->name }}</h3>
                                    <p class="text-[12px] font-medium text-slate-500 mt-0.5">
                                        {{ $city->areas_count }} {{ $city->areas_count === 1 ? 'area' : 'areas' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                <button
                                    wire:click="manageAreas({{ $city->id }})"
                                    class="h-7 px-2 rounded-xl border border-primary/20 bg-primary-soft text-primary flex items-center gap-1 text-[12px] font-bold transition-all hover:opacity-90 active:scale-[0.95] cursor-pointer shadow-xs"
                                    title="Manage Areas"
                                >
                                    Areas
                                </button>
                                <button
                                    wire:click="openEditCityModal({{ $city->id }}, {{ Illuminate\Support\Js::from($city->name) }})"
                                    class="h-7 w-7 rounded-xl border border-amber-200/80 bg-amber-50 text-amber-700 flex items-center justify-center transition-all hover:bg-amber-100 active:scale-[0.95] cursor-pointer shadow-xs"
                                    title="Edit City"
                                >
                                    <x-icon name="pencil" class="h-3 w-3" />
                                </button>
                                <button
                                    wire:click="openDeleteCity({{ $city->id }}, {{ Illuminate\Support\Js::from($city->name) }})"
                                    class="h-7 w-7 rounded-xl border border-rose-200/80 bg-rose-50 text-rose-600 flex items-center justify-center transition-all hover:bg-rose-100 active:scale-[0.95] cursor-pointer shadow-xs"
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

    {{-- Add City Modal --}}
    @if ($isAddCityModalOpen)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm" wire:click="cancelAddCity"></div>
            <div x-data x-show="true" x-transition class="relative z-50 w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <x-icon name="map-pin" class="h-4.5 w-4.5 text-primary" /> Add New City
                    </h3>
                    <button wire:click="cancelAddCity" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                <form wire:submit="addCity" class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">City Name</label>
                        <input
                            type="text"
                            wire:model="addCityName"
                            placeholder="e.g. Ahmedabad"
                            class="w-full rounded-xl border border-border bg-slate-50/50 py-2.5 px-4 text-xs font-medium text-slate-900 outline-none transition-all focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary/20"
                            autofocus
                        />
                        @error('addCityName')
                            <p class="text-[12px] font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="cancelAddCity" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 transition-all cursor-pointer">
                            <span wire:loading.remove wire:target="addCity" class="inline-flex items-center gap-1.5"><x-icon name="plus" class="h-3.5 w-3.5" /> Create City</span>
                            <span wire:loading wire:target="addCity">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Edit City Modal --}}
    @if ($editingCityId !== null)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm" wire:click="cancelEditCity"></div>
            <div x-data x-show="true" x-transition class="relative z-50 w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <x-icon name="pencil" class="h-4.5 w-4.5 text-amber-600" /> Edit City
                    </h3>
                    <button wire:click="cancelEditCity" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                <form wire:submit="updateCity" class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">City Name</label>
                        <input
                            type="text"
                            wire:model="editCityName"
                            class="w-full rounded-xl border border-border bg-slate-50/50 py-2.5 px-4 text-xs font-medium text-slate-900 outline-none transition-all focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary/20"
                            autofocus
                        />
                        @error('editCityName')
                            <p class="text-[12px] font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="cancelEditCity" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-amber-700 active:scale-95 transition-all cursor-pointer">
                            <span wire:loading.remove wire:target="updateCity" class="inline-flex items-center gap-1.5"><x-icon name="pencil" class="h-3.5 w-3.5" /> Save Changes</span>
                            <span wire:loading wire:target="updateCity">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Manage Areas Modal --}}
    @if ($managingCity)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm" wire:click="closeManageAreas"></div>
            <div x-data x-show="true" x-transition class="relative z-50 w-full max-w-lg bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4 max-h-[85vh] flex flex-col">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 shrink-0">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <x-icon name="map-pin" class="h-4.5 w-4.5 text-primary" /> Areas in {{ $managingCity->name }}
                    </h3>
                    <button wire:click="closeManageAreas" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                <form wire:submit="addArea" class="flex gap-2 shrink-0">
                    <input
                        type="text"
                        wire:model="addAreaName"
                        placeholder="e.g. Vastrapur"
                        class="flex-1 rounded-xl border border-border bg-slate-50/50 py-2 px-3 text-xs font-medium text-slate-900 outline-none transition-all focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary/20"
                    />
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-3 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 transition-all cursor-pointer shrink-0">
                        <x-icon name="plus" class="h-3.5 w-3.5" /> Add
                    </button>
                </form>
                @error('addAreaName')
                    <p class="text-[12px] font-semibold text-rose-600 -mt-2">{{ $message }}</p>
                @enderror

                <div class="flex-1 overflow-y-auto space-y-1.5 pr-1">
                    @forelse ($managingCity->areas as $area)
                        <div class="flex items-center justify-between px-3 py-2 rounded-xl border border-slate-100 bg-slate-50/50">
                            <span class="text-xs font-semibold text-slate-800">{{ $area->name }}</span>
                            <button
                                wire:click="openDeleteArea({{ $area->id }}, {{ Illuminate\Support\Js::from($area->name) }})"
                                class="h-6 w-6 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center hover:bg-rose-100 transition-colors cursor-pointer shrink-0"
                                title="Delete Area"
                            >
                                <x-icon name="trash-2" class="h-3 w-3" />
                            </button>
                        </div>
                    @empty
                        <p class="text-xs text-muted italic text-center py-6">No areas added yet for this city.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <x-admin.confirm-modal
        :show="$deletingCityId !== null"
        title="Delete City"
        :message="'Are you sure you want to delete &quot;' . $deletingCityName . '&quot;? This also deletes all its areas.'"
        confirm-label="Delete City"
        variant="danger"
        confirm-action="confirmDeleteCity"
        cancel-action="cancelDeleteCity"
    />

    <x-admin.confirm-modal
        :show="$deletingAreaId !== null"
        title="Delete Area"
        :message="'Are you sure you want to delete &quot;' . $deletingAreaName . '&quot;?'"
        confirm-label="Delete Area"
        variant="danger"
        confirm-action="confirmDeleteArea"
        cancel-action="cancelDeleteArea"
    />
</div>
