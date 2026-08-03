<x-layouts.app :title="$title ?? config('app.name')">
    <main class="grid min-h-screen place-items-center px-6 py-16">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-semibold tracking-[0.2em] text-emerald-700 uppercase">Website foundation</p>
            <h1 class="mt-4 text-5xl font-bold tracking-tight text-balance sm:text-7xl">Scalby Fair</h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-stone-600">
                Statamic, Blade, Livewire and Tailwind CSS are installed and ready for the site build.
            </p>
            <a
                class="mt-10 inline-flex rounded-full bg-emerald-700 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700"
                href="/cp"
            >
                Open the control panel
            </a>
        </div>
    </main>
</x-layouts.app>
