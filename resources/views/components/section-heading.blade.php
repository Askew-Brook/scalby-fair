@props(['eyebrow' => null, 'heading', 'text' => null, 'align' => 'left'])
<div {{ $attributes->class([$align === 'center' ? 'mx-auto max-w-3xl text-center' : 'max-w-3xl']) }}>
    @if($eyebrow)<p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">{{ $eyebrow }}</p>@endif
    <h2 class="font-serif text-3xl font-semibold tracking-tight text-balance text-hedge-900 sm:text-5xl {{ $eyebrow ? 'mt-3' : '' }}">{{ $heading }}</h2>
    @if($text)<p class="mt-5 text-lg leading-8 text-pretty text-hedge-800/80">{{ $text }}</p>@endif
</div>
