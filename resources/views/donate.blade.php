@php($pageContent = \Statamic\View\Blade\value($content))

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description ?: $introduction" :share-image="$share_image ?: $featured_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" :image="$featured_image" :supporting-image="$supporting_image" />

        <section class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-20" aria-labelledby="donation-heading">
            <x-breadcrumbs :items="[['title' => $title]]" />

            <div class="mt-10 grid gap-12 lg:grid-cols-12 lg:items-start">
                <div class="lg:col-span-7">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Support the community</p>
                    <h2 id="donation-heading" class="mt-3 font-serif text-4xl font-semibold tracking-tight text-balance text-hedge-900 sm:text-5xl">Make a secure donation</h2>
                    @if($pageContent)<div class="prose mt-6">{!! \Statamic\Statamic::modify($pageContent)->markdown() !!}</div>@endif

                    <section id="donation-form" class="mt-10 scroll-mt-28 border-t-4 border-wheat-300 bg-cream-100 p-6 sm:p-9" aria-labelledby="donation-form-heading">
                        <h3 id="donation-form-heading" class="font-serif text-3xl font-semibold tracking-tight text-hedge-900">Donation details</h3>
                        <p class="mt-2 text-hedge-800/75">Required fields are marked with an asterisk.</p>

                        @if(request('payment') === 'cancelled')
                            <div class="mt-6 border-l-4 border-wheat-500 bg-cream-50 p-4" role="status"><p class="font-semibold text-hedge-900">Payment was cancelled.</p><p class="mt-1 text-hedge-800/75">Nothing has been charged. You can review the details and try again.</p></div>
                        @endif

                        @if($errors->any())
                            <div class="mt-6 border-l-4 border-barn-600 bg-barn-100 p-4 text-barn-700" role="alert">
                                <p class="font-semibold">We could not continue to payment for the following reason:</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5">@foreach(collect($errors->all())->unique() as $message)<li>{{ $message }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <form action="{{ route('donations.checkout') }}" method="post" class="mt-8 grid gap-6" data-donation-form>
                            @csrf
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div><label class="field-label" for="donation-first-name">First name *</label><input class="field-control" id="donation-first-name" name="first_name" type="text" value="{{ old('first_name') }}" autocomplete="given-name" required @error('first_name') aria-invalid="true" @enderror>@error('first_name')<p class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror</div>
                                <div><label class="field-label" for="donation-last-name">Last name *</label><input class="field-control" id="donation-last-name" name="last_name" type="text" value="{{ old('last_name') }}" autocomplete="family-name" required @error('last_name') aria-invalid="true" @enderror>@error('last_name')<p class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror</div>
                                <div><label class="field-label" for="donation-email">Email address *</label><input class="field-control" id="donation-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required @error('email') aria-invalid="true" @enderror>@error('email')<p class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror</div>
                                <div><label class="field-label" for="donation-email-confirmation">Confirm email address *</label><input class="field-control" id="donation-email-confirmation" name="email_confirmation" type="email" value="{{ old('email_confirmation') }}" autocomplete="email" required @error('email_confirmation') aria-invalid="true" @enderror>@error('email_confirmation')<p class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror</div>
                            </div>

                            <div>
                                <label class="field-label" for="donation-amount">Donation amount *</label>
                                <div class="mt-2 flex flex-wrap gap-3" aria-label="Suggested donation amounts">
                                    @foreach([2, 5, 10, 20] as $preset)<button class="min-h-11 border border-hedge-700 bg-cream-50 px-4 font-semibold text-hedge-900 hover:bg-hedge-50" type="button" data-donation-preset="{{ $preset }}">£{{ $preset }}</button>@endforeach
                                </div>
                                <div class="mt-4 grid max-w-xs grid-cols-[auto_1fr] items-center">
                                    <span class="col-start-1 row-start-1 pl-4 font-semibold text-hedge-800" aria-hidden="true">£</span>
                                    <input class="field-control col-span-full row-start-1 pl-9 tabular-nums" id="donation-amount" name="amount" type="number" value="{{ old('amount') }}" min="2" max="10000" step="0.01" inputmode="decimal" required data-donation-amount @error('amount') aria-invalid="true" @enderror>
                                </div>
                                @error('amount')<p class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                            </div>

                            <label class="flex items-start gap-3 text-sm text-hedge-800"><input class="mt-1 size-5 shrink-0 accent-hedge-700" name="privacy_consent" type="checkbox" value="1" @checked(old('privacy_consent')) required><span>I agree that Scalby Fair and Stripe may use these details to process this donation. Read the <a class="font-semibold text-barn-700 underline underline-offset-4" href="/privacy">privacy notice</a>. *</span></label>
                            @error('privacy_consent')<p class="-mt-4 font-semibold text-barn-700">{{ $message }}</p>@enderror

                            <div class="hidden" aria-hidden="true"><label for="donation-reference">Leave this field empty</label><input id="donation-reference" name="donation_reference" type="text" tabindex="-1" autocomplete="off"></div>
                            <div><button class="inline-flex min-h-12 items-center justify-center border-2 border-barn-600 bg-barn-600 px-6 py-3 font-semibold text-white hover:-translate-y-0.5 hover:border-barn-700 hover:bg-barn-700" type="submit">Continue to secure payment</button></div>
                        </form>
                    </section>
                </div>

                <aside class="border-t-4 border-wheat-300 bg-hedge-900 p-7 text-cream-50 lg:sticky lg:top-28 lg:col-span-4 lg:col-start-9">
                    <p class="text-xs font-semibold tracking-[0.16em] text-wheat-300 uppercase">Secure payment</p>
                    <h2 class="mt-3 font-serif text-3xl font-semibold">Every contribution helps</h2>
                    <p class="mt-5 text-pretty text-cream-100/80">Card details are entered on Stripe and are not stored by this website. Your donation amount is checked securely before payment.</p>
                </aside>
            </div>
        </section>
    </main>
</x-layouts.app>
