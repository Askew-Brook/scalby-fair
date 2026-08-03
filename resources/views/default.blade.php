<x-layouts.app :title="$title ?? config('app.name')">
    <main class="mx-auto max-w-4xl px-6 py-16">
        <h1 class="text-4xl font-bold tracking-tight text-stone-950">{{ $title }}</h1>

        @if ($content)
            <div class="prose prose-stone mt-8 max-w-none">
                {!! \Statamic\Statamic::modify($content)->markdown() !!}
            </div>
        @endif
    </main>
</x-layouts.app>
