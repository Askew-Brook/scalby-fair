@php($siteSettings = globalSet('site'))

@if($siteSettings?->askew_brook_banner_enabled)
    <aside class="border-t border-hedge-700/15 bg-wheat-300 text-hedge-900" aria-labelledby="askew-brook-heading">
        <div class="mx-auto grid max-w-7xl gap-7 px-5 py-9 sm:px-8 sm:py-11 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-3">
                @if($siteSettings?->askew_brook_logo)
                    <x-responsive-image :asset="$siteSettings->askew_brook_logo" :width="420" :height="186" sizes="260px" class="h-auto w-52 object-contain object-left sm:w-60" />
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
                    <a href="{{ $siteSettings->askew_brook_url }}" class="inline-flex min-h-12 items-center justify-center border border-hedge-900 px-5 py-3 font-semibold text-hedge-900 hover:bg-hedge-900 hover:text-cream-50" target="_blank" rel="noopener">{{ $siteSettings->askew_brook_label }}<span class="sr-only"> (opens in a new tab)</span></a>
                </div>
            @endif
        </div>
    </aside>
@endif
