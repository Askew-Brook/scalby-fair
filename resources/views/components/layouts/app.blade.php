@props(['title' => config('app.name')])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title }}</title>

        @livewireStyles
        @vite(['resources/css/site.css', 'resources/js/site.js'])
    </head>
    <body class="min-h-screen bg-stone-50 font-sans text-stone-900 antialiased">
        {{ $slot }}

        @livewireScripts
        @stack('scripts')
    </body>
</html>
