<x-layouts.app title="Stall booking payment">
    <main id="main-content">
        <section class="bg-hedge-900 py-20 text-cream-50 sm:py-28">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <p class="text-sm font-semibold tracking-[0.16em] text-wheat-300 uppercase">Stall bookings</p>
                @if($paid)
                    <h1 class="mt-4 max-w-[20ch] font-serif text-5xl font-semibold tracking-tight text-balance sm:text-7xl">Thank you. Your booking is paid.</h1>
                    <p class="mt-6 max-w-[48ch] text-lg text-pretty text-cream-100">Your details have been sent to the stalls organiser. Please keep the Stripe receipt for your records.</p>
                @else
                    <h1 class="mt-4 max-w-[20ch] font-serif text-5xl font-semibold tracking-tight text-balance sm:text-7xl">Your payment is being confirmed.</h1>
                    <p class="mt-6 max-w-[48ch] text-lg text-pretty text-cream-100">Stripe is still confirming the payment. The stalls organiser will receive your booking automatically once it is complete.</p>
                @endif
                <a href="/" class="mt-8 inline-flex min-h-12 items-center justify-center border-2 border-wheat-300 px-4 py-3 font-semibold text-wheat-300 hover:-translate-y-0.5 hover:bg-wheat-300 hover:text-hedge-900">Return to the homepage</a>
            </div>
        </section>
    </main>
</x-layouts.app>
