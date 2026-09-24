<x-layouts.app title="Scalby Walk registration payment">
    <main id="main-content">
        <section class="bg-hedge-900 py-20 text-cream-50 sm:py-28">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <p class="text-sm font-semibold tracking-[0.16em] text-wheat-300 uppercase">Scalby Charity Walk</p>
                @if($paid)
                    <h1 class="mt-4 max-w-[20ch] font-serif text-5xl font-semibold tracking-tight text-balance sm:text-7xl">Thank you. Your registration is paid.</h1>
                    <p class="mt-6 max-w-[48ch] text-lg text-pretty text-cream-100">A full confirmation has been emailed to the registrant and copied to the Walk organisers. Please keep the Stripe receipt for your records.</p>
                @else
                    <h1 class="mt-4 max-w-[20ch] font-serif text-5xl font-semibold tracking-tight text-balance sm:text-7xl">Your payment is being confirmed.</h1>
                    <p class="mt-6 max-w-[48ch] text-lg text-pretty text-cream-100">Stripe is still confirming the payment. The confirmation will be sent automatically once payment is complete.</p>
                @endif
                <a href="/scalby-walk" class="mt-8 inline-flex min-h-12 items-center justify-center border-2 border-wheat-300 px-4 py-3 font-semibold text-wheat-300 hover:-translate-y-0.5 hover:bg-wheat-300 hover:text-hedge-900">Return to Scalby Walk</a>
            </div>
        </section>
    </main>
</x-layouts.app>
