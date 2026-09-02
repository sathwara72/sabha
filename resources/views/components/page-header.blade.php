@props([
    'kicker' => null,
    'title',
    'subtitle' => null,
    'align' => 'left',
])

@php $centered = $align === 'center'; @endphp

<section class="hero-surface relative border-b border-border">
    <div class="mx-auto max-w-7xl px-6 py-3 lg:px-8 lg:py-6">
        <div class="{{ $centered ? 'flex flex-col items-center text-center' : 'flex flex-col gap-5 md:flex-row md:items-end md:justify-between' }}">
            <div class="max-w-2xl animate-fade-in">
                @if ($kicker)
                    <div class="{{ $centered ? 'mb-3 flex items-center justify-center gap-2.5' : 'mb-3 flex items-center gap-2.5' }}">
                        <span class="h-4 w-1.5 rounded-full bg-accent"></span>
                        <span class="text-sm font-semibold text-accent">{{ $kicker }}</span>
                    </div>
                @endif
                <h1 class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="mt-2 text-base leading-relaxed text-muted">{{ $subtitle }}</p>
                @endif
            </div>

            @isset($right)
                <div class="shrink-0">{{ $right }}</div>
            @endisset
        </div>

        @isset($slot)
            @if (trim($slot))
                <div class="mt-6">{{ $slot }}</div>
            @endif
        @endisset
    </div>
</section>
