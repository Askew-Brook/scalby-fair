@props(['title', 'eyebrow' => null, 'introduction' => null, 'image' => null, 'supportingImage' => null])
@php($hasImage = (bool) \Statamic\View\Blade\value($image))
<header @class([
    'relative isolate overflow-hidden border-b border-hedge-700/15',
    'bg-cream-100' => $hasImage,
    'bg-hedge-900 text-cream-50' => !$hasImage,
])>
    @unless($hasImage)
        <div class="absolute -right-6 -bottom-24 -z-10 select-none font-serif text-[18rem] leading-none text-cream-50/[0.035] sm:text-[26rem]" aria-hidden="true">1977</div>
    @endunless
    <div @class([
        'mx-auto grid max-w-7xl gap-10 px-5 sm:px-8 lg:grid-cols-12 lg:items-center',
        'py-14 sm:py-20 lg:py-24' => !$hasImage,
        'pt-14 pb-16 sm:pt-20 sm:pb-24 lg:pt-20 lg:pb-28' => $hasImage,
    ])>
        <div @class(['lg:col-span-6' => $hasImage, 'max-w-4xl lg:col-span-9' => !$hasImage])>
            @if($eyebrow)<p @class(['text-sm font-semibold tracking-[0.16em] uppercase', 'text-barn-600' => $hasImage, 'text-wheat-300' => !$hasImage])>{{ $eyebrow }}</p>@endif
            <h1 @class([
                'font-serif text-5xl font-semibold leading-[0.98] tracking-tight text-balance sm:text-7xl',
                'mt-4' => $eyebrow,
                'text-hedge-900' => $hasImage,
                'text-cream-50' => !$hasImage,
            ])>{{ $title }}</h1>
            @if($introduction)<p @class(['mt-6 max-w-2xl text-xl leading-8 text-pretty', 'text-hedge-800/85' => $hasImage, 'text-hedge-100' => !$hasImage])>{{ $introduction }}</p>@endif
        </div>
        @if($hasImage)
            <div class="relative lg:col-span-5 lg:col-start-8 lg:pb-5">
                <div class="absolute -top-4 -right-4 h-24 w-24 border-t-2 border-r-2 border-wheat-500" aria-hidden="true"></div>
                <div class="image-zoom shadow-soft">
                    <x-responsive-image :asset="$image" :width="1100" :height="900" sizes="(min-width: 1024px) 42vw, 100vw" class="aspect-[11/9] w-full object-cover" />
                </div>
                @if($supportingImage)
                    <div class="image-zoom absolute -bottom-8 -left-5 w-28 border-[6px] border-cream-100 shadow-soft sm:-left-8 sm:w-40 lg:-left-14 lg:w-44">
                        <x-responsive-image :asset="$supportingImage" :width="520" :height="520" sizes="176px" alt="" class="aspect-square w-full object-cover" />
                    </div>
                @endif
            </div>
        @endif
    </div>
</header>
