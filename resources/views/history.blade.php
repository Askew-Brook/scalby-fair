@php
    use Statamic\Facades\Asset;

    $chapters = collect($page->chapters ?? []);
    $leaders = collect($page->leaders ?? []);
    $fallbackImages = collect([
        Asset::find('assets::SF-2026-Fair-Day-Parade-1-scaled-e1782569315833.webp'),
        Asset::find('assets::SF_Celidh_2025.jpeg'),
        Asset::find('assets::Scalby-Fair_BBQ_2025.webp'),
        Asset::find('assets::images.jpeg'),
        Asset::find('assets::SF_Wine-Tasting-2025.jpeg'),
    ])->filter()->values();
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description ?: $introduction" :share-image="$share_image ?: $featured_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" :image="$featured_image" :supporting-image="$supporting_image" />
        <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-20">
            <x-breadcrumbs :items="[['title' => 'About the Fair', 'url' => '/about'], ['title' => $title]]" />

            <div class="mt-10 grid gap-12 lg:grid-cols-12 lg:items-start">
                <aside class="lg:sticky lg:top-28 lg:col-span-3">
                    @if($author)<div class="border-l-4 border-wheat-300 pl-5"><p class="text-xs font-semibold tracking-[0.14em] text-barn-600 uppercase">Original account</p><p class="mt-2 font-serif text-xl text-hedge-900">{{ $author }}</p>@if($author_note)<p class="mt-2 text-sm text-hedge-800/75">{{ $author_note }}</p>@endif</div>@endif
                    <nav class="mt-8 border-t border-hedge-700/20 pt-5" aria-label="On this page"><p class="text-xs font-semibold tracking-[0.14em] text-hedge-600 uppercase">On this page</p><ol class="mt-3 grid gap-2 text-sm">@foreach($chapters as $chapter)<li><a class="text-hedge-800 underline decoration-hedge-300 underline-offset-4 hover:text-barn-700" href="#{{ \Illuminate\Support\Str::slug($chapter['heading'] ?? '') }}">{{ $chapter['heading'] ?? '' }}</a></li>@endforeach @if($leaders->isNotEmpty())<li><a class="text-hedge-800 underline decoration-hedge-300 underline-offset-4 hover:text-barn-700" href="#presidents-and-chairs">Presidents and Chairs</a></li>@endif</ol></nav>
                </aside>

                <div class="lg:col-span-8 lg:col-start-5">
                    @foreach($chapters as $index => $chapter)
                        @php
                            $chapterImageValue = \Statamic\View\Blade\value($chapter['image'] ?? null);
                            $chapterImage = $chapterImageValue ?: $fallbackImages->get($index % max($fallbackImages->count(), 1));
                        @endphp
                        <article id="{{ \Illuminate\Support\Str::slug($chapter['heading'] ?? '') }}" class="scroll-mt-28 border-t border-hedge-700/20 py-12 first:border-t-0 first:pt-0">
                            <div class="flex items-baseline gap-4"><span class="font-serif text-2xl text-wheat-500" aria-hidden="true">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span><h2 class="font-serif text-3xl tracking-tight text-balance text-hedge-900 sm:text-4xl">{{ $chapter['heading'] ?? '' }}</h2></div>
                            @if($chapterImage && $index % 2 === 0)<div class="image-zoom mt-7"><x-responsive-image :asset="$chapterImage" :width="1100" :height="620" sizes="(min-width: 1024px) 62vw, 100vw" alt="" class="aspect-[16/9] w-full object-cover" /></div>@endif
                            <div class="prose mt-7 max-w-none">{!! \Statamic\Statamic::modify($chapter['content'] ?? '')->markdown() !!}</div>
                            @if($chapterImage && $index % 2 === 1)<div class="image-zoom mt-8 ml-auto max-w-2xl"><x-responsive-image :asset="$chapterImage" :width="900" :height="650" sizes="(min-width: 1024px) 50vw, 100vw" alt="" class="aspect-[9/6] w-full object-cover shadow-soft" /></div>@endif
                        </article>
                    @endforeach

                    @if($leaders->isNotEmpty())
                        <section id="presidents-and-chairs" class="scroll-mt-28 border-t border-hedge-700/20 py-12" aria-labelledby="leaders-heading">
                            <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">The complete record</p><h2 id="leaders-heading" class="mt-3 font-serif text-4xl tracking-tight text-hedge-900">Presidents and Chairs</h2>
                            <p class="mt-4 max-w-3xl text-hedge-800/80">Appointments usually begin in the autumn. No President was appointed from 2013 until the role returned in 2024.</p>
                            <div class="mt-7 overflow-x-auto border border-hedge-700/20">
                                <table class="min-w-[38rem] w-full border-collapse text-left"><caption class="sr-only">Scalby Fair Presidents and Chairs by year</caption><thead class="bg-hedge-900 text-cream-50"><tr><th class="px-4 py-3" scope="col">Year</th><th class="px-4 py-3" scope="col">President</th><th class="px-4 py-3" scope="col">Chair</th></tr></thead><tbody class="divide-y divide-hedge-700/15">@foreach($leaders as $leader)<tr @class(['bg-wheat-300/25' => (int) ($leader['year'] ?? 0) === 2026])><th class="px-4 py-3 font-semibold text-hedge-900" scope="row">{{ $leader['year'] ?? '' }}</th><td class="px-4 py-3">{{ $leader['president'] ?: 'No appointment' }}</td><td class="px-4 py-3">{{ $leader['chair'] ?? '' }}</td></tr>@endforeach</tbody></table>
                            </div>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </main>
</x-layouts.app>
