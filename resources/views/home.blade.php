@php
    use Statamic\Facades\Entry;

    $siteSettings = globalSet('site');
    $currentFair = Entry::query()->where('collection', 'fair_years')->whereStatus('published')->orderBy('date', 'desc')->first();
    $upcomingEvents = Entry::query()
        ->where('collection', 'events')
        ->whereStatus('published')
        ->where('start_at', '>=', now()->startOfDay()->format('Y-m-d H:i'))
        ->orderBy('start_at')
        ->get();
    $featuredEvents = $upcomingEvents->sortByDesc(fn ($event) => (bool) $event->featured)->take(4);
    $latestNews = Entry::query()->where('collection', 'news')->whereStatus('published')->orderBy('date', 'desc')->limit(3)->get();
    $communityImages = collect(\Statamic\View\Blade\value($community_gallery))->filter()->values();
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description" :share-image="$share_image">
    @if($currentFair?->announcement)
        <x-announcement :message="$currentFair->announcement" :href="$currentFair->announcement_link" :label="$currentFair->announcement_link_label" />
    @endif

    <main id="main-content">
        <section class="relative isolate overflow-hidden bg-hedge-900 text-cream-50">
            @if($hero_image)
                <x-responsive-image :asset="$hero_image" :width="1920" :height="1120" sizes="100vw" loading="eager" fetch-priority="high" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover object-center" />
            @endif
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-hedge-900 via-hedge-900/85 to-hedge-900/25" aria-hidden="true"></div>
            <div class="absolute inset-x-0 bottom-0 -z-10 h-44 bg-gradient-to-t from-hedge-900/65 to-transparent" aria-hidden="true"></div>
            <div class="mx-auto grid min-h-[42rem] max-w-7xl items-end px-5 py-16 sm:min-h-[47rem] sm:px-8 sm:py-20 lg:grid-cols-12 lg:py-24">
                <div class="max-w-4xl lg:col-span-8">
                    <p class="text-sm font-semibold tracking-[0.18em] text-wheat-300 uppercase">{{ $hero_kicker }}</p>
                    <h1 class="mt-5 font-serif text-5xl leading-[0.96] tracking-tight text-balance sm:text-7xl lg:text-8xl">{{ $hero_heading }}</h1>
                    <p class="mt-7 max-w-2xl text-lg leading-8 text-pretty text-cream-100 sm:text-xl">{{ $hero_text }}</p>
                    @if($currentFair)
                        <div class="mt-7 flex flex-wrap gap-x-8 gap-y-2 border-l-2 border-wheat-300 pl-5 text-sm font-semibold text-cream-100">
                            @if($currentFair->fair_week_start && $currentFair->fair_week_end)<p>Fair Week: {{ \Illuminate\Support\Carbon::parse($currentFair->fair_week_start)->format('j') }}–{{ \Illuminate\Support\Carbon::parse($currentFair->fair_week_end)->format('j F Y') }}</p>@endif
                            @if($currentFair->fair_day)<p>Fair Day: {{ \Illuminate\Support\Carbon::parse($currentFair->fair_day)->format('j F Y') }}</p>@endif
                            @if($currentFair->theme)<p>Theme: {{ $currentFair->theme }}</p>@endif
                        </div>
                    @endif
                    @if($hero_link && $hero_link_label)<x-button :href="$hero_link" class="mt-9">{{ $hero_link_label }}</x-button>@endif
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-24">
            <div class="grid gap-12 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-5">
                    <x-section-heading eyebrow="Welcome" :heading="$introduction_heading" />
                    <div class="prose mt-7">{!! \Statamic\Statamic::modify($introduction)->markdown() !!}</div>
                </div>
                <x-photo-collage :images="$hero_supporting_images" class="lg:col-span-6 lg:col-start-7" />
            </div>
        </section>

        <section class="border-y border-hedge-700/15 bg-cream-100 py-16 sm:py-24">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                    <x-section-heading eyebrow="The programme" heading="Coming up at the Fair" text="Confirmed events are shown in date order, with booking and access details on each event page." />
                    <a href="/events" class="shrink-0 font-semibold text-barn-700 underline decoration-2 underline-offset-4 hover:text-barn-600">View the full programme</a>
                </div>
                @if($featuredEvents->isNotEmpty())
                    <div class="mt-12 grid gap-x-10 gap-y-9 lg:grid-cols-2">
                        @foreach($featuredEvents as $event)<x-event-card :event="$event" />@endforeach
                    </div>
                @else
                    <x-photo-empty-state class="mt-12" :heading="$archive_heading" :text="$archive_text" :images="$programme_images">
                        <div class="mt-7 flex flex-wrap gap-4">
                            <x-button href="/history" variant="secondary">Explore our history</x-button>
                            <a href="/volunteer" class="inline-flex min-h-12 items-center font-semibold text-barn-700 underline decoration-2 underline-offset-4">Get involved</a>
                        </div>
                    </x-photo-empty-state>
                @endif
            </div>
        </section>

        <section class="overflow-hidden py-16 sm:py-24">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <x-section-heading eyebrow="Annual traditions" heading="Three ways to be part of it" text="From the build-up through Fair Week to Easter Monday’s walk, each tradition has its own character." />

                <article class="mt-12 grid overflow-hidden bg-hedge-50 lg:grid-cols-2 lg:items-center">
                    <x-responsive-image :asset="$fair_week_image" :width="1100" :height="800" sizes="(min-width: 1024px) 50vw, 100vw" alt="" class="aspect-[4/3] h-full w-full object-cover" />
                    <div class="p-8 sm:p-12 lg:p-16">
                        <p class="font-serif text-5xl text-wheat-500" aria-hidden="true">01</p>
                        <h2 class="mt-6 font-serif text-4xl tracking-tight text-balance text-hedge-900 sm:text-5xl">Fair Week</h2>
                        <p class="mt-5 max-w-xl text-lg text-pretty text-hedge-800/80">{{ $fair_week_summary }}</p>
                        <a href="/fair-week" class="mt-7 inline-flex font-semibold text-barn-700 underline decoration-2 underline-offset-4">Explore Fair Week</a>
                    </div>
                </article>
            </div>

            <article class="relative isolate mx-auto mt-8 min-h-[34rem] max-w-[96rem] overflow-hidden bg-hedge-900 text-cream-50 sm:mt-12">
                <x-responsive-image :asset="$fair_day_image" :width="1920" :height="1050" sizes="100vw" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" />
                <div class="absolute inset-0 -z-10 bg-gradient-to-r from-hedge-900/95 via-hedge-900/70 to-transparent" aria-hidden="true"></div>
                <div class="mx-auto flex min-h-[34rem] max-w-7xl items-end px-5 py-12 sm:px-8 sm:py-16">
                    <div class="max-w-xl">
                        <p class="font-serif text-5xl text-wheat-300" aria-hidden="true">02</p>
                        <h2 class="mt-5 font-serif text-5xl tracking-tight text-balance sm:text-6xl">Fair Day</h2>
                        <p class="mt-5 text-lg text-pretty text-cream-100">{{ $fair_day_summary }}</p>
                        <a href="/fair-day" class="mt-7 inline-flex font-semibold text-wheat-300 underline decoration-2 underline-offset-4 hover:text-cream-50">Discover Fair Day</a>
                    </div>
                </div>
            </article>

            <div class="mx-auto mt-8 max-w-7xl px-5 sm:mt-12 sm:px-8">
                <article class="grid gap-8 lg:grid-cols-12 lg:items-center">
                    <div class="bg-cream-100 p-8 sm:p-12 lg:col-span-5 lg:p-14">
                        <p class="font-serif text-5xl text-wheat-500" aria-hidden="true">03</p>
                        <h2 class="mt-6 font-serif text-4xl tracking-tight text-balance text-hedge-900 sm:text-5xl">Scalby Walk</h2>
                        <p class="mt-5 text-lg text-pretty text-hedge-800/80">{{ $walk_summary }}</p>
                        <a href="/scalby-walk" class="mt-7 inline-flex font-semibold text-barn-700 underline decoration-2 underline-offset-4">Plan your walk</a>
                    </div>
                    <div class="relative lg:col-span-7">
                        <div class="absolute -top-4 -right-4 h-24 w-24 border-t-2 border-r-2 border-wheat-500" aria-hidden="true"></div>
                        <x-responsive-image :asset="$walk_image" :width="1200" :height="800" sizes="(min-width: 1024px) 58vw, 100vw" alt="" class="aspect-[3/2] w-full object-cover shadow-soft" />
                    </div>
                </article>
            </div>
        </section>

        <section class="relative isolate overflow-hidden bg-hedge-900 py-18 text-cream-50 sm:py-24">
            @if($impact_background_image)
                <x-responsive-image :asset="$impact_background_image" :width="1800" :height="900" sizes="100vw" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" />
            @endif
            <div class="absolute inset-0 -z-10 bg-hedge-900/88" aria-hidden="true"></div>
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="grid gap-12 lg:grid-cols-12 lg:items-end">
                    <div class="lg:col-span-4">
                        <p class="text-sm font-semibold tracking-[0.16em] text-wheat-300 uppercase">Our shared impact</p>
                        <h2 class="mt-3 font-serif text-4xl tracking-tight text-balance sm:text-5xl">Made by the community, for the community</h2>
                    </div>
                    <div class="grid gap-7 sm:grid-cols-2 lg:col-span-7 lg:col-start-6 lg:grid-cols-3">
                        @foreach($siteSettings?->statistics ?? [] as $statistic)<x-statistic :value="$statistic['value']" :label="$statistic['label']" />@endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-24">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <x-section-heading eyebrow="Latest stories" heading="News from the Fair" />
                <a href="/news" class="font-semibold text-barn-700 underline decoration-2 underline-offset-4">All news</a>
            </div>
            @if($latestNews->isNotEmpty())
                <div class="mt-12 grid gap-10 md:grid-cols-3">@foreach($latestNews as $article)<x-news-card :article="$article" />@endforeach</div>
            @else
                <div class="mt-12 grid overflow-hidden bg-hedge-50 lg:grid-cols-12 lg:items-center">
                    <x-responsive-image :asset="$news_empty_image" :width="1100" :height="760" sizes="(min-width: 1024px) 58vw, 100vw" alt="" class="aspect-[4/3] h-full w-full object-cover lg:col-span-7" />
                    <div class="p-8 sm:p-12 lg:col-span-5">
                        <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Stories are on their way</p>
                        <h2 class="mt-3 font-serif text-3xl tracking-tight text-balance text-hedge-900 sm:text-4xl">News will appear here</h2>
                        <p class="mt-4 text-pretty text-hedge-800/80">The committee can publish announcements, community stories and programme updates from the Statamic control panel.</p>
                        <a href="/newsletter" class="mt-7 inline-flex font-semibold text-barn-700 underline decoration-2 underline-offset-4">Join the newsletter</a>
                    </div>
                </div>
            @endif
        </section>

        @if($communityImages->isNotEmpty())
            <section class="border-y border-hedge-700/15 bg-cream-100 py-16 sm:py-24">
                <div class="mx-auto max-w-7xl px-5 sm:px-8">
                    <div class="grid gap-10 lg:grid-cols-12 lg:items-end">
                        <div class="lg:col-span-5"><x-section-heading eyebrow="Community album" :heading="$community_heading" /></div>
                        <p class="max-w-2xl text-lg text-pretty text-hedge-800/80 lg:col-span-6 lg:col-start-7">{{ $community_text }}</p>
                    </div>
                    <div class="mt-12 grid auto-rows-[11rem] grid-cols-2 gap-3 sm:auto-rows-[15rem] sm:grid-cols-4 sm:gap-4">
                        @foreach($communityImages as $index => $image)
                            <x-responsive-image :asset="$image" :width="$index === 0 ? 1200 : 700" :height="$index === 0 ? 900 : 600" sizes="(min-width: 1024px) 25vw, 50vw" alt="" @class([
                                'h-full w-full object-cover',
                                'col-span-2 row-span-2' => $index === 0,
                                'row-span-2' => $index === 3,
                            ]) />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-24">
            <div class="grid gap-px bg-hedge-700/20 lg:grid-cols-2">
                <div class="bg-cream-100 p-8 sm:p-12">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Lend a hand</p>
                    <h2 class="mt-3 font-serif text-3xl tracking-tight text-balance text-hedge-900">Help make the next Fair happen</h2>
                    <p class="mt-4 text-pretty text-hedge-800/80">There are practical and welcoming roles for people with different amounts of time to give.</p>
                    <x-button href="/volunteer" variant="secondary" class="mt-7">Volunteer with us</x-button>
                </div>
                <div class="bg-cream-50 p-8 sm:p-12">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Keep in touch</p>
                    <h2 class="mt-3 font-serif text-3xl tracking-tight text-balance text-hedge-900">Hear when plans are announced</h2>
                    <p class="mt-4 text-pretty text-hedge-800/80">Sign up for occasional programme news and ways to take part.</p>
                    <x-button href="/newsletter" variant="secondary" class="mt-7">Newsletter</x-button>
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
