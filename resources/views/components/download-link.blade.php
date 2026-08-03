@props(['asset'])
<a href="{{ $asset->url() }}" class="group flex min-h-12 items-center justify-between gap-5 border-b border-hedge-700/25 py-3 font-semibold text-hedge-800 hover:border-barn-500 hover:text-barn-700" download>
    <span>{{ $asset->title ?: $asset->basename() }}</span><span class="text-xs tracking-[0.12em] uppercase">Download <span class="inline-block transition-transform group-hover:translate-y-0.5" aria-hidden="true">↓</span></span>
</a>
