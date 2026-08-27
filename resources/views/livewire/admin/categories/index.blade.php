<div class="space-y-5 font-outfit">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Business Categories</h1>
            <p class="text-xs text-muted">Manage directory categories displayed in member registrations and search filters</p>
        </div>
        <a
            href="{{ route('admin.categories.create') }}"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer shadow-sm self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" /> Add Category
        </a>
    </div>

    @if ($successMsg)
        <div wire:key="success-{{ md5($successMsg) }}" x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 3000)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700 flex items-center gap-2">
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
                    placeholder="Search categories..."
                    class="w-full rounded-xl border border-border bg-slate-50/50 py-2 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary"
                />
            </div>
            <div class="text-xs font-bold text-slate-600 bg-slate-100 rounded-xl px-3 py-2 border border-slate-200 shrink-0">
                Total Categories: <span class="text-primary font-black">{{ $totalCount }}</span>
            </div>
        </div>

        @if ($categories->isEmpty())
            <div class="py-24 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic shadow-xs">
                {{ $search ? 'No categories matching your search query.' : "No categories defined yet. Click 'Add Category' above to create your first category." }}
            </div>
        @else
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    @foreach ($categories as $cat)
                        <div class="p-4 rounded-2xl border border-slate-200/80 bg-white flex items-center justify-between shadow-xs hover:shadow-md hover:border-primary/40 transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary shadow-xs">
                                    <x-icon name="tag" class="h-3.5 w-3.5" />
                                </div>
                                <div>
                                    <h3 class="text-xs font-extrabold text-slate-900 group-hover:text-primary transition-colors">{{ $cat->name }}</h3>
                                    <p class="text-[12px] font-medium text-slate-500 flex items-center gap-1 mt-0.5">
                                        <x-icon name="briefcase" class="h-2.5 w-2.5 text-slate-400" />
                                        {{ $cat->businesses_count ?? 0 }} {{ $cat->businesses_count === 1 ? 'business' : 'businesses' }} registered
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <a
                                    href="{{ route('admin.categories.edit', $cat->id) }}"
                                    class="h-7 w-7 rounded-xl border border-amber-200/80 bg-amber-50 text-amber-700 flex items-center justify-center transition-all hover:bg-amber-100 active:scale-[0.95] cursor-pointer shadow-xs"
                                    title="Edit Category"
                                >
                                    <x-icon name="pencil" class="h-3 w-3" />
                                </a>
                                <button
                                    wire:click="openDelete({{ $cat->id }}, {{ Illuminate\Support\Js::from($cat->name) }})"
                                    class="h-7 w-7 rounded-xl border border-rose-200/80 bg-rose-50 text-rose-600 flex items-center justify-center transition-all hover:bg-rose-100 active:scale-[0.95] disabled:opacity-50 cursor-pointer shadow-xs"
                                    title="Delete Category"
                                >
                                    <x-icon name="trash-2" class="h-3 w-3" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <x-pagination :paginator="$categories" item-label="categories" />
            </div>
        @endif
    </div>

    <x-admin.confirm-modal
        :show="$deletingId !== null"
        title="Delete Category"
        :message="'Are you sure you want to delete the category &quot;' . $deletingName . '&quot;?'"
        confirm-label="Delete Category"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
