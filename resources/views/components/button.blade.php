@props(['href', 'variant' => 'primary', 'external' => false])
@php
    $classes = $variant === 'primary'
        ? 'inline-flex min-h-12 items-center justify-center bg-barn-600 px-5 py-3 text-center font-semibold text-white shadow-sm hover:bg-barn-700 focus-visible:outline-barn-600'
        : 'inline-flex min-h-12 items-center justify-center border-2 border-hedge-700 px-5 py-3 text-center font-semibold text-hedge-800 hover:bg-hedge-50 focus-visible:outline-hedge-700';
@endphp
<a href="{{ $href }}" {{ $attributes->class($classes) }} @if($external) target="_blank" rel="noopener" @endif>
    {{ $slot }}@if($external)<span class="sr-only"> (opens in a new tab)</span>@endif
</a>
