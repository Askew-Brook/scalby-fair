@props(['eyebrow' => null, 'heading' => null, 'title' => null, 'text' => null, 'introduction' => null, 'align' => 'left', 'theme' => 'light'])
@php
    $displayHeading = $heading ?: $title;
    $displayText = $text ?: $introduction;
@endphp
<div {{ $attributes->class([$align === 'center' ? 'mx-auto max-w-3xl text-center' : 'max-w-3xl']) }}>
    @if($eyebrow)<p @class(['text-sm font-semibold tracking-[0.16em] uppercase', 'text-barn-600' => $theme === 'light', 'text-wheat-300' => $theme === 'dark'])>{{ $eyebrow }}</p>@endif
    <h2 @class(['font-serif text-3xl font-semibold tracking-tight text-balance sm:text-5xl', 'mt-3' => $eyebrow, 'text-hedge-900' => $theme === 'light', 'text-cream-50' => $theme === 'dark'])>{{ $displayHeading }}</h2>
    @if($displayText)<p @class(['mt-5 text-lg leading-8 text-pretty', 'text-hedge-800/80' => $theme === 'light', 'text-hedge-100' => $theme === 'dark'])>{{ $displayText }}</p>@endif
</div>
