<div class="space-y-5 font-outfit">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Business Categories</h1>
            <p class="text-xs text-muted">Manage directory categories displayed in member registrations and search filters</p>
        </div>
        <button
            wire:click="openAddModal"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer shadow-sm self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" /> Add Category
        </button>
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
                                    <p class="text-[11px] font-medium text-slate-500 flex items-center gap-1 mt-0.5">
                                        <x-icon name="briefcase" class="h-2.5 w-2.5 text-slate-400" />
                                        {{ $cat->businesses_count ?? 0 }} {{ $cat->businesses_count === 1 ? 'business' : 'businesses' }} registered
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <button
                                    wire:click="openEditModal({{ $cat->id }}, {{ Illuminate\Support\Js::from($cat->name) }})"
                                    class="h-7 w-7 rounded-xl border border-amber-200/80 bg-amber-50 text-amber-700 flex items-center justify-center transition-all hover:bg-amber-100 active:scale-[0.95] cursor-pointer shadow-xs"
                                    title="Edit Category"
                                >
                                    <x-icon name="pencil" class="h-3 w-3" />
                                </button>
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

    {{-- Add Category Modal --}}
    @if ($isAddModalOpen)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm" wire:click="cancelAdd"></div>
            <div x-data x-show="true" x-transition class="relative z-50 w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <x-icon name="tag" class="h-4.5 w-4.5 text-primary" /> Add New Category
                    </h3>
                    <button wire:click="cancelAdd" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                <form wire:submit="addCategory" class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Category Name</label>
                        <div class="relative">
                            <x-icon name="tag" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                            <input
                                type="text"
                                wire:model="addName"
                                placeholder="e.g. Renewables & Solar"
                                class="w-full rounded-xl border border-border bg-slate-50/50 py-2.5 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none transition-all focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary/20"
                                autofocus
                            />
                        </div>
                        @error('addName')
                            <p class="text-[11px] font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="cancelAdd" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 disabled:opacity-50 transition-all cursor-pointer">
                            <span wire:loading.remove wire:target="addCategory" class="inline-flex items-center gap-1.5"><x-icon name="plus" class="h-3.5 w-3.5" /> Create Category</span>
                            <span wire:loading wire:target="addCategory">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Edit Category Modal --}}
    @if ($editingId !== null)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm" wire:click="cancelEdit"></div>
            <div x-data x-show="true" x-transition class="relative z-50 w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <x-icon name="pencil" class="h-4.5 w-4.5 text-amber-600" /> Edit Category
                    </h3>
                    <button wire:click="cancelEdit" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                <form wire:submit="updateCategory" class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Category Name</label>
                        <div class="relative">
                            <x-icon name="tag" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                            <input
                                type="text"
                                wire:model="editName"
                                placeholder="Enter category name"
                                class="w-full rounded-xl border border-border bg-slate-50/50 py-2.5 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none transition-all focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary/20"
                                autofocus
                            />
                        </div>
                        @error('editName')
                            <p class="text-[11px] font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="cancelEdit" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-amber-700 active:scale-95 disabled:opacity-50 transition-all cursor-pointer">
                            <span wire:loading.remove wire:target="updateCategory" class="inline-flex items-center gap-1.5"><x-icon name="pencil" class="h-3.5 w-3.5" /> Save Changes</span>
                            <span wire:loading wire:target="updateCategory">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
