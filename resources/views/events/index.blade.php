@php
    use Statamic\Facades\Entry;
    use Statamic\Facades\Term;

    $selectedType = request('type');
    $period = in_array(request('period'), ['upcoming', 'past'], true) ? request('period') : 'upcoming';
    $cutoff = now()->startOfDay()->format('Y-m-d H:i');
    $query = Entry::query()->where('collection', 'events')->whereStatus('published');

    if ($selectedType) {
        $query->whereTaxonomy("event_types::$selectedType");
    }

    $period === 'past'
        ? $query->where('start_at', '<', $cutoff)->orderBy('start_at', 'desc')
        : $query->where('start_at', '>=', $cutoff)->orderBy('start_at', 'asc');

    $events = $query->get();
    $groupedEvents = $events->groupBy(fn ($event) => \Illuminate\Support\Carbon::parse($event->start_at)->toDateString());
    $eventTypes = Term::query()->where('taxonomy', 'event_types')->get()->sortBy('title');
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description" :share-image="$share_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" />
        <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-18">
            <x-breadcrumbs :items="[['title' => $title]]" />

            <form method="get" class="mt-10 grid gap-5 border-y border-hedge-700/20 bg-cream-100 px-5 py-6 sm:grid-cols-3 sm:items-end" aria-label="Filter events">
                <div>
                    <label class="field-label" for="event-type">Event type</label>
                    <select class="field-control" id="event-type" name="type">
                        <option value="">All event types</option>
                        @foreach($eventTypes as $eventType)<option value="{{ $eventType->slug() }}" @selected($selectedType === $eventType->slug())>{{ $eventType->title }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label" for="event-period">When</label>
                    <select class="field-control" id="event-period" name="period">
                        <option value="upcoming" @selected($period === 'upcoming')>Upcoming events</option>
                        <option value="past" @selected($period === 'past')>Past events</option>
                    </select>
                </div>
                <button class="min-h-12 bg-hedge-700 px-5 py-3 font-semibold text-white hover:bg-hedge-800" type="submit">Apply filters</button>
            </form>

            @if($events->isNotEmpty())
                <div class="mt-12 grid gap-14">
                    @foreach($groupedEvents as $day => $dayEvents)
                        <section aria-labelledby="day-{{ $day }}">
                            <h2 id="day-{{ $day }}" class="border-b-2 border-hedge-700 pb-3 font-serif text-3xl font-semibold tracking-tight text-hedge-900">{{ \Illuminate\Support\Carbon::parse($day)->format('l j F Y') }}</h2>
                            <div class="mt-7 grid gap-8 lg:grid-cols-2">@foreach($dayEvents as $event)<x-event-card :event="$event" />@endforeach</div>
                        </section>
                    @endforeach
                </div>
            @else
                <x-empty-state class="mt-12" heading="No events match those filters" text="Try another event type or switch between upcoming and past events.">
                    <x-button href="/events" variant="secondary" class="mt-6">Clear filters</x-button>
                </x-empty-state>
            @endif
        </div>
    </main>
</x-layouts.app>
