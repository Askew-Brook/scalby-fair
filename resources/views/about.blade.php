@php
    use Statamic\Facades\Asset;
    $pageContent = \Statamic\View\Blade\value($content);
    $beneficiaryGroups = collect($page->beneficiary_groups ?? []);
    $volunteerImage = Asset::find('assets::Scalby-Fair_BBQ_2025.webp');
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description ?: $introduction" :share-image="$share_image ?: $featured_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" :image="$featured_image" :supporting-image="$supporting_image" />

        <section class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-20" aria-labelledby="about-heading">
            <x-breadcrumbs :items="[['title' => $title]]" />
            <div class="mt-10 grid gap-12 lg:grid-cols-12 lg:items-start">
                <div class="lg:col-span-7">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">A village tradition, shared by everyone</p>
                    <h2 id="about-heading" class="mt-3 font-serif text-4xl tracking-tight text-balance text-hedge-900 sm:text-5xl">Built by volunteers, for the whole community</h2>
                    @if($pageContent)<div class="prose mt-7">{!! \Statamic\Statamic::modify($pageContent)->markdown() !!}</div>@endif
                </div>
                <nav class="border-t-4 border-wheat-300 bg-cream-100 p-7 lg:col-span-4 lg:col-start-9" aria-label="Explore the story of the Fair">
                    <p class="text-xs font-semibold tracking-[0.16em] text-barn-600 uppercase">Explore more</p>
                    <ul class="mt-4 divide-y divide-hedge-700/15 font-serif text-xl text-hedge-900">
                        <li><a class="flex justify-between gap-4 py-4 hover:text-barn-700" href="/history">Our history <span aria-hidden="true">→</span></a></li>
                        <li><a class="flex justify-between gap-4 py-4 hover:text-barn-700" href="/village-history">A history of Scalby <span aria-hidden="true">→</span></a></li>
                        <li><a class="flex justify-between gap-4 py-4 hover:text-barn-700" href="/scalby-walk#walk-history">Scalby Walk history <span aria-hidden="true">→</span></a></li>
                        <li><a class="flex justify-between gap-4 py-4 hover:text-barn-700" href="/committee">Meet the committee <span aria-hidden="true">→</span></a></li>
                    </ul>
                </nav>
            </div>
        </section>

        <section id="community-impact" class="scroll-mt-28 bg-hedge-900 py-16 text-cream-50 sm:py-20" aria-labelledby="impact-heading">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-end">
                    <div class="lg:col-span-7">
                        <p class="text-sm font-semibold tracking-[0.16em] text-wheat-300 uppercase">Giving back</p>
                        <h2 id="impact-heading" class="mt-3 font-serif text-4xl tracking-tight text-balance sm:text-5xl">Money raised here stays close to home</h2>
                        <p class="mt-5 max-w-3xl text-lg leading-8 text-hedge-100">Schools, sports clubs, churches, youth organisations, community groups, hospices and local services have all benefited from the time and generosity given to the Fair.</p>
                    </div>
                    <div class="border-l-4 border-wheat-300 pl-6 lg:col-span-4 lg:col-start-9">
                        <p class="font-serif text-5xl text-wheat-300 sm:text-6xl">{{ $impact_total ?: 'Tens of thousands' }}</p>
                        <p class="mt-2 text-hedge-100">returned to local good causes over the years</p>
                    </div>
                </div>
                @if($beneficiaryGroups->isNotEmpty())
                    <div class="mt-12 grid gap-px bg-cream-50/20 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($beneficiaryGroups as $group)
                            <article class="bg-hedge-800 p-6"><h3 class="font-serif text-xl text-wheat-300">{{ $group['group'] ?? '' }}</h3><p class="mt-3 text-sm leading-6 text-hedge-100">{{ $group['beneficiaries'] ?? '' }}</p></article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-20">
            <div class="grid gap-4 sm:grid-cols-12">
                <div class="image-zoom sm:col-span-7"><x-responsive-image :asset="$featured_image" :width="1100" :height="720" sizes="(min-width: 640px) 58vw, 100vw" class="aspect-[11/7] h-full w-full object-cover" /></div>
                <div class="image-zoom sm:col-span-5 sm:translate-y-10"><x-responsive-image :asset="$volunteerImage" :width="820" :height="720" sizes="(min-width: 640px) 42vw, 100vw" class="aspect-[8/7] h-full w-full object-cover" /></div>
            </div>
            <x-cta class="mt-16" heading="Help write the next chapter" text="Volunteer your time, share an idea or support the community work made possible by the Fair." href="/volunteer" label="Volunteer with us" />
        </section>
    </main>
</x-layouts.app>
