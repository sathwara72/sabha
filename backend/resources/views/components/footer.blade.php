@php
    $settings = \Illuminate\Support\Facades\Cache::remember('site.settings', 60, function () {
        return \App\Models\Setting::all()->pluck('value', 'key');
    });

    $rawSocials = [
        ['name' => 'Instagram', 'url' => $settings->get('instagram_url'), 'hover' => 'hover:text-pink-600 hover:border-pink-500'],
        ['name' => 'WhatsApp', 'url' => $settings->get('whatsapp_url'), 'hover' => 'hover:text-emerald-600 hover:border-emerald-500'],
        ['name' => 'Facebook', 'url' => $settings->get('facebook_url'), 'hover' => 'hover:text-blue-600 hover:border-blue-500'],
    ];

    $configuredSocials = collect($rawSocials)->filter(fn ($s) => filled($s['url']))->values();

    $activeSocials = $configuredSocials->isNotEmpty() ? $configuredSocials : collect([
        ['name' => 'Instagram', 'url' => 'https://instagram.com', 'hover' => 'hover:text-pink-600 hover:border-pink-500'],
        ['name' => 'WhatsApp', 'url' => 'https://wa.me/919123456789', 'hover' => 'hover:text-emerald-600 hover:border-emerald-500'],
        ['name' => 'Facebook', 'url' => 'https://facebook.com', 'hover' => 'hover:text-blue-600 hover:border-blue-500'],
    ]);

    $contactEmail = $settings->get('contact_email') ?: 'hello@sabha.global';
    $contactPhone = $settings->get('contact_phone') ?: '+91 95377 33567';
    $contactAddress = $settings->get('contact_address') ?: 'Ahmedabad, Gujarat, India';
@endphp

<footer class="border-t border-border bg-white font-outfit">
    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="grid grid-cols-1 gap-12 pb-12 md:grid-cols-2 lg:grid-cols-3">
            {{-- Brand --}}
            <div class="space-y-4">
                <a href="/" class="flex items-center gap-2.5">
                    <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-10 w-10 rounded-full object-contain" />
                    <span class="text-xl font-bold tracking-tight text-primary-dark">SABHA</span>
                </a>
                <p class="max-w-xs text-sm leading-relaxed text-muted">
                    {{ __('site.footer.tagline') }}
                </p>
                <div class="flex gap-2.5">
                    @foreach ($activeSocials as $item)
                        @php
                            $targetUrl = $item['url'] ?: '#';
                            if ($targetUrl && !str_starts_with($targetUrl, 'http://') && !str_starts_with($targetUrl, 'https://') && !str_starts_with($targetUrl, 'wa.me')) {
                                $targetUrl = 'https://' . $targetUrl;
                            }
                        @endphp
                        <a href="{{ $targetUrl }}" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded-lg border border-border text-muted transition-all duration-200 {{ $item['hover'] }} hover:scale-105" aria-label="{{ $item['name'] }}" title="{{ $item['name'] }}">
                            <x-social-icon :name="$item['name']" class="h-4 w-4" />
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Platform links --}}
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-foreground">{{ __('site.footer.platform') }}</h3>
                <ul class="space-y-3">
                    @foreach ([
                        ['name' => __('site.footer.link_businesses'), 'href' => '/businesses'],
                        ['name' => __('site.footer.link_events'), 'href' => '/events'],
                        ['name' => __('site.footer.link_gallery'), 'href' => '/gallery'],
                        ['name' => __('site.footer.link_about'), 'href' => '/about'],
                    ] as $link)
                        <li>
                            <a href="{{ $link['href'] }}" class="text-sm text-muted transition-colors hover:text-primary">{{ $link['name'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact --}}
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-foreground">{{ __('site.footer.contact') }}</h3>
                <ul class="space-y-3 text-sm text-muted">
                    <li class="flex items-start gap-3">
                        <x-icon name="map-pin" class="h-4 w-4 shrink-0 text-primary mt-0.5" />
                        <span>{{ $contactAddress }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <x-icon name="phone" class="h-4 w-4 shrink-0 text-primary" />
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}" class="hover:text-primary transition-colors">{{ $contactPhone }}</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <x-icon name="mail" class="h-4 w-4 shrink-0 text-primary" />
                        <a href="mailto:{{ $contactEmail }}" class="hover:text-primary transition-colors">{{ $contactEmail }}</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="flex flex-col items-center justify-between gap-4 border-t border-border pt-8 text-sm text-muted md:flex-row">
            <p>{{ str_replace('2026', (string) now()->year, __('site.footer.copyright')) }}</p>
            <div class="flex gap-6">
                <a href="/privacy" class="transition-colors hover:text-primary">{{ __('site.footer.privacy') }}</a>
                <a href="/terms" class="transition-colors hover:text-primary">{{ __('site.footer.terms') }}</a>
            </div>
        </div>
    </div>
</footer>
