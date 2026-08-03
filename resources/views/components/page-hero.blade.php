@props(['title', 'eyebrow' => null, 'introduction' => null, 'image' => null])
<header class="relative overflow-hidden border-b border-hedge-700/15 bg-cream-100">
    <div class="mx-auto grid max-w-7xl gap-8 px-5 py-14 sm:px-8 sm:py-20 lg:grid-cols-12 lg:items-center lg:py-24">
        <div class="lg:col-span-7">
            @if($eyebrow)<p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">{{ $eyebrow }}</p>@endif
            <h1 class="font-serif text-5xl font-semibold leading-[0.98] tracking-tight text-balance text-hedge-900 sm:text-7xl {{ $eyebrow ? 'mt-4' : '' }}">{{ $title }}</h1>
            @if($introduction)<p class="mt-6 max-w-2xl text-xl leading-8 text-pretty text-hedge-800/85">{{ $introduction }}</p>@endif
        </div>
        @if($image)
            <div class="lg:col-span-5"><x-responsive-image :asset="$image" class="aspect-[4/3] w-full object-cover shadow-soft" /></div>
        @else
            <div class="hidden lg:col-span-5 lg:block" aria-hidden="true">
                <div class="relative mx-auto aspect-square max-w-72 rounded-full border border-wheat-500/40">
                    <div class="absolute inset-7 rounded-full border border-barn-500/30"></div>
                    <div class="absolute inset-14 grid place-items-center rounded-full bg-hedge-700 font-serif text-6xl text-cream-50">SF</div>
                </div>
            </div>
        @endif
    </div>
</header>
