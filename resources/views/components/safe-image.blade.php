@props([
    'src' => null,
    'alt' => '',
    'imgClass' => 'h-full w-full object-cover',
    'containerClass' => 'relative h-full w-full overflow-hidden',
    'fallbackType' => 'generic',
    'title' => '',
    'date' => '',
    'blurBackdrop' => false,
])

@php
    $hasValidSrc = filled($src) && trim((string) $src) !== '';

    $parsedDate = null;
    if ($date) {
        try {
            $d = \Illuminate\Support\Carbon::parse($date);
            $parsedDate = ['monthShort' => strtoupper($d->format('M')), 'dayNum' => $d->format('d')];
        } catch (\Throwable $e) {
            $parsedDate = null;
        }
    }

    $initialLetter = strtoupper(mb_substr(trim($title ?: $alt ?: '?'), 0, 1));
@endphp

<div {{ $attributes->merge(['class' => $containerClass]) }}>
    @if ($hasValidSrc)
        @if ($blurBackdrop)
            {{-- Ambient Blurred Background --}}
            <img
                src="{{ $src }}"
                alt=""
                aria-hidden="true"
                loading="lazy"
                class="absolute inset-0 h-full w-full object-cover blur-xl scale-125 opacity-70 filter pointer-events-none"
            />
            <div class="absolute inset-0 bg-slate-950/30 pointer-events-none"></div>

            {{-- Full Foreground Image without cropping --}}
            <img
                src="{{ $src }}"
                alt="{{ $alt ?: $title }}"
                loading="lazy"
                onerror="this.style.display='none'; const fb = this.closest('.relative').querySelector('.fallback-container'); if(fb) { fb.classList.remove('hidden'); fb.style.display='block'; }"
                class="relative z-10 h-full w-full object-contain drop-shadow-md transition-transform duration-500 group-hover:scale-105"
            />
        @else
            <img
                src="{{ $src }}"
                alt="{{ $alt ?: $title }}"
                loading="lazy"
                onerror="this.style.display='none'; if(this.nextElementSibling) { this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.style.display='block'; }"
                class="{{ $imgClass }}"
            />
        @endif
    @endif

    <div class="fallback-container {{ $hasValidSrc ? 'hidden' : 'block' }} h-full w-full relative z-10">
        @switch($fallbackType)
            @case('event')
                <div class="h-full w-full bg-gradient-to-br from-[#00379D] via-[#0F3459] to-[#091E36] flex flex-col items-center justify-center p-3 relative overflow-hidden select-none">
                    <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 12px 12px;"></div>
                    <div class="absolute -top-8 -right-8 w-24 h-24 bg-[#1d4ed8]/30 rounded-full blur-xl pointer-events-none"></div>
                    <div class="absolute -bottom-8 -left-8 w-24 h-24 bg-[#00379D]/40 rounded-full blur-xl pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col items-center justify-center">
                        @if ($parsedDate)
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white shadow-2xl overflow-hidden border border-white/70 flex flex-col items-center transform transition-transform duration-300 group-hover:scale-105">
                                <div class="w-full bg-gradient-to-r from-[#00379D] to-[#1d4ed8] py-0.5 text-center shadow-xs">
                                    <span class="text-[12px] font-black tracking-widest text-white uppercase block leading-none">{{ $parsedDate['monthShort'] }}</span>
                                </div>
                                <div class="flex-1 flex items-center justify-center bg-white w-full">
                                    <span class="text-xl sm:text-2xl font-black text-[#0F3459] leading-none tracking-tight">{{ $parsedDate['dayNum'] }}</span>
                                </div>
                            </div>
                        @else
                            <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-md border border-white/25 flex items-center justify-center text-white shadow-lg">
                                <x-icon name="calendar" class="h-6 w-6" />
                            </div>
                        @endif
                        @if ($title)
                            <span class="mt-2.5 text-[12px] font-bold text-white/95 truncate max-w-[85%] bg-[#0F3459]/75 backdrop-blur-xs px-2.5 py-0.5 rounded-full border border-white/20">{{ $title }}</span>
                        @endif
                    </div>
                </div>
                @break

            @case('business')
                <div class="h-full w-full bg-gradient-to-br from-[#00379D] via-[#0F3459] to-[#113552] flex items-center justify-center p-2 relative overflow-hidden select-none text-white font-extrabold">
                    <div class="absolute inset-0 opacity-15 pointer-events-none" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 10px 10px;"></div>
                    <div class="absolute -bottom-6 -right-6 w-16 h-16 bg-[#2563eb]/25 rounded-full blur-lg pointer-events-none"></div>
                    <div class="relative z-10 flex items-center justify-center">
                        @if ($initialLetter !== '?')
                            <span class="text-xl sm:text-2xl font-extrabold tracking-wider text-white drop-shadow-md">{{ $initialLetter }}</span>
                        @else
                            <x-icon name="building" class="h-[22px] w-[22px] text-white drop-shadow-md" />
                        @endif
                    </div>
                </div>
                @break

            @case('gallery')
                <div class="h-full w-full bg-gradient-to-br from-[#1d4ed8] via-[#00379D] to-[#0F3459] flex flex-col items-center justify-center p-3 relative overflow-hidden select-none">
                    <div class="absolute inset-0 opacity-15 pointer-events-none" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 12px 12px;"></div>
                    <div class="absolute -top-6 -left-6 w-20 h-20 bg-[#3b82f6]/30 rounded-full blur-lg pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col items-center justify-center text-white">
                        <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center shadow-lg mb-1">
                            <x-icon name="image" class="h-[22px] w-[22px]" />
                        </div>
                        @if ($title)
                            <span class="text-[12px] font-semibold text-white/90 line-clamp-1 max-w-[85%] text-center">{{ $title }}</span>
                        @endif
                    </div>
                </div>
                @break

            @case('avatar')
                <div class="h-full w-full bg-gradient-to-br from-[#00379D] to-[#0F3459] flex items-center justify-center text-white font-bold select-none">
                    @if ($initialLetter !== '?')
                        <span class="text-sm font-extrabold">{{ $initialLetter }}</span>
                    @else
                        <x-icon name="user" class="h-4 w-4" />
                    @endif
                </div>
                @break

            @case('banner')
                <div class="h-full w-full bg-gradient-to-r from-[#0F3459] via-[#00379D] to-[#091E36] flex items-center justify-center relative overflow-hidden select-none">
                    <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 16px 16px;"></div>
                    <div class="relative z-10 flex items-center gap-2 text-white/90 font-semibold text-sm">
                        <x-icon name="sparkles" class="h-[18px] w-[18px] text-sky-300" />
                        <span>{{ $title ?: 'SABHA Community' }}</span>
                    </div>
                </div>
                @break

            @default
                <div class="h-full w-full bg-gradient-to-br from-[#00379D] via-[#0F3459] to-[#113552] flex flex-col items-center justify-center p-3 relative overflow-hidden select-none">
                    <div class="absolute inset-0 opacity-15 pointer-events-none" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 12px 12px;"></div>
                    <div class="relative z-10 flex flex-col items-center justify-center text-white">
                        <x-icon name="sparkles" class="h-6 w-6 text-white/90 mb-1" />
                        @if ($title)
                            <span class="text-xs font-semibold text-white/90 line-clamp-1 max-w-[85%] text-center">{{ $title }}</span>
                        @endif
                    </div>
                </div>
        @endswitch
    </div>
</div>
