@props([
    'labels' => [],
    'values' => [],
    'color' => 'primary',
    'height' => 140,
    'emptyText' => 'No data yet',
])

@php
    // Literal Tailwind classes (not built from $color at runtime) so the
    // JIT scanner picks them up — same constraint as member-title-badge.
    $barClasses = [
        'primary' => 'bg-primary',
        'emerald' => 'bg-emerald-500',
        'amber' => 'bg-amber-500',
        'rose' => 'bg-rose-500',
        'indigo' => 'bg-indigo-500',
        'sky' => 'bg-sky-500',
        'violet' => 'bg-violet-500',
    ];
    $barClass = $barClasses[$color] ?? $barClasses['primary'];
    $max = max(array_merge($values, [0]));
@endphp

@if ($max <= 0)
    <div class="flex items-center justify-center text-[12px] text-muted-foreground italic" style="height: {{ $height }}px">
        {{ $emptyText }}
    </div>
@else
    <div class="flex items-end gap-2" style="height: {{ $height }}px">
        @foreach ($labels as $i => $label)
            @php $value = $values[$i] ?? 0; @endphp
            <div class="flex-1 flex flex-col items-center justify-end h-full gap-1 min-w-0">
                <span class="text-[12px] font-bold text-foreground">{{ $value }}</span>
                <div class="w-full rounded-t-md {{ $barClass }} transition-all" style="height: {{ $max > 0 ? max(4, round($value / $max * ($height - 36))) : 4 }}px"></div>
                <span class="text-[12px] text-muted-foreground truncate w-full text-center">{{ $label }}</span>
            </div>
        @endforeach
    </div>
@endif
