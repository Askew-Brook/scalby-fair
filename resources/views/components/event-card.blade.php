@props(['event'])
@php
    $startAt = $event->start_at ? \Illuminate\Support\Carbon::parse($event->start_at) : null;
    $endAt = $event->end_at ? \Illuminate\Support\Carbon::parse($event->end_at) : null;
    $status = $event->event_status ?: 'scheduled';
    $types = collect($event->event_types ?? []);
@endphp

<article {{ $attributes->class(['group grid h-full border-b border-hedge-700/20 pb-7 sm:grid-cols-[8rem_1fr] sm:gap-6']) }}>
    <div class="mb-4 border-l-2 border-wheat-300 pl-4 sm:mb-0">
        @if($startAt)
            <p class="font-serif text-4xl font-semibold leading-none text-hedge-900">{{ $startAt->format('j') }}</p>
            <p class="mt-1 text-sm font-semibold tracking-[0.12em] text-barn-600 uppercase">{{ $startAt->format('M Y') }}</p>
            <p class="mt-2 text-sm text-hedge-800">{{ $startAt->format('g:ia') }}@if($endAt)–{{ $endAt->format('g:ia') }}@endif</p>
        @endif
    </div>
    <div>
        <div class="flex flex-wrap items-center gap-2">
            @foreach($types as $type)<span class="text-xs font-semibold tracking-[0.12em] text-hedge-600 uppercase">{{ $type->title }}</span>@endforeach
            @if($status !== 'scheduled')<span class="border border-barn-600 bg-barn-100 px-2 py-0.5 text-xs font-semibold tracking-[0.1em] text-barn-700 uppercase">{{ $status }}</span>@endif
        </div>
        <h3 class="mt-2 font-serif text-2xl font-semibold tracking-tight text-balance text-hedge-900 sm:text-3xl"><a href="{{ $event->url() }}" class="group-hover:text-barn-700">{{ $event->title }}</a></h3>
        @if($event->summary)<p class="mt-3 text-pretty text-hedge-800/80">{{ $event->summary }}</p>@endif
        @if($event->location)<p class="mt-4 text-sm font-semibold text-hedge-700">{{ $event->location }}</p>@endif
    </div>
</article>
