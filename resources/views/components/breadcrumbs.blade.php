@props(['items' => []])
<nav aria-label="Breadcrumb" {{ $attributes }}>
    <ol class="flex flex-wrap items-center gap-2 text-sm text-hedge-800/70" role="list">
        <li><a class="font-semibold underline-offset-4 hover:text-barn-700 hover:underline" href="/">Home</a></li>
        @foreach($items as $item)
            <li aria-hidden="true">/</li>
            <li @if($loop->last) aria-current="page" @endif>
                @if(!$loop->last && isset($item['url']))<a class="font-semibold underline-offset-4 hover:text-barn-700 hover:underline" href="{{ $item['url'] }}">{{ $item['title'] }}</a>@else{{ $item['title'] }}@endif
            </li>
        @endforeach
    </ol>
</nav>
