@props(['message', 'href' => null, 'label' => null])
<aside {{ $attributes->class(['border-y border-wheat-500/40 bg-wheat-300/30']) }} aria-label="Important notice">
    <div class="mx-auto flex max-w-7xl flex-col gap-2 px-5 py-3 text-sm font-semibold text-hedge-900 sm:flex-row sm:items-center sm:justify-center sm:px-8">
        <span class="inline-flex w-fit border border-hedge-800 px-2 py-0.5 text-xs tracking-[0.12em] uppercase">Notice</span>
        <p class="text-pretty">{{ $message }}</p>
        @if($href && $label)<a href="{{ $href }}" class="w-fit underline decoration-2 underline-offset-4 hover:text-barn-700">{{ $label }}</a>@endif
    </div>
</aside>
