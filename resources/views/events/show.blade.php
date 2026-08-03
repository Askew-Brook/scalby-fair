@php
    use Statamic\Facades\Entry;
    $startAt = $page->start_at ? \Illuminate\Support\Carbon::parse($page->start_at) : null;
    $endAt = $page->end_at ? \Illuminate\Support\Carbon::parse($page->end_at) : null;
    $status = \Statamic\View\Blade\value($page->event_status) ?: 'scheduled';
    $timeTbc = (bool) \Statamic\View\Blade\value($page->time_tbc);
    $isFree = (bool) \Statamic\View\Blade\value($page->is_free);
    $priceValue = \Statamic\View\Blade\value($page->price);
    $bookingInformation = \Statamic\View\Blade\value($page->booking_information);
    $organiserValue = \Statamic\View\Blade\value($page->organiser);
    $bookingStatus = \Statamic\View\Blade\value($page->booking_status);
    $bookingUrl = \Statamic\View\Blade\value($page->booking_url);
    $mapUrl = \Statamic\View\Blade\value($page->map_url);
    $contactName = \Statamic\View\Blade\value($page->contact_name);
    $contactEmail = \Statamic\View\Blade\value($page->contact_email);
    $contactPhone = \Statamic\View\Blade\value($page->contact_phone);
    $eventImage = $page->featured_image;
    $galleryItems = collect($page->gallery ?? []);
    $downloadItems = collect($page->downloads ?? []);
    $typeSlugs = collect($page->event_types ?? [])->map->slug();
    $relatedEvents = Entry::query()->where('collection', 'events')->whereStatus('published')->get()
        ->reject(fn ($event) => $event->id() === $page->id())
        ->filter(fn ($event) => collect($event->event_types ?? [])->map->slug()->intersect($typeSlugs)->isNotEmpty())
        ->sortBy('start_at')->take(3);
    $eventSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => $title,
        'description' => $summary,
        'startDate' => $startAt?->toIso8601String(),
        'endDate' => $endAt?->toIso8601String(),
        'eventStatus' => match($status) {
            'cancelled' => 'https://schema.org/EventCancelled',
            'postponed' => 'https://schema.org/EventPostponed',
            default => 'https://schema.org/EventScheduled',
        },
        'location' => ['@type' => 'Place', 'name' => $location, 'address' => $address],
        'url' => url()->current(),
        'isAccessibleForFree' => $isFree,
    ];
@endphp

@push('schema')<script type="application/ld+json">{!! json_encode($eventSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>@endpush

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description ?: $summary" :share-image="$share_image ?: $eventImage">
    <main id="main-content">
        @if($status !== 'scheduled')
            <aside class="border-y-4 border-barn-700 bg-barn-600 text-white" role="status" aria-label="Event status">
                <div class="mx-auto flex max-w-7xl flex-col gap-2 px-5 py-5 sm:flex-row sm:items-center sm:gap-5 sm:px-8">
                    <span class="w-fit border border-white/70 px-3 py-1 text-xs font-bold tracking-[0.16em] uppercase">{{ $status }}</span>
                    <p class="font-serif text-xl leading-7 text-pretty">{{ $status_message ?: 'This event is ' . $status . '. Please check this page for updates.' }}</p>
                </div>
            </aside>
        @endif
        <x-page-hero :title="$title" eyebrow="Event" :introduction="$summary" :image="$eventImage" />
        <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-18">
            <x-breadcrumbs :items="[['title' => 'What’s On', 'url' => '/events'], ['title' => $title]]" />
            <div class="mt-10 grid gap-12 lg:grid-cols-12">
                <article class="lg:col-span-7">
                    @if($content)<div class="prose">{!! \Statamic\Statamic::modify($content)->markdown() !!}</div>@endif

                    @if($galleryItems->isNotEmpty())
                        <section class="mt-12" aria-labelledby="event-gallery"><h2 id="event-gallery" class="font-serif text-3xl font-semibold tracking-tight text-hedge-900">Gallery</h2><div class="mt-6 grid gap-4 sm:grid-cols-2">@foreach($galleryItems as $image)<x-responsive-image :asset="$image" class="aspect-[4/3] w-full object-cover" />@endforeach</div></section>
                    @endif

                    @if($accessibility)
                        <section class="mt-12 border-l-4 border-wheat-300 bg-cream-100 p-6" aria-labelledby="access-heading"><h2 id="access-heading" class="font-serif text-2xl font-semibold tracking-tight text-hedge-900">Accessibility</h2><p class="mt-3 whitespace-pre-line text-pretty text-hedge-800">{{ $accessibility }}</p></section>
                    @endif

                    @if($downloadItems->isNotEmpty())
                        <section class="mt-12" aria-labelledby="downloads-heading"><h2 id="downloads-heading" class="font-serif text-2xl font-semibold tracking-tight text-hedge-900">Downloads</h2><div class="mt-3">@foreach($downloadItems as $download)<x-download-link :asset="$download" />@endforeach</div></section>
                    @endif
                </article>

                <aside class="lg:col-span-4 lg:col-start-9">
                    <div class="border-t-4 border-wheat-300 bg-cream-100 p-7">
                        <h2 class="font-serif text-2xl font-semibold tracking-tight text-hedge-900">Event details</h2>
                        <dl class="mt-6 grid gap-5">
                            @if($startAt)<div><dt class="text-xs font-semibold tracking-[0.12em] text-barn-600 uppercase">Date and time</dt><dd class="mt-1 font-semibold text-hedge-900">{{ $startAt->format('l j F Y') }}@if($timeTbc)<br><span class="font-normal">Time to be confirmed</span>@else, {{ $startAt->format('g:ia') }}@if($endAt)<br>to {{ $endAt->format($endAt->isSameDay($startAt) ? 'g:ia' : 'l j F Y, g:ia') }}@endif @endif</dd></div>@endif
                            @if($location)<div><dt class="text-xs font-semibold tracking-[0.12em] text-barn-600 uppercase">Location</dt><dd class="mt-1 font-semibold text-hedge-900">{{ $location }}@if($address)<span class="mt-1 block whitespace-pre-line font-normal text-hedge-800/80">{{ $address }}</span>@endif</dd></div>@endif
                            <div><dt class="text-xs font-semibold tracking-[0.12em] text-barn-600 uppercase">Admission</dt><dd class="mt-1 font-semibold text-hedge-900">{{ $isFree ? 'Free' : ($priceValue ?: 'See event information') }}</dd></div>
                            @if($bookingInformation)<div><dt class="text-xs font-semibold tracking-[0.12em] text-barn-600 uppercase">Booking</dt><dd class="mt-1 text-hedge-800">{{ $bookingInformation }}</dd></div>@endif
                            @if($organiserValue)<div><dt class="text-xs font-semibold tracking-[0.12em] text-barn-600 uppercase">Organiser</dt><dd class="mt-1 font-semibold text-hedge-900">{{ $organiserValue }}</dd></div>@endif
                        </dl>
                        @if($mapUrl)<x-button :href="$mapUrl" variant="secondary" external class="mt-6 w-full">Open map</x-button>@endif
                        @if($bookingStatus === 'available' && $bookingUrl)<x-button :href="$bookingUrl" external class="mt-4 w-full">{{ $booking_label ?: 'Book your place' }}</x-button>
                        @elseif($status === 'scheduled' && in_array($bookingStatus, ['sold_out', 'closed']))<p class="mt-5 border border-barn-600 px-4 py-3 font-semibold text-barn-700">{{ $bookingStatus === 'sold_out' ? 'This event is sold out.' : 'Booking is closed.' }}</p>@endif
                        <a href="/contact?event={{ urlencode($title) }}#contact-form" class="mt-5 inline-flex font-semibold text-barn-700 underline decoration-2 underline-offset-4">Ask the committee about this event</a>
                    </div>

                    @if($contactName || $contactEmail || $contactPhone)
                        <div class="mt-8 border-t border-hedge-700/20 pt-6"><h2 class="font-serif text-xl font-semibold text-hedge-900">Event contact</h2><div class="mt-3 grid gap-2 text-sm">@if($contactName)<p>{{ $contactName }}</p>@endif @if($contactEmail)<a class="font-semibold text-barn-700 underline underline-offset-4" href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif @if($contactPhone)<a class="font-semibold text-barn-700 underline underline-offset-4" href="tel:{{ preg_replace('/[^+0-9]/', '', $contactPhone) }}">{{ $contactPhone }}</a>@endif</div></div>
                    @endif
                </aside>
            </div>

            @if($relatedEvents->isNotEmpty())
                <section class="mt-18 border-t border-hedge-700/20 pt-12" aria-labelledby="related-events"><h2 id="related-events" class="font-serif text-3xl font-semibold tracking-tight text-hedge-900">Related events</h2><div class="mt-8 grid gap-8 lg:grid-cols-3">@foreach($relatedEvents as $event)<x-event-card :event="$event" />@endforeach</div></section>
            @endif
        </div>
    </main>
</x-layouts.app>
