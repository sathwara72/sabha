<div class="space-y-5 font-outfit">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Referral Testimonials</h1>
            <p class="text-xs text-muted">Moderate testimonials left by referral receivers on closed deals — hide any that shouldn't be public</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <div class="text-xs font-bold text-slate-600 bg-slate-100 rounded-xl px-3 py-2 border border-slate-200">
                Total: <span class="text-primary font-black">{{ $totalCount }}</span>
            </div>
            <div class="text-xs font-bold text-slate-600 bg-emerald-50 rounded-xl px-3 py-2 border border-emerald-200">
                Visible: <span class="text-emerald-700 font-black">{{ $visibleCount }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white p-3 rounded-2xl border border-border shadow-xs">
        <div class="relative max-w-md">
            <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by testimonial text or member name..."
                class="w-full rounded-xl border border-border bg-slate-50/50 py-2 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary"
            />
        </div>
    </div>

    @if ($testimonials->isEmpty())
        <div class="py-24 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic shadow-xs">
            {{ $search ? 'No testimonials matching your search query.' : 'No testimonials submitted yet.' }}
        </div>
    @else
        <div class="space-y-3.5">
            @foreach ($testimonials as $t)
                <div class="p-4 rounded-2xl border border-slate-200/80 bg-white shadow-xs space-y-2.5">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <p class="text-xs font-extrabold text-slate-900">{{ $t->receiver?->name ?? '—' }} <span class="font-normal text-slate-400">about</span> {{ $t->giver?->name ?? '—' }}'s business</p>
                            <p class="mt-0.5 text-[12px] text-slate-500">Closed referral · {{ $t->amount ? '₹' . number_format((float) $t->amount) : 'amount not specified' }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-[12px] font-bold uppercase tracking-wide {{ $t->display_testimonial ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                            {{ $t->display_testimonial ? 'Visible on business page' : 'Hidden' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-700 italic bg-slate-50/70 p-3 rounded-xl border border-slate-100">"{{ $t->testimonial }}"</p>
                    <div class="flex justify-end">
                        <button
                            wire:click="toggleDisplay({{ $t->id }})"
                            class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[12px] font-bold border transition-all active:scale-95 cursor-pointer shadow-xs {{ $t->display_testimonial ? 'bg-rose-50 text-rose-600 border-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}"
                        >
                            @if ($t->display_testimonial)
                                <x-icon name="eye-off" class="h-3 w-3" /> Hide from Public Page
                            @else
                                <x-icon name="eye" class="h-3 w-3" /> Show on Public Page
                            @endif
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-2xl border border-border shadow-xs">
            <x-pagination :paginator="$testimonials" item-label="testimonials" />
        </div>
    @endif
</div>
