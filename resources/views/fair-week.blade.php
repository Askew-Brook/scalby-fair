@php
    use Illuminate\Support\Carbon;
    use Statamic\Facades\Asset;
    use Statamic\Facades\Entry;

    $weekStarts = Carbon::parse('2026-06-12')->startOfDay();
    $weekEnds = Carbon::parse('2026-06-21')->endOfDay();
    $events = Entry::query()->where('collection', 'events')->whereStatus('published')->get()
        ->filter(fn ($event) => Carbon::parse($event->start_at)->betweenIncluded($weekStarts, $weekEnds))
        ->sortBy('start_at')
        ->values();
    $groupedEvents = $events->groupBy(fn ($event) => Carbon::parse($event->start_at)->toDateString());
    $featureOrder = collect(['event-ceilidh-2026', 'event-soul-rida-2026', 'event-fair-day-2026']);
    $featuredEvents = $events->filter(fn ($event) => $featureOrder->contains($event->id()))
        ->sortBy(fn ($event) => $featureOrder->search($event->id()));
    $competition = Entry::query()->where('collection', 'photography_competitions')->whereStatus('published')->orderBy('year', 'desc')->first();
    $pageContent = \Statamic\View\Blade\value($content);
    $ceilidhImage = Asset::find('assets::SF_Celidh_2025.jpeg');
    $wineImage = Asset::find('assets::SF_Wine-Tasting-2025.jpeg');
    $soulImage = Asset::find('assets::Soul-Rida-Scalby-Fair-2026-Upscaler-2x-scale.webp');
    $nancyImage = Asset::find('assets::SF-2026-Nancy-Tilley-on-stageRS-e1782569879860.webp');
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description" :share-image="$share_image ?: $featured_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" :image="$featured_image" :supporting-image="$supporting_image" />

        <section class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-20" aria-labelledby="fair-week-intro">
            <x-breadcrumbs :items="[['title' => $title]]" />
            <div class="mt-10 grid gap-10 lg:grid-cols-12 lg:items-start">
                <div class="lg:col-span-7">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">One village. Ten days. A hundred reasons to join in.</p>
                    <h2 id="fair-week-intro" class="mt-3 font-serif text-4xl leading-tight tracking-tight text-balance text-hedge-900 sm:text-5xl">The build-up is part of the celebration</h2>
                    @if($pageContent)<div class="prose mt-6 max-w-3xl">{!! \Statamic\Statamic::modify($pageContent)->markdown() !!}</div>@endif
                </div>
                <aside class="border-t-4 border-wheat-300 bg-cream-100 p-7 lg:col-span-4 lg:col-start-9">
                    <p class="text-xs font-semibold tracking-[0.16em] text-barn-600 uppercase">At a glance</p>
                    <p class="mt-3 font-serif text-4xl text-hedge-900">{{ $events->count() }} events</p>
                    <p class="mt-2 text-hedge-800/80">From Friday 12 to Saturday 20 June, with the wider Fair Week running through Sunday 21 June.</p>
                    <a href="#programme" class="mt-6 inline-flex font-semibold text-barn-700 underline decoration-2 underline-offset-4">Jump to the full programme ↓</a>
                </aside>
            </div>
        </section>

        @if($featuredEvents->isNotEmpty())
            <section class="bg-hedge-900 py-16 text-cream-50 sm:py-20" aria-labelledby="featured-events-heading">
                <div class="mx-auto max-w-7xl px-5 sm:px-8">
                    <x-section-heading id="featured-events-heading" eyebrow="Start here" title="Fair Week highlights" introduction="Three very different moments that capture the range of the week—from the first dance to the final parade." theme="dark" />
                    <div class="mt-10 grid gap-7 xl:grid-cols-3">
                        @foreach($featuredEvents as $event)
                            <x-event-card :event="$event" class="bg-cream-50 text-ink sm:grid-cols-1" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="overflow-hidden bg-cream-100 py-12 sm:py-16" aria-label="The atmosphere of Fair Week">
            <div class="mx-auto grid max-w-7xl gap-4 px-5 sm:grid-cols-12 sm:px-8">
                <div class="image-zoom sm:col-span-5 sm:translate-y-5"><x-responsive-image :asset="$ceilidhImage" :width="900" :height="760" sizes="(min-width: 640px) 42vw, 100vw" class="aspect-[9/7] h-full w-full object-cover" /></div>
                <div class="image-zoom sm:col-span-4"><x-responsive-image :asset="$wineImage" :width="760" :height="900" sizes="(min-width: 640px) 34vw, 100vw" class="aspect-[4/5] h-full w-full object-cover" /></div>
                <div class="image-zoom sm:col-span-3 sm:translate-y-10"><x-responsive-image :asset="$soulImage" :width="650" :height="760" sizes="(min-width: 640px) 25vw, 100vw" class="aspect-[4/5] h-full w-full object-cover" /></div>
            </div>
        </section>

        <section id="programme" class="mx-auto max-w-7xl scroll-mt-28 px-5 py-16 sm:px-8 sm:py-24" aria-labelledby="programme-heading">
            <x-section-heading id="programme-heading" eyebrow="Day by day" title="The complete 2026 programme" introduction="Every event in chronological order, with cancellation notices and practical information kept visible." />

            <div class="relative mt-12 grid gap-14 before:absolute before:top-2 before:bottom-3 before:left-[1.05rem] before:w-px before:bg-hedge-700/20 sm:before:left-[5.5rem]">
                @foreach($groupedEvents as $day => $dayEvents)
                    @php($date = Carbon::parse($day))
                    <section class="relative grid gap-6 sm:grid-cols-[8.5rem_1fr]" aria-labelledby="programme-{{ $day }}">
                        <div class="relative z-10 flex items-start gap-4 sm:block">
                            <span class="mt-1 block h-9 w-9 shrink-0 rounded-full border-[7px] border-cream-50 bg-barn-600 sm:mx-auto" aria-hidden="true"></span>
                            <h3 id="programme-{{ $day }}" class="font-serif text-2xl leading-tight text-hedge-900 sm:mt-3 sm:text-center"><span class="block text-sm font-sans font-semibold tracking-[0.14em] text-barn-600 uppercase">{{ $date->format('l') }}</span>{{ $date->format('j F') }}</h3>
                        </div>
                        <div class="grid gap-7 lg:grid-cols-2">
                            @foreach($dayEvents as $event)<x-event-card :event="$event" heading-level="4" />@endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </section>

        <section class="border-y border-hedge-700/15 bg-wheat-300/25 py-16 sm:py-20" aria-labelledby="keep-celebrating">
            <div class="mx-auto grid max-w-7xl gap-10 px-5 sm:px-8 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-7">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Keep the week with you</p>
                    <h2 id="keep-celebrating" class="mt-3 font-serif text-4xl tracking-tight text-balance text-hedge-900 sm:text-5xl">Take part, take a photograph, make a memory</h2>
                    <p class="mt-5 max-w-3xl text-lg leading-8 text-hedge-800/80">The best view of Fair Week is your own. Explore the photography competition, plan for Fair Day or ask the committee about volunteering.</p>
                    <div class="mt-7 flex flex-wrap gap-4">
                        @if($competition)<x-button :href="$competition->url()">Photography competition</x-button>@endif
                        <x-button href="/fair-day" variant="secondary">Plan Fair Day</x-button>
                        <x-button href="/volunteer" variant="secondary">Volunteer</x-button>
                    </div>
                </div>
                <div class="image-zoom lg:col-span-4 lg:col-start-9"><x-responsive-image :asset="$nancyImage" :width="760" :height="760" sizes="(min-width: 1024px) 32vw, 100vw" class="aspect-square w-full object-cover shadow-soft" /></div>
            </div>
        </section>
    </main>
</x-layouts.app>
