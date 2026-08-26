@props([
    // ['label' => string, 'value' => number, 'color' => palette key][]
    'segments' => [],
    'size' => 120,
])

@php
    $hex = [
        'primary' => 'var(--primary)',
        'emerald' => '#10b981',
        'amber' => '#f59e0b',
        'rose' => '#f43f5e',
        'indigo' => '#6366f1',
        'sky' => '#0ea5e9',
        'violet' => '#8b5cf6',
        'slate' => '#94a3b8',
    ];
    $swatchClasses = [
        'primary' => 'bg-primary',
        'emerald' => 'bg-emerald-500',
        'amber' => 'bg-amber-500',
        'rose' => 'bg-rose-500',
        'indigo' => 'bg-indigo-500',
        'sky' => 'bg-sky-500',
        'violet' => 'bg-violet-500',
        'slate' => 'bg-slate-400',
    ];

    $total = array_sum(array_column($segments, 'value'));
    $stops = [];
    $angle = 0;
    foreach ($segments as $seg) {
        if ($total <= 0 || $seg['value'] <= 0) {
            continue;
        }
        $color = $hex[$seg['color']] ?? $hex['slate'];
        $sweep = $seg['value'] / $total * 360;
        $stops[] = "{$color} {$angle}deg " . ($angle + $sweep) . 'deg';
        $angle += $sweep;
    }
    $gradient = $stops ? implode(', ', $stops) : '#e2e8f0 0deg 360deg';
@endphp

<div class="flex items-center gap-4">
    <div
        class="rounded-full shrink-0"
        style="width: {{ $size }}px; height: {{ $size }}px; background: conic-gradient({{ $gradient }});"
    >
        <div class="h-full w-full flex items-center justify-center">
            <div class="rounded-full bg-white flex items-center justify-center text-center" style="width: {{ round($size * 0.62) }}px; height: {{ round($size * 0.62) }}px;">
                <span class="text-sm font-extrabold text-foreground">{{ $total }}</span>
            </div>
        </div>
    </div>
    <div class="space-y-1.5 min-w-0">
        @foreach ($segments as $seg)
            <div class="flex items-center gap-1.5 text-[12px]">
                <span class="h-2.5 w-2.5 rounded-full shrink-0 {{ $swatchClasses[$seg['color']] ?? $swatchClasses['slate'] }}"></span>
                <span class="text-muted-foreground truncate">{{ $seg['label'] }}</span>
                <span class="font-bold text-foreground">{{ $seg['value'] }}</span>
            </div>
        @endforeach
    </div>
</div>
