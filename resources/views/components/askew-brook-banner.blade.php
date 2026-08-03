@php
    $siteSettings = globalSet('site');
    $askewBrookLogo = \Statamic\View\Blade\value($siteSettings?->askew_brook_logo);
@endphp

@if($siteSettings?->askew_brook_banner_enabled)
    <aside class="relative isolate overflow-hidden border-t border-hedge-700/15 bg-wheat-300 text-hedge-900" aria-labelledby="askew-brook-heading">
        <div class="absolute -top-28 -right-24 -z-10 size-80 rounded-full border border-hedge-900/10" aria-hidden="true"></div>
        <div class="mx-auto grid max-w-7xl gap-7 px-5 py-9 sm:px-8 sm:py-11 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-3">
                @if($askewBrookLogo)
                    <p class="mb-3 text-xs font-semibold tracking-[0.16em] text-hedge-900/65 uppercase">Website partner</p>
                    <img src="{{ $askewBrookLogo->url() }}" width="2542" height="402" alt="Askew Brook" loading="lazy" decoding="async" class="h-auto w-full max-w-72 object-contain object-left">
                @else
                    <p class="font-serif text-3xl">Askew Brook</p>
                @endif
            </div>
            <div class="lg:col-span-6">
                @if($siteSettings?->askew_brook_heading)<h2 id="askew-brook-heading" class="font-serif text-2xl tracking-tight text-balance sm:text-3xl">{{ $siteSettings->askew_brook_heading }}</h2>@endif
                @if($siteSettings?->askew_brook_text)<p class="mt-2 max-w-3xl text-pretty text-hedge-900/80">{{ $siteSettings->askew_brook_text }}</p>@endif
            </div>
            @if($siteSettings?->askew_brook_url && $siteSettings?->askew_brook_label)
                <div class="lg:col-span-3 lg:text-right">
                    <a href="{{ $siteSettings->askew_brook_url }}" class="group inline-flex min-h-12 items-center justify-center border-2 border-hedge-900 px-5 py-3 font-semibold text-hedge-900 hover:-translate-y-0.5 hover:bg-hedge-900 hover:text-cream-50" target="_blank" rel="noopener">{{ $siteSettings->askew_brook_label }}<span class="ml-2 inline-block transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true">↗</span><span class="sr-only"> (opens in a new tab)</span></a>
                </div>
            @endif
        </div>
    </aside>
@endif
