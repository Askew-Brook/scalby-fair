@props(['heading', 'text'])
<div {{ $attributes->class(['border-y border-hedge-700/20 bg-cream-100 px-6 py-10 text-center sm:px-10']) }}>
    <p class="font-serif text-2xl font-semibold tracking-tight text-hedge-900">{{ $heading }}</p>
    <p class="mx-auto mt-3 max-w-2xl text-pretty text-hedge-800/80">{{ $text }}</p>
    {{ $slot }}
</div>
