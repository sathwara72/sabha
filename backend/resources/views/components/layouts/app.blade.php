@props([
    'title' => 'Sabha | Community for Businesses',
    'description' => 'A community platform where people create accounts, list their businesses, and connect through events and workshops.',
    'image' => null,
    'noindex' => false,
])

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full scroll-smooth antialiased {{ app()->getLocale() === 'gu' ? 'lang-gu' : '' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}" />
    @if ($noindex)
        <meta name="robots" content="noindex, nofollow" />
    @endif
    <link rel="canonical" href="{{ url()->current() }}" />
    <link rel="icon" href="{{ asset('logo.png') }}" />
    <link rel="shortcut icon" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}" />

    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Sabha" />
    <meta property="og:title" content="{{ $title }}" />
    <meta property="og:description" content="{{ $description }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ $image ?: asset('logo.png') }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $title }}" />
    <meta name="twitter:description" content="{{ $description }}" />
    <meta name="twitter:image" content="{{ $image ?: asset('logo.png') }}" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body
    class="flex min-h-full flex-col bg-background text-foreground selection:bg-primary/15 selection:text-primary"
    x-data
    x-init="
        if (new URLSearchParams(window.location.search).get('login') === '1') {
            $store.auth.openLogin();
            window.history.replaceState({}, '', window.location.pathname);
        }
    "
>
    <x-navbar />
    <main class="flex-1 pt-16">
        {{ $slot }}
    </main>
    <x-footer />
    <x-login-modal />
    <x-register-modal />
    @livewireScripts
</body>
</html>
