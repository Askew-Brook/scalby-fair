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
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description" :share-image="$share_image">
    @if($currentFair?->announcement)
        <x-announcement :message="$currentFair->announcement" :href="$currentFair->announcement_link" :label="$currentFair->announcement_link_label" />
    @endif

    <main id="main-content">
        <section class="relative overflow-hidden bg-hedge-800 text-cream-50">
            <div class="absolute inset-0 opacity-30" aria-hidden="true" style="background-image: radial-gradient(circle at 20% 20%, #d9b96e 0 1px, transparent 2px); background-size: 34px 34px;"></div>
            <div class="relative mx-auto grid min-h-[38rem] max-w-7xl gap-12 px-5 py-16 sm:px-8 sm:py-24 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-8">
                    <p class="text-sm font-semibold tracking-[0.18em] text-wheat-300 uppercase">{{ $hero_kicker }}</p>
                    <h1 class="mt-5 max-w-4xl font-serif text-5xl font-semibold leading-[0.96] tracking-tight text-balance sm:text-7xl lg:text-8xl">{{ $hero_heading }}</h1>
                    <p class="mt-7 max-w-2xl text-xl leading-8 text-pretty text-hedge-100">{{ $hero_text }}</p>
                    @if($currentFair)
                        <div class="mt-7 flex flex-wrap gap-x-8 gap-y-2 border-l-2 border-wheat-300 pl-5 text-sm font-semibold text-cream-100">
                            @if($currentFair->fair_week_start && $currentFair->fair_week_end)<p>Fair Week: {{ \Illuminate\Support\Carbon::parse($currentFair->fair_week_start)->format('j') }}–{{ \Illuminate\Support\Carbon::parse($currentFair->fair_week_end)->format('j F Y') }}</p>@endif
                            @if($currentFair->fair_day)<p>Fair Day: {{ \Illuminate\Support\Carbon::parse($currentFair->fair_day)->format('j F Y') }}</p>@endif
                            @if($currentFair->theme)<p>Theme: {{ $currentFair->theme }}</p>@endif
                        </div>
                    @endif
                    @if($hero_link && $hero_link_label)<x-button :href="$hero_link" class="mt-9">{{ $hero_link_label }}</x-button>@endif
                </div>
                <div class="hidden lg:col-span-4 lg:block" aria-hidden="true">
                    <div class="relative mx-auto aspect-square max-w-sm rounded-full border border-wheat-300/50">
                        <div class="absolute inset-8 rounded-full border border-cream-100/30"></div>
                        <div class="absolute inset-16 grid place-items-center rounded-full bg-cream-100 text-hedge-800 shadow-soft"><span class="font-serif text-8xl">SF</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-24">
            <div class="grid gap-10 lg:grid-cols-12">
                <div class="lg:col-span-5"><x-section-heading eyebrow="Welcome" :heading="$introduction_heading" /></div>
                <div class="prose lg:col-span-6 lg:col-start-7">{!! \Statamic\Statamic::modify($introduction)->markdown() !!}</div>
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
                    <x-empty-state class="mt-12" :heading="$archive_heading" :text="$archive_text">
                        <div class="mt-6 flex flex-wrap justify-center gap-4">
                            <x-button href="/history" variant="secondary">Explore our history</x-button>
                            <a href="/volunteer" class="inline-flex min-h-12 items-center font-semibold text-barn-700 underline decoration-2 underline-offset-4">Get involved</a>
                        </div>
                    </x-empty-state>
                @endif
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-24">
            <x-section-heading eyebrow="Annual traditions" heading="Three ways to be part of it" text="From the build-up through Fair Week to Easter Monday’s walk, each tradition has its own character." />
            <div class="mt-12 grid gap-px bg-hedge-700/20 lg:grid-cols-3">
                @foreach([
                    ['Fair Week', $fair_week_summary, '/fair-week', '01'],
                    ['Fair Day', $fair_day_summary, '/fair-day', '02'],
                    ['Scalby Walk', $walk_summary, '/scalby-walk', '03'],
                ] as [$panelTitle, $panelText, $panelUrl, $number])
                    <article class="group bg-cream-50 p-7 sm:p-9">
                        <p class="font-serif text-4xl text-wheat-500" aria-hidden="true">{{ $number }}</p>
                        <h3 class="mt-7 font-serif text-3xl font-semibold tracking-tight text-hedge-900"><a href="{{ $panelUrl }}" class="group-hover:text-barn-700">{{ $panelTitle }}</a></h3>
                        <p class="mt-4 text-pretty text-hedge-800/80">{{ $panelText }}</p>
                        <p class="mt-7 font-semibold text-barn-700 underline decoration-2 underline-offset-4">Find out more</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="bg-hedge-800 py-16 text-cream-50 sm:py-20">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-end">
                    <div class="lg:col-span-4">
                        <p class="text-sm font-semibold tracking-[0.16em] text-wheat-300 uppercase">Our shared impact</p>
                        <h2 class="mt-3 font-serif text-4xl font-semibold tracking-tight text-balance sm:text-5xl">Made by the community, for the community</h2>
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
                <x-empty-state class="mt-12" heading="News will appear here" text="The committee can publish announcements and stories from the Statamic control panel." />
            @endif
        </section>

        <section class="mx-auto max-w-7xl px-5 pb-16 sm:px-8 sm:pb-24">
            <div class="grid gap-px bg-hedge-700/20 lg:grid-cols-2">
                <div class="bg-cream-100 p-8 sm:p-12">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Lend a hand</p>
                    <h2 class="mt-3 font-serif text-3xl font-semibold tracking-tight text-hedge-900">Help make the next Fair happen</h2>
                    <p class="mt-4 text-pretty text-hedge-800/80">There are practical and welcoming roles for people with different amounts of time to give.</p>
                    <x-button href="/volunteer" variant="secondary" class="mt-7">Volunteer with us</x-button>
                </div>
                <div class="bg-cream-50 p-8 sm:p-12">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Keep in touch</p>
                    <h2 class="mt-3 font-serif text-3xl font-semibold tracking-tight text-hedge-900">Hear when plans are announced</h2>
                    <p class="mt-4 text-pretty text-hedge-800/80">Sign up for occasional programme news and ways to take part.</p>
                    <x-button href="/newsletter" variant="secondary" class="mt-7">Newsletter</x-button>
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
