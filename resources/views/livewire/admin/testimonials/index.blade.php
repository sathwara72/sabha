<div class="space-y-4 font-outfit">
    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">Referral Testimonials</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Moderate testimonials left by referral receivers on closed deals</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <div class="text-xs font-bold text-slate-700 bg-white rounded-xl px-3 py-1.5 border border-slate-200 shadow-2xs">
                Total: <span class="text-primary font-black ml-0.5">{{ $totalCount }}</span>
            </div>
            <div class="text-xs font-bold text-emerald-800 bg-emerald-50 rounded-xl px-3 py-1.5 border border-emerald-200 shadow-2xs">
                Visible: <span class="text-emerald-700 font-black ml-0.5">{{ $visibleCount }}</span>
            </div>
        </div>
    </div>

    {{-- Search Toolbar Card --}}
    <div class="bg-white p-2.5 sm:p-3 rounded-xl border border-slate-200/90 shadow-2xs">
        <div class="relative max-w-md">
            <x-icon name="search" wire:loading.remove wire:target="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
            <span wire:loading wire:target="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 animate-spin rounded-full border-2 border-primary border-t-transparent"></span>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search testimonials or member..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 pl-9 pr-3 text-xs font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary shadow-2xs"
            />
        </div>
    </div>

    {{-- Testimonials Area with Loading Overlay --}}
    <div class="relative min-h-[160px]">
        <x-loading-state target="search" message="Searching testimonials..." />

        @if ($testimonials->isEmpty())
            <div class="py-24 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-slate-200 italic shadow-xs">
                {{ $search ? 'No testimonials matching your search query.' : 'No testimonials submitted yet.' }}
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($testimonials as $t)
                    <div class="bg-white rounded-xl p-3 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-primary/30 transition-all flex flex-col justify-between space-y-2">
                        <div class="space-y-2">
                            {{-- Top: Reviewer & Status --}}
                            <div class="flex items-start justify-between gap-1.5">
                                <div class="min-w-0">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Reviewer</span>
                                    <h4 class="text-xs font-bold text-slate-900 truncate">{{ $t->receiver?->name ?? '—' }}</h4>
                                    <p class="text-[10px] text-slate-500 truncate">
                                        <span class="text-slate-400">for</span> <span class="font-semibold text-slate-700">{{ $t->giver?->name ?? '—' }}</span>
                                    </p>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-md border px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide shrink-0 {{ $t->display_testimonial ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $t->display_testimonial ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $t->display_testimonial ? 'Visible' : 'Hidden' }}
                                </span>
                            </div>

                            {{-- Deal Info Box --}}
                            <div class="rounded-lg bg-slate-50/90 p-2 space-y-0.5 border border-slate-100 text-[10.5px]">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Closed Value</span>
                                    <span class="font-bold text-emerald-700">{{ $t->amount ? '₹' . number_format((float) $t->amount) : 'Not specified' }}</span>
                                </div>
                                <div class="flex items-center justify-between pt-0.5 border-t border-slate-200/50">
                                    <span class="text-slate-500 font-medium">Contact</span>
                                    <span class="font-semibold text-slate-700 truncate max-w-[60%] text-right">{{ $t->contact_name ?: '—' }}</span>
                                </div>
                            </div>

                            {{-- Quote Text --}}
                            <div class="relative bg-blue-50/40 p-2 rounded-lg border border-blue-100/50">
                                <p class="text-[11px] text-slate-700 italic line-clamp-2 leading-snug">
                                    "{{ $t->testimonial }}"
                                </p>
                            </div>
                        </div>

                        {{-- Bottom Action Button --}}
                        <div class="pt-1.5 border-t border-slate-100">
                            <button
                                wire:click="toggleDisplay({{ $t->id }})"
                                class="w-full inline-flex items-center justify-center gap-1 rounded-lg py-1.5 px-2 text-[11px] font-bold border transition-all active:scale-[0.98] cursor-pointer shadow-2xs {{ $t->display_testimonial ? 'bg-rose-50 text-rose-600 border-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}"
                            >
                                @if ($t->display_testimonial)
                                    <x-icon name="eye-off" class="h-3 w-3" /> Hide from Business Page
                                @else
                                    <x-icon name="eye" class="h-3 w-3" /> Show on Business Page
                                @endif
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($testimonials->hasPages())
                <div class="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-xs">
                    <x-pagination :paginator="$testimonials" item-label="testimonials" />
                </div>
            @endif
        @endif
    </div>
</div>
