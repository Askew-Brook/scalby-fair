@props(['asset', 'width' => 1200, 'height' => 800, 'loading' => 'lazy', 'sizes' => '(min-width: 1024px) 50vw, 100vw', 'fetchPriority' => null, 'alt' => null])
@php($asset = \Statamic\View\Blade\value($asset))
@if($asset)
    <img
        src="{{ glide($asset->path(), ['w' => $width, 'h' => $height, 'q' => 82, 'fm' => 'webp']) }}"
        srcset="{{ glide($asset->path(), ['w' => 640, 'h' => round(640 * $height / $width), 'q' => 80, 'fm' => 'webp']) }} 640w, {{ glide($asset->path(), ['w' => $width, 'h' => $height, 'q' => 82, 'fm' => 'webp']) }} {{ $width }}w"
        sizes="{{ $sizes }}"
        width="{{ $width }}" height="{{ $height }}" alt="{{ $alt ?? ($asset->alt ?? '') }}" loading="{{ $loading }}" @if($fetchPriority) fetchpriority="{{ $fetchPriority }}" @endif decoding="async" {{ $attributes }}
    >
@endif
