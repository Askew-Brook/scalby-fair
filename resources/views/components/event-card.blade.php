@props(['event'])
@php
    $startAt = $event->start_at ? \Illuminate\Support\Carbon::parse($event->start_at) : null;
    $endAt = $event->end_at ? \Illuminate\Support\Carbon::parse($event->end_at) : null;
    $status = $event->event_status ?: 'scheduled';
    $types = collect($event->event_types ?? []);
@endphp

<article {{ $attributes->class(['interactive-card group grid h-full overflow-hidden border border-hedge-700/15 bg-cream-50 sm:grid-cols-[12rem_1fr]']) }}>
    @if($event->featured_image)
        <a href="{{ $event->url() }}" tabindex="-1" aria-hidden="true" class="image-zoom min-h-52 overflow-hidden">
            <x-responsive-image :asset="$event->featured_image" :width="700" :height="850" sizes="(min-width: 1024px) 16vw, 100vw" alt="" class="h-full w-full object-cover" />
        </a>
    @else
        <div class="grid min-h-40 place-items-center bg-hedge-700 font-serif text-5xl text-cream-50" aria-hidden="true">SF</div>
    @endif
    <div class="p-6 sm:p-7">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
            @if($startAt)<p class="font-semibold text-barn-700">{{ $startAt->format('D j M, g:ia') }}@if($endAt)–{{ $endAt->format('g:ia') }}@endif</p>@endif
            @if($status !== 'scheduled')<span class="border border-barn-600 bg-barn-100 px-2 py-0.5 text-xs font-semibold tracking-[0.1em] text-barn-700 uppercase">{{ $status }}</span>@endif
        </div>
        @if($types->isNotEmpty())<p class="mt-3 text-xs font-semibold tracking-[0.12em] text-hedge-600 uppercase">{{ $types->pluck('title')->join(' · ') }}</p>@endif
        <h3 class="mt-2 font-serif text-2xl tracking-tight text-balance text-hedge-900 sm:text-3xl"><a href="{{ $event->url() }}" class="group-hover:text-barn-700">{{ $event->title }}<span aria-hidden="true" class="ml-1 inline-block transition-transform group-hover:translate-x-1">→</span></a></h3>
        @if($event->summary)<p class="mt-3 text-pretty text-hedge-800/80">{{ $event->summary }}</p>@endif
        @if($event->location)<p class="mt-4 text-sm font-semibold text-hedge-700">{{ $event->location }}</p>@endif
        <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2">
            <a href="{{ $event->url() }}" class="font-semibold text-barn-700 underline decoration-2 underline-offset-4">Event details</a>
            @if($event->booking_url)<a href="{{ $event->booking_url }}" class="font-semibold text-hedge-700 underline decoration-2 underline-offset-4" rel="noopener">Booking available</a>@endif
        </div>
    </div>
</article>
