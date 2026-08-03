@props(['asset'])
<a href="{{ $asset->url() }}" class="flex min-h-12 items-center justify-between gap-5 border-b border-hedge-700/25 py-3 font-semibold text-hedge-800 hover:text-barn-700" download>
    <span>{{ $asset->title ?: $asset->basename() }}</span><span class="text-xs tracking-[0.12em] uppercase">Download</span>
</a>
