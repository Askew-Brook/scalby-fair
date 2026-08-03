<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description" :share-image="$share_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" :image="$featured_image" />
        <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-18">
            <x-breadcrumbs :items="[['title' => $title]]" />
            @if($content)<div class="prose mt-10 max-w-3xl">{!! \Statamic\Statamic::modify($content)->markdown() !!}</div>@endif

            @if($title === 'Contact')
                @php($siteSettings = globalSet('site'))
                <div class="mt-10 grid max-w-3xl gap-px bg-hedge-700/20 sm:grid-cols-2">
                    @if($siteSettings?->contact_email)<a class="bg-cream-100 p-6 font-semibold text-barn-700 underline underline-offset-4" href="mailto:{{ $siteSettings->contact_email }}">{{ $siteSettings->contact_email }}</a>@endif
                    @if($siteSettings?->contact_phone)<a class="bg-cream-100 p-6 font-semibold text-barn-700 underline underline-offset-4" href="tel:{{ preg_replace('/[^+0-9]/', '', $siteSettings->contact_phone) }}">{{ $siteSettings->contact_phone }}</a>@endif
                </div>
            @endif

            @if($callout_heading && $callout_link && $callout_label)
                <x-cta class="mt-14" :heading="$callout_heading" :text="$callout_text" :href="$callout_link" :label="$callout_label" />
            @endif
        </div>
    </main>
</x-layouts.app>
