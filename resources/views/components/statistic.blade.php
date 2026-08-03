@props(['value', 'label'])
<div {{ $attributes->class(['border-l-2 border-wheat-300 pl-5']) }}>
    <p class="font-serif text-3xl font-semibold tracking-tight text-cream-50">{{ $value }}</p>
    <p class="mt-2 text-sm leading-6 text-hedge-100">{{ $label }}</p>
</div>
