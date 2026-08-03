@php
    $published = $page->date ? \Illuminate\Support\Carbon::parse($page->date) : null;
    $articleImage = $page->featured_image;
    $galleryItems = collect($page->gallery ?? []);
    $relatedEventItems = collect($page->related_events ?? []);
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $title,
        'description' => $summary,
        'datePublished' => $published?->toIso8601String(),
        'author' => $author_name ? ['@type' => 'Person', 'name' => $author_name] : ['@type' => 'Organization', 'name' => 'Scalby Fair'],
        'mainEntityOfPage' => url()->current(),
        'image' => $articleImage?->url(),
    ];
@endphp

@push('schema')<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>@endpush

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description ?: $summary" :share-image="$share_image ?: $articleImage">
    <main id="main-content">
        <header class="border-b border-hedge-700/15 bg-cream-100">
            <div class="mx-auto max-w-5xl px-5 py-14 sm:px-8 sm:py-20">
                <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">News</p>
                <h1 class="mt-4 max-w-4xl font-serif text-5xl font-semibold leading-[1.02] tracking-tight text-balance text-hedge-900 sm:text-7xl">{{ $title }}</h1>
                <div class="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold text-hedge-700">
                    @if($published)<p>{{ $published->format('j F Y') }}</p>@endif
                    @if($author_name)<p>By {{ $author_name }}</p>@endif
                </div>
            </div>
        </header>
        <div class="mx-auto max-w-5xl px-5 py-12 sm:px-8 sm:py-18">
            <x-breadcrumbs :items="[['title' => 'News', 'url' => '/news'], ['title' => $title]]" />
            @if($articleImage)<x-responsive-image :asset="$articleImage" width="1400" height="800" class="mt-10 aspect-[7/4] w-full object-cover" />@endif
            <article class="prose mt-10 max-w-3xl">{!! \Statamic\Statamic::modify($content)->markdown() !!}</article>

            @if($galleryItems->isNotEmpty())
                <section class="mt-12 grid gap-4 sm:grid-cols-2" aria-label="Article gallery">@foreach($galleryItems as $image)<x-responsive-image :asset="$image" class="aspect-[4/3] w-full object-cover" />@endforeach</section>
            @endif

            @if($relatedEventItems->isNotEmpty())
                <section class="mt-14 border-t border-hedge-700/20 pt-10" aria-labelledby="article-related-events"><h2 id="article-related-events" class="font-serif text-3xl font-semibold tracking-tight text-hedge-900">Related events</h2><div class="mt-8 grid gap-8 lg:grid-cols-2">@foreach($relatedEventItems as $event)<x-event-card :event="$event" />@endforeach</div></section>
            @endif
        </div>
    </main>
</x-layouts.app>
