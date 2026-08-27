@props(['paginator', 'itemLabel' => 'items', 'pageName' => 'page'])

@php
    $current = $paginator->currentPage();
    $total = $paginator->lastPage();
    $totalItems = $paginator->total();
    $perPage = $paginator->perPage();

    if ($total <= 7) {
        $pages = range(1, $total);
    } elseif ($current <= 4) {
        $pages = [1, 2, 3, 4, 5, '...', $total];
    } elseif ($current >= $total - 3) {
        $pages = [1, '...', $total - 4, $total - 3, $total - 2, $total - 1, $total];
    } else {
        $pages = [1, '...', $current - 1, $current, $current + 1, '...', $total];
    }

    $startItem = ($current - 1) * $perPage + 1;
    $endItem = min($current * $perPage, $totalItems);
@endphp

@if ($total > 1)
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-border bg-slate-50/50 rounded-b-2xl">
        <p class="text-xs text-slate-500 font-medium">
            Showing <span class="font-extrabold text-slate-800">{{ $startItem }}</span> to
            <span class="font-extrabold text-slate-800">{{ $endItem }}</span> of
            <span class="font-extrabold text-slate-800">{{ $totalItems }}</span> {{ $itemLabel }}
        </p>
        <div class="flex items-center gap-1.5 flex-wrap justify-center">
            <button
                wire:click="previousPage('{{ $pageName }}')"
                @if ($current === 1) disabled @endif
                class="inline-flex items-center justify-center rounded-xl border border-border bg-white px-3 py-1.5 text-xs font-bold text-slate-700 transition-all hover:bg-slate-100 hover:border-slate-300 disabled:opacity-40 disabled:pointer-events-none cursor-pointer shadow-xs active:scale-95"
            >Previous</button>

            @foreach ($pages as $p)
                @if ($p === '...')
                    <span class="px-1 text-xs text-slate-400 font-bold">...</span>
                @else
                    <button
                        wire:click="gotoPage({{ $p }}, '{{ $pageName }}')"
                        class="h-7 min-w-[28px] px-2 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-xs flex items-center justify-center {{ $current === $p ? 'bg-primary text-white border border-primary shadow-sm shadow-primary/20 scale-105' : 'border border-border bg-white text-slate-600 hover:bg-slate-100 hover:text-foreground hover:border-slate-300 active:scale-95' }}"
                    >{{ $p }}</button>
                @endif
            @endforeach

            <button
                wire:click="nextPage('{{ $pageName }}')"
                @if ($current === $total) disabled @endif
                class="inline-flex items-center justify-center rounded-xl border border-border bg-white px-3 py-1.5 text-xs font-bold text-slate-700 transition-all hover:bg-slate-100 hover:border-slate-300 disabled:opacity-40 disabled:pointer-events-none cursor-pointer shadow-xs active:scale-95"
            >Next</button>
        </div>
    </div>
@endif
