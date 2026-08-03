@props(['title' => null, 'seoTitle' => null, 'seoDescription' => null, 'shareImage' => null])

@php
    $siteSettings = globalSet('site');
    $title = \Statamic\View\Blade\value($title);
    $seoTitle = \Statamic\View\Blade\value($seoTitle);
    $seoDescription = \Statamic\View\Blade\value($seoDescription);
    $shareImage = \Statamic\View\Blade\value($shareImage);
    $siteName = \Statamic\View\Blade\value($siteSettings?->site_name) ?: 'Scalby Fair';
    $resolvedTitle = $seoTitle ?: ($title ? "$title | $siteName" : (\Statamic\View\Blade\value($siteSettings?->default_seo_title) ?: $siteName));
    $resolvedDescription = $seoDescription ?: \Statamic\View\Blade\value($siteSettings?->default_seo_description);
    $resolvedShareImage = $shareImage ?: \Statamic\View\Blade\value($siteSettings?->default_share_image);
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $resolvedTitle }}</title>
        @if ($resolvedDescription)<meta name="description" content="{{ $resolvedDescription }}">@endif
        <link rel="canonical" href="{{ url()->current() }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ $siteName }}">
        <meta property="og:title" content="{{ $resolvedTitle }}">
        @if ($resolvedDescription)<meta property="og:description" content="{{ $resolvedDescription }}">@endif
        <meta property="og:url" content="{{ url()->current() }}">
        @if ($resolvedShareImage)<meta property="og:image" content="{{ $resolvedShareImage->url() }}">@endif
        <meta name="twitter:card" content="summary_large_image">
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => \Statamic\View\Blade\value($siteSettings?->organisation_name) ?: $siteName,
            'url' => url('/'),
            'email' => \Statamic\View\Blade\value($siteSettings?->contact_email) ?: null,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @stack('schema')

        @livewireStyles
        @vite(['resources/css/site.css', 'resources/js/site.js'])
    </head>
    <body class="paper-texture min-h-dvh bg-cream-50 font-sans text-ink antialiased">
        <div class="site-shell">
            <a href="#main-content" class="fixed top-3 left-3 z-[100] -translate-y-24 bg-hedge-900 px-4 py-2 font-semibold text-white focus:translate-y-0">Skip to content</a>
            <x-site-header />
            {{ $slot }}
            <x-askew-brook-banner />
            <x-site-footer />
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
