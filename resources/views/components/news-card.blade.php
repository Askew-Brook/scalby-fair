@props(['article'])
@php($published = $article->date ? \Illuminate\Support\Carbon::parse($article->date) : null)
<article {{ $attributes->class(['interactive-card group border border-transparent p-3 -m-3']) }}>
    @if($article->featured_image)
        <a href="{{ $article->url() }}" tabindex="-1" aria-hidden="true" class="image-zoom block"><x-responsive-image :asset="$article->featured_image" class="aspect-[4/3] w-full object-cover" /></a>
    @else
        <div class="grid aspect-[4/3] place-items-center bg-hedge-50 font-serif text-5xl text-hedge-700" aria-hidden="true">SF</div>
    @endif
    <div class="pt-5">
        @if($published)<p class="text-sm font-semibold tracking-[0.1em] text-barn-600 uppercase">{{ $published->format('j F Y') }}</p>@endif
        <h3 class="mt-2 font-serif text-2xl font-semibold tracking-tight text-balance text-hedge-900"><a href="{{ $article->url() }}" class="group-hover:text-barn-700">{{ $article->title }}<span aria-hidden="true" class="ml-1 inline-block transition-transform group-hover:translate-x-1">→</span></a></h3>
        @if($article->summary)<p class="mt-3 text-pretty text-hedge-800/80">{{ $article->summary }}</p>@endif
    </div>
</article>
