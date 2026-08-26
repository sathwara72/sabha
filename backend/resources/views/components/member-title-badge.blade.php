@props(['title' => null, 'fallback' => null])

@php
    // Tailwind's JIT scanner needs full literal class strings present in the
    // source — a dynamic "bg-{$color}-50" concatenation would be purged, so
    // every color option is spelled out here.
    $palette = [
        'primary' => 'bg-primary-soft text-primary border-primary/10',
        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
        'rose' => 'bg-rose-50 text-rose-600 border-rose-200',
        'indigo' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
        'sky' => 'bg-sky-50 text-sky-700 border-sky-200',
        'violet' => 'bg-violet-50 text-violet-700 border-violet-200',
        'slate' => 'bg-slate-100 text-slate-700 border-slate-200',
    ];
@endphp

@if ($title)
    <span {{ $attributes->class(['inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[12px] font-bold uppercase tracking-wide', $palette[$title->badge_color] ?? $palette['primary']]) }}>
        {{ $title->name }}
    </span>
@elseif ($fallback)
    <span {{ $attributes->class(['inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[12px] font-bold uppercase tracking-wide', $palette['primary']]) }}>
        {{ $fallback }}
    </span>
@endif
