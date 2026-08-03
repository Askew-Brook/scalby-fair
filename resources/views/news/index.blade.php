@php
    use Statamic\Facades\Entry;
    $articles = Entry::query()->where('collection', 'news')->whereStatus('published')->orderBy('date', 'desc')->paginate(9)->withQueryString();
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description" :share-image="$share_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" />
        <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-18">
            <x-breadcrumbs :items="[['title' => $title]]" />
            @if($articles->isNotEmpty())
                <div class="mt-12 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">@foreach($articles as $article)<x-news-card :article="$article" />@endforeach</div>
                <x-pagination :paginator="$articles" />
            @else
                <x-empty-state class="mt-12" heading="No news has been published yet" text="Announcements and stories will appear here when they are ready." />
            @endif
        </div>
    </main>
</x-layouts.app>
