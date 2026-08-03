@props(['heading', 'text' => null, 'href', 'label'])
<aside {{ $attributes->class(['bg-hedge-800 px-6 py-10 text-cream-50 sm:px-10 sm:py-12']) }}>
    <div class="flex flex-col gap-7 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-2xl"><h2 class="font-serif text-3xl font-semibold tracking-tight text-balance">{{ $heading }}</h2>@if($text)<p class="mt-3 text-pretty text-hedge-100">{{ $text }}</p>@endif</div>
        <x-button :href="$href" class="shrink-0">{{ $label }}</x-button>
    </div>
</aside>
