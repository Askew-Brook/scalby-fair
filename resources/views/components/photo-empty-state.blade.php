@props(['heading', 'text', 'images'])

<div {{ $attributes->class(['overflow-hidden border-y border-hedge-700/20 bg-cream-50']) }}>
    <div class="grid lg:grid-cols-12 lg:items-stretch">
        <div class="p-7 sm:p-10 lg:col-span-5 lg:p-12">
            <p class="font-serif text-3xl tracking-tight text-balance text-hedge-900 sm:text-4xl">{{ $heading }}</p>
            <p class="mt-4 max-w-xl text-pretty text-hedge-800/80">{{ $text }}</p>
            {{ $slot }}
        </div>
        <x-photo-collage :images="$images" class="min-h-72 bg-hedge-50 p-3 sm:p-4 lg:col-span-7" />
    </div>
</div>
