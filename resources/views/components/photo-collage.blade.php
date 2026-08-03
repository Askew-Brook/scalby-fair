@props(['images'])
@php($images = collect(\Statamic\View\Blade\value($images))->filter()->take(3)->values())

@if($images->isNotEmpty())
    <div {{ $attributes->class(['grid grid-cols-2 gap-3 sm:gap-4']) }}>
        @foreach($images as $index => $image)
            <x-responsive-image
                :asset="$image"
                :width="$index === 0 ? 900 : 600"
                :height="$index === 0 ? 1100 : 500"
                sizes="(min-width: 1024px) 25vw, 50vw"
                @class([
                    'w-full object-cover shadow-soft',
                    'row-span-2 h-full min-h-72' => $index === 0,
                    'aspect-[4/3]' => $index > 0,
                ])
            />
        @endforeach
    </div>
@endif
