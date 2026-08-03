@php
    $siteSettings = globalSet('site');
    $navigation = collect(\Statamic\Statamic::tag('nav:main')->fetch());
@endphp

<header class="relative z-50 border-b border-hedge-900/10 bg-cream-50/95">
    @if($siteSettings?->emergency_notice)
        <x-announcement :message="$siteSettings->emergency_notice" :href="$siteSettings->emergency_notice_link" :label="$siteSettings->emergency_notice_label" />
    @endif

    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 py-4 sm:px-8">
        <a href="/" class="shrink-0 text-hedge-800" aria-label="Scalby Fair home"><x-site-mark /></a>

        <nav class="hidden lg:block" aria-label="Main navigation">
            <ul class="flex items-center gap-1" role="list">
                @foreach($navigation as $item)
                    @php($children = collect($item['children'] ?? []))
                    <li class="relative group">
                        <a href="{{ $item['url'] }}" class="inline-flex min-h-11 items-center border-b-2 px-3 py-2 text-sm font-semibold {{ ($item['is_current'] ?? false) || ($item['is_parent'] ?? false) ? 'border-barn-500 text-barn-700' : 'border-transparent text-hedge-900 hover:border-hedge-300 hover:text-barn-700' }}">
                            {{ $item['title'] }}
                        </a>
                        @if($children->isNotEmpty())
                            <div class="invisible absolute top-full left-0 w-56 pt-2 opacity-0 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
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

        <details class="relative lg:hidden">
            <summary class="list-none border-2 border-hedge-700 px-4 py-2 font-semibold text-hedge-900 marker:content-none">Menu</summary>
            <nav class="absolute top-[calc(100%+0.75rem)] right-0 w-[min(21rem,calc(100vw-2.5rem))] border border-hedge-900/15 bg-cream-50 p-3 shadow-soft" aria-label="Mobile navigation">
                <ul class="flex flex-col gap-1" role="list">
                    @foreach($navigation as $item)
                        <li>
                            <a href="{{ $item['url'] }}" class="block border-l-2 px-3 py-2 font-semibold {{ ($item['is_current'] ?? false) ? 'border-barn-500 text-barn-700' : 'border-transparent text-hedge-900' }}">{{ $item['title'] }}</a>
                            @if(!empty($item['children']))
                                <ul class="ml-4 border-l border-hedge-300 pl-2" role="list">
                                    @foreach($item['children'] as $child)
                                        <li><a href="{{ $child['url'] }}" class="block px-3 py-2 text-sm font-semibold text-hedge-800">{{ $child['title'] }}</a></li>
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
