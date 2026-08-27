@php
    $swatchClasses = [
        'primary' => 'bg-primary',
        'emerald' => 'bg-emerald-500',
        'amber' => 'bg-amber-500',
        'rose' => 'bg-rose-500',
        'indigo' => 'bg-indigo-500',
        'sky' => 'bg-sky-500',
        'violet' => 'bg-violet-500',
        'slate' => 'bg-slate-500',
    ];
@endphp

<div class="space-y-5 font-outfit">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Member Titles</h1>
            <p class="text-xs text-muted">Manage display titles/badges (e.g. Leadership Team, Founding Member) assignable to members</p>
        </div>
        <a
            href="{{ route('admin.member-titles.create') }}"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer shadow-sm self-start sm:self-auto"
        >
            <x-icon name="plus" class="h-3.5 w-3.5" /> Add Member Title
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
                    placeholder="Search member titles..."
                    class="w-full rounded-xl border border-border bg-slate-50/50 py-2 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary"
                />
            </div>
            <div class="text-xs font-bold text-slate-600 bg-slate-100 rounded-xl px-3 py-2 border border-slate-200 shrink-0">
                Total Titles: <span class="text-primary font-black">{{ $totalCount }}</span>
            </div>
        </div>

        @if ($titles->isEmpty())
            <div class="py-24 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic shadow-xs">
                {{ $search ? 'No member titles matching your search query.' : "No member titles defined yet. Click 'Add Member Title' above to create your first one." }}
            </div>
        @else
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    @foreach ($titles as $title)
                        <div class="p-4 rounded-2xl border border-slate-200/80 bg-white flex items-center justify-between shadow-xs hover:shadow-md hover:border-primary/40 transition-all group">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="h-3 w-3 rounded-full shrink-0 {{ $swatchClasses[$title->badge_color] ?? $swatchClasses['primary'] }}"></span>
                                <div class="min-w-0">
                                    <x-member-title-badge :title="$title" />
                                    <p class="text-[12px] font-medium text-slate-500 flex items-center gap-1 mt-1">
                                        <x-icon name="users" class="h-2.5 w-2.5 text-slate-400" />
                                        {{ $title->users_count ?? 0 }} {{ $title->users_count === 1 ? 'member' : 'members' }} assigned
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                <a
                                    href="{{ route('admin.member-titles.edit', $title->id) }}"
                                    class="h-7 w-7 rounded-xl border border-amber-200/80 bg-amber-50 text-amber-700 flex items-center justify-center transition-all hover:bg-amber-100 active:scale-[0.95] cursor-pointer shadow-xs"
                                    title="Edit Member Title"
                                >
                                    <x-icon name="pencil" class="h-3 w-3" />
                                </a>
                                <button
                                    wire:click="openDelete({{ $title->id }}, {{ Illuminate\Support\Js::from($title->name) }})"
                                    class="h-7 w-7 rounded-xl border border-rose-200/80 bg-rose-50 text-rose-600 flex items-center justify-center transition-all hover:bg-rose-100 active:scale-[0.95] disabled:opacity-50 cursor-pointer shadow-xs"
                                    title="Delete Member Title"
                                >
                                    <x-icon name="trash-2" class="h-3 w-3" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <x-pagination :paginator="$titles" item-label="member titles" />
            </div>
        @endif
    </div>

    <x-admin.confirm-modal
        :show="$deletingId !== null"
        title="Delete Member Title"
        :message="'Are you sure you want to delete the member title &quot;' . $deletingName . '&quot;? Members with this title will have it removed.'"
        confirm-label="Delete Title"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
