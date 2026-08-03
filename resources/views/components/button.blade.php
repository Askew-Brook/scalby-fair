@props(['href', 'variant' => 'primary', 'external' => false])
@php
    $classes = $variant === 'primary'
        ? 'inline-flex min-h-12 items-center justify-center border-2 border-barn-600 bg-barn-600 px-5 py-3 text-center font-semibold text-white shadow-sm hover:-translate-y-0.5 hover:border-barn-700 hover:bg-barn-700 hover:shadow-md active:translate-y-0 focus-visible:outline-barn-600'
        : 'inline-flex min-h-12 items-center justify-center border-2 border-hedge-700 bg-transparent px-5 py-3 text-center font-semibold text-hedge-800 hover:-translate-y-0.5 hover:bg-hedge-700 hover:text-cream-50 hover:shadow-sm active:translate-y-0 focus-visible:outline-hedge-700';
@endphp
<a href="{{ $href }}" {{ $attributes->class($classes) }} @if($external) target="_blank" rel="noopener" @endif>
    {{ $slot }}@if($external)<span class="sr-only"> (opens in a new tab)</span>@endif
</a>
