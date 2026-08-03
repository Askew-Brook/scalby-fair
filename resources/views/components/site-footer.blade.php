@php
    $siteSettings = globalSet('site');
    $footerNavigation = collect(\Statamic\Statamic::tag('nav:footer')->fetch());
@endphp

<footer class="bg-hedge-900 text-cream-50">
    <x-bunting />
    <div class="mx-auto max-w-7xl px-5 py-14 sm:px-8 sm:py-18">
        <div class="grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <a href="/" class="inline-flex text-cream-50" aria-label="Scalby Fair home"><x-site-mark /></a>
                @if($siteSettings?->footer_text)<p class="mt-6 max-w-md text-pretty text-hedge-100">{{ $siteSettings->footer_text }}</p>@endif
                @if($siteSettings?->charity_details)<p class="mt-4 text-sm text-hedge-100/80">{{ $siteSettings->charity_details }}</p>@endif
            </div>
            <nav class="lg:col-span-3" aria-label="Footer navigation">
                <p class="text-sm font-semibold tracking-[0.14em] text-wheat-300 uppercase">Useful links</p>
                <ul class="mt-4 grid gap-2" role="list">
                    @foreach($footerNavigation as $item)<li><a class="font-semibold text-hedge-100 hover:text-wheat-300" href="{{ $item['url'] }}">{{ $item['title'] }}</a></li>@endforeach
                </ul>
            </nav>
            <div class="lg:col-span-4">
                <p class="text-sm font-semibold tracking-[0.14em] text-wheat-300 uppercase">Contact</p>
                <div class="mt-4 grid gap-2 text-hedge-100">
                    @if($siteSettings?->contact_email)<a class="font-semibold hover:text-wheat-300" href="mailto:{{ $siteSettings->contact_email }}">{{ $siteSettings->contact_email }}</a>@endif
                    @if($siteSettings?->contact_phone)<a class="font-semibold hover:text-wheat-300" href="tel:{{ preg_replace('/[^+0-9]/', '', $siteSettings->contact_phone) }}">{{ $siteSettings->contact_phone }}</a>@endif
                    @if($siteSettings?->postal_address)<p class="whitespace-pre-line">{{ $siteSettings->postal_address }}</p>@endif
                    @if(!$siteSettings?->contact_email && !$siteSettings?->contact_phone)<a class="font-semibold hover:text-wheat-300" href="/contact">Contact the Fair committee</a>@endif
                </div>
                @if(collect($siteSettings?->social_links)->isNotEmpty())
                    <ul class="mt-6 flex flex-wrap gap-4" role="list">
                        @foreach($siteSettings->social_links as $social)<li><a class="font-semibold underline underline-offset-4 hover:text-wheat-300" href="{{ $social['url'] }}" rel="me">{{ $social['label'] }}</a></li>@endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="mt-12 flex flex-col gap-3 border-t border-cream-50/15 pt-6 text-sm text-hedge-100/75 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ $siteSettings?->organisation_name ?: 'Scalby Fair' }}</p>
            @if($siteSettings?->askew_brook_credit)<p>Website by <a href="{{ $siteSettings->askew_brook_credit }}" class="font-semibold text-cream-50 hover:text-wheat-300" target="_blank" rel="noopener">Askew Brook<span class="sr-only"> (opens in a new tab)</span></a></p>@endif
        </div>
    </div>
</footer>
