@php
    use Statamic\Facades\Entry;
    $siteSettings = globalSet('site');
    $currentFair = Entry::query()->where('collection', 'fair_years')->whereStatus('published')->orderBy('date', 'desc')->first();
    $resolve = static fn ($value) => \Statamic\View\Blade\value($value);
    $action = match ($page->id()) {
        'stall-bookings' => [$resolve($currentFair?->stall_booking_url) ?: $resolve($siteSettings?->default_stall_booking_url), 'Continue to stall booking'],
        'walk-bookings' => [$resolve($currentFair?->walk_booking_url) ?: $resolve($siteSettings?->default_walk_booking_url), 'Continue to walk registration'],
        'donate' => [$resolve($siteSettings?->donation_url), 'Donate securely'],
        'newsletter' => [$resolve($siteSettings?->newsletter_url), 'Sign up for updates'],
        default => [null, null],
    };
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description" :share-image="$share_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" :image="$featured_image" :supporting-image="$supporting_image" />
        <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-18">
            <x-breadcrumbs :items="[['title' => $title]]" />
            <div class="mt-10 grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    @if($content)<div class="prose">{!! \Statamic\Statamic::modify($content)->markdown() !!}</div>@endif
                </div>
                <aside class="h-fit border-t-4 border-wheat-300 bg-cream-100 p-7 shadow-sm lg:sticky lg:top-6 lg:col-span-4 lg:col-start-9">
                    @if($action[0])
                        <h2 class="font-serif text-2xl font-semibold tracking-tight text-hedge-900">Ready to continue?</h2>
                        <p class="mt-3 text-pretty text-hedge-800/80">You will leave this website to complete the next step using the official external service.</p>
                        <x-button :href="$action[0]" external class="mt-6 w-full">{{ $action[1] }}</x-button>
                    @else
                        <h2 class="font-serif text-2xl font-semibold tracking-tight text-hedge-900">Not open just yet</h2>
                        <p class="mt-3 text-pretty text-hedge-800/80">The committee will add the official link here when this service is available.</p>
                        <x-button href="/contact" variant="secondary" class="mt-6 w-full">Contact the committee</x-button>
                    @endif
                </aside>
            </div>
        </div>
    </main>
</x-layouts.app>
