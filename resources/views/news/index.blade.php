@php
    use Statamic\Facades\Entry;
    $articles = Entry::query()->where('collection', 'news')->whereStatus('published')->orderBy('date', 'desc')->paginate(9)->withQueryString();
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description" :share-image="$share_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" :image="$featured_image" :supporting-image="$supporting_image" />
        <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-18">
            <x-breadcrumbs :items="[['title' => $title]]" />
            @if($articles->isNotEmpty())
                <div class="mt-12 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">@foreach($articles as $article)<x-news-card :article="$article" />@endforeach</div>
                <x-pagination :paginator="$articles" />
            @else
                <div class="mt-12 grid overflow-hidden bg-hedge-50 lg:grid-cols-12 lg:items-center">
                    <x-responsive-image :asset="$featured_image" :width="1100" :height="760" sizes="(min-width: 1024px) 58vw, 100vw" alt="" class="aspect-[4/3] h-full w-full object-cover lg:col-span-7" />
                    <div class="p-8 sm:p-12 lg:col-span-5">
                        <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Stories are gathering</p>
                        <h2 class="mt-3 font-serif text-3xl tracking-tight text-balance text-hedge-900 sm:text-4xl">News from across the Fair is coming soon</h2>
                        <p class="mt-4 text-pretty text-hedge-800/80">Announcements, programme updates and stories from the people who make the Fair will be shared here.</p>
                        <a href="/newsletter" class="mt-7 inline-flex font-semibold text-barn-700 underline decoration-2 underline-offset-4">Join the newsletter</a>
                    </div>
                </div>
            @endif
        </div>
    </main>
</x-layouts.app>
