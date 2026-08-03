@php
    $siteSettings = globalSet('site');
    $navigation = collect(\Statamic\Statamic::tag('nav:main')->fetch());
@endphp

<header class="relative z-50 border-b border-hedge-900/10 bg-cream-50/95 backdrop-blur-sm">
    @if($siteSettings?->emergency_notice)
        <x-announcement :message="$siteSettings->emergency_notice" :href="$siteSettings->emergency_notice_link" :label="$siteSettings->emergency_notice_label" />
    @endif

    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 py-4 sm:px-8">
        <a href="/" class="shrink-0 text-hedge-800 hover:text-barn-700" aria-label="Scalby Fair home"><x-site-mark /></a>

        <nav class="hidden lg:block" aria-label="Main navigation">
            <ul class="flex items-center gap-1" role="list">
                @foreach($navigation as $item)
                    @php($children = collect($item['children'] ?? []))
                    <li class="relative group">
                        <a href="{{ $item['url'] }}" class="inline-flex min-h-11 items-center gap-1.5 border-b-2 px-3 py-2 text-sm font-semibold {{ ($item['is_current'] ?? false) || ($item['is_parent'] ?? false) ? 'border-barn-500 text-barn-700' : 'border-transparent text-hedge-900 hover:border-hedge-300 hover:text-barn-700' }}">
                            {{ $item['title'] }}@if($children->isNotEmpty())<svg class="size-3 transition-transform group-hover:rotate-180" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="m2.5 4.5 3.5 3 3.5-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>@endif
                        </a>
                        @if($children->isNotEmpty())
                            <div class="invisible absolute top-full left-0 w-56 translate-y-1 pt-2 opacity-0 transition duration-200 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
                                <ul class="border border-hedge-900/10 bg-cream-50 p-2 shadow-soft" role="list">
                                    @foreach($children as $child)
                                        <li><a href="{{ $child['url'] }}" class="block px-3 py-2 text-sm font-semibold text-hedge-900 hover:bg-hedge-50 hover:text-barn-700">{{ $child['title'] }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>

        <details class="group relative lg:hidden">
            <summary class="inline-flex min-h-12 list-none items-center gap-3 border-2 border-hedge-700 px-4 py-2 font-semibold text-hedge-900 hover:bg-hedge-700 hover:text-cream-50 marker:content-none">
                <span>Menu</span>
                <span class="relative block h-3.5 w-4" aria-hidden="true"><span class="absolute top-0 left-0 h-0.5 w-4 bg-current transition-transform group-open:top-1.5 group-open:rotate-45"></span><span class="absolute top-1.5 left-0 h-0.5 w-4 bg-current transition-opacity group-open:opacity-0"></span><span class="absolute top-3 left-0 h-0.5 w-4 bg-current transition-transform group-open:top-1.5 group-open:-rotate-45"></span></span>
            </summary>
            <nav class="absolute top-[calc(100%+0.75rem)] right-0 w-[min(21rem,calc(100vw-2.5rem))] border border-hedge-900/15 bg-cream-50 p-3 shadow-soft" aria-label="Mobile navigation">
                <ul class="flex flex-col gap-1" role="list">
                    @foreach($navigation as $item)
                        <li>
                            <a href="{{ $item['url'] }}" class="block border-l-2 px-3 py-2.5 font-semibold hover:bg-hedge-50 hover:text-barn-700 {{ ($item['is_current'] ?? false) ? 'border-barn-500 text-barn-700' : 'border-transparent text-hedge-900' }}">{{ $item['title'] }}</a>
                            @if(!empty($item['children']))
                                <ul class="ml-4 border-l border-hedge-300 pl-2" role="list">
                                    @foreach($item['children'] as $child)
                                        <li><a href="{{ $child['url'] }}" class="block px-3 py-2 text-sm font-semibold text-hedge-800 hover:bg-hedge-50 hover:text-barn-700">{{ $child['title'] }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>
        </details>
    </div>
    <x-bunting />
</header>
