@php
    $pageContent = \Statamic\View\Blade\value($content);
    $catalogue = app(\App\Support\StallBookingCatalogue::class);
    $bookingItems = $catalogue->items();
    $bookingOpen = $catalogue->bookingsAreOpen() && $bookingItems->contains('available', true);
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description ?: $introduction" :share-image="$share_image ?: $featured_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" :image="$featured_image" :supporting-image="$supporting_image" />

        <section class="py-12 sm:py-20" aria-labelledby="stall-booking-heading">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <x-breadcrumbs :items="[['title' => $title]]" />

                <div class="mt-10 grid gap-12 lg:grid-cols-12 lg:items-start">
                    <div class="lg:col-span-7">
                        <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Fair Day {{ $catalogue->year() }}</p>
                        <h2 id="stall-booking-heading" class="mt-3 max-w-[35ch] font-serif text-4xl font-semibold tracking-tight text-balance text-hedge-900 sm:text-5xl">Please read this before booking</h2>
                        @if($pageContent)<div id="stall-booking-terms" class="prose mt-7 max-w-[75ch]">{!! \Statamic\Statamic::modify($pageContent)->markdown() !!}</div>@endif
                    </div>

                    <aside class="border-t-4 border-wheat-300 bg-cream-100 p-7 lg:sticky lg:top-28 lg:col-span-4 lg:col-start-9" aria-labelledby="booking-process-heading">
                        <h2 id="booking-process-heading" class="font-serif text-2xl font-semibold tracking-tight text-balance text-hedge-900">How online booking works</h2>
                        <ol class="mt-5 grid list-decimal gap-3 pl-5 text-hedge-800/80">
                            <li>Choose the items you need.</li>
                            <li>Complete your stallholder details.</li>
                            <li>Pay securely on Stripe.</li>
                            <li>The stalls organiser receives your paid booking automatically.</li>
                        </ol>
                        <p class="mt-6 border-t border-hedge-900/10 pt-6 text-pretty text-hedge-800/75">Your booking is not complete until Stripe confirms payment.</p>
                    </aside>
                </div>
            </div>
        </section>

        <section id="stall-booking-form" class="scroll-mt-24 border-y border-hedge-900/10 bg-cream-100 py-14 sm:py-20" aria-labelledby="booking-form-heading">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Book and pay online</p>
                <h2 id="booking-form-heading" class="mt-3 max-w-[35ch] font-serif text-4xl font-semibold tracking-tight text-balance text-hedge-900 sm:text-5xl">Scalby Fair stall booking form {{ $catalogue->year() }}</h2>
                <p class="mt-4 max-w-[56ch] text-base text-pretty text-hedge-800/75">Required fields are marked with an asterisk. Prices and availability below are current for this Fair.</p>

                @if(request('payment') === 'cancelled')
                    <div class="mt-8 border-l-4 border-wheat-500 bg-cream-50 p-5 text-hedge-900" role="status">
                        <p class="font-semibold">Payment was cancelled.</p>
                        <p class="mt-1 text-pretty">Nothing has been charged. Your details remain in this browser so you can review them and try again.</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mt-8 border-l-4 border-barn-600 bg-barn-100 p-5 text-barn-700" role="alert">
                        <p class="font-semibold">Please check the highlighted fields and try again.</p>
                        @if($errors->has('booking'))<p class="mt-1">{{ $errors->first('booking') }}</p>@endif
                        @if($errors->has('payment'))<p class="mt-1">{{ $errors->first('payment') }}</p>@endif
                        @if($errors->has('items'))<p class="mt-1">{{ $errors->first('items') }}</p>@endif
                    </div>
                @endif

                @if($bookingOpen)
                    <form action="{{ route('stall-bookings.checkout') }}" method="post" class="mt-10 grid gap-10 lg:grid-cols-12 lg:items-start" data-stall-booking-form>
                        @csrf

                        <div class="grid gap-10 lg:col-span-8">
                            <fieldset class="border-t-4 border-hedge-700 bg-cream-50 p-6 sm:p-9">
                                <legend class="px-2 font-serif text-2xl font-semibold tracking-tight text-hedge-900">1. Choose your items</legend>
                                <div class="grid divide-y divide-hedge-900/10">
                                    @foreach($bookingItems as $item)
                                        <div class="grid gap-4 py-6 first:pt-3 last:pb-1 sm:grid-cols-[1fr_auto] sm:items-center">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                                                    <h3 class="font-semibold text-hedge-900">{{ $item['name'] }}</h3>
                                                    <p class="tabular-nums font-semibold text-barn-700">£{{ number_format($item['unit_amount'] / 100, 2) }}</p>
                                                </div>
                                                @if($item['description'])<p class="mt-1 max-w-[60ch] text-pretty text-hedge-800/70">{{ $item['description'] }}</p>@endif
                                                @error("items.{$item['code']}")<p id="item-{{ $item['code'] }}-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                            </div>
                                            @if($item['available'])
                                                <div class="w-28 shrink-0">
                                                    <label class="field-label" for="item-{{ $item['code'] }}">Quantity</label>
                                                    <input
                                                        class="field-control tabular-nums"
                                                        id="item-{{ $item['code'] }}"
                                                        name="items[{{ $item['code'] }}]"
                                                        type="number"
                                                        value="{{ old("items.{$item['code']}", 0) }}"
                                                        min="0"
                                                        max="{{ $item['max_quantity'] }}"
                                                        inputmode="numeric"
                                                        data-stall-booking-item
                                                        data-unit-amount="{{ $item['unit_amount'] }}"
                                                        @if($errors->has("items.{$item['code']}")) aria-invalid="true" aria-describedby="item-{{ $item['code'] }}-error" @endif
                                                    >
                                                </div>
                                            @else
                                                <p class="w-fit border border-barn-600/25 bg-barn-100 px-3 py-1 font-semibold text-barn-700 sm:justify-self-end">Currently unavailable</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>

                            <fieldset class="border-t-4 border-hedge-700 bg-cream-50 p-6 sm:p-9">
                                <legend class="px-2 font-serif text-2xl font-semibold tracking-tight text-hedge-900">2. Your contact details</legend>
                                <div class="grid gap-6 sm:grid-cols-2">
                                    <div>
                                        <label class="field-label" for="first-name">First name *</label>
                                        <input class="field-control" id="first-name" name="first_name" type="text" value="{{ old('first_name') }}" autocomplete="given-name" required @error('first_name') aria-invalid="true" aria-describedby="first-name-error" @enderror>
                                        @error('first_name')<p id="first-name-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="last-name">Last name *</label>
                                        <input class="field-control" id="last-name" name="last_name" type="text" value="{{ old('last_name') }}" autocomplete="family-name" required @error('last_name') aria-invalid="true" aria-describedby="last-name-error" @enderror>
                                        @error('last_name')<p id="last-name-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="booking-email">Email address *</label>
                                        <input class="field-control" id="booking-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required @error('email') aria-invalid="true" aria-describedby="booking-email-error" @enderror>
                                        @error('email')<p id="booking-email-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="booking-email-confirmation">Confirm email address *</label>
                                        <input class="field-control" id="booking-email-confirmation" name="email_confirmation" type="email" value="{{ old('email_confirmation') }}" autocomplete="email" required @error('email_confirmation') aria-invalid="true" aria-describedby="booking-email-confirmation-error" @enderror>
                                        @error('email_confirmation')<p id="booking-email-confirmation-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="booking-phone">Phone number *</label>
                                        <input class="field-control" id="booking-phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" required @error('phone') aria-invalid="true" aria-describedby="booking-phone-error" @enderror>
                                        @error('phone')<p id="booking-phone-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="business-name">Organiser or business name *</label>
                                        <input class="field-control" id="business-name" name="business_name" type="text" value="{{ old('business_name') }}" autocomplete="organization" required @error('business_name') aria-invalid="true" aria-describedby="business-name-error" @enderror>
                                        @error('business_name')<p id="business-name-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="border-t-4 border-hedge-700 bg-cream-50 p-6 sm:p-9">
                                <legend class="px-2 font-serif text-2xl font-semibold tracking-tight text-hedge-900">3. Address</legend>
                                <div class="grid gap-6 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <label class="field-label" for="address-line-1">Address line 1 *</label>
                                        <input class="field-control" id="address-line-1" name="address_line_1" type="text" value="{{ old('address_line_1') }}" autocomplete="address-line1" required @error('address_line_1') aria-invalid="true" aria-describedby="address-line-1-error" @enderror>
                                        @error('address_line_1')<p id="address-line-1-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="field-label" for="address-line-2">Address line 2 (optional)</label>
                                        <input class="field-control" id="address-line-2" name="address_line_2" type="text" value="{{ old('address_line_2') }}" autocomplete="address-line2" @error('address_line_2') aria-invalid="true" aria-describedby="address-line-2-error" @enderror>
                                        @error('address_line_2')<p id="address-line-2-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="town">Town or city *</label>
                                        <input class="field-control" id="town" name="town" type="text" value="{{ old('town') }}" autocomplete="address-level2" required @error('town') aria-invalid="true" aria-describedby="town-error" @enderror>
                                        @error('town')<p id="town-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="postcode">Postcode *</label>
                                        <input class="field-control" id="postcode" name="postcode" type="text" value="{{ old('postcode') }}" autocomplete="postal-code" required @error('postcode') aria-invalid="true" aria-describedby="postcode-error" @enderror>
                                        @error('postcode')<p id="postcode-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="border-t-4 border-hedge-700 bg-cream-50 p-6 sm:p-9">
                                <legend class="px-2 font-serif text-2xl font-semibold tracking-tight text-hedge-900">4. About your stall</legend>
                                <div class="grid gap-6">
                                    <div>
                                        <label class="field-label" for="stall-purpose">Purpose of stall *</label>
                                        <textarea class="field-control min-h-36 resize-y" id="stall-purpose" name="stall_purpose" required aria-describedby="stall-purpose-hint @error('stall_purpose') stall-purpose-error @enderror" @error('stall_purpose') aria-invalid="true" @enderror>{{ old('stall_purpose') }}</textarea>
                                        <p id="stall-purpose-hint" class="mt-2 text-hedge-800/70">For example, crafts, cakes or information. Please give as much detail as possible.</p>
                                        @error('stall_purpose')<p id="stall-purpose-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="special-requirements">Special requirements *</label>
                                        <textarea class="field-control min-h-36 resize-y" id="special-requirements" name="special_requirements" required aria-describedby="special-requirements-hint @error('special_requirements') special-requirements-error @enderror" @error('special_requirements') aria-invalid="true" @enderror>{{ old('special_requirements') }}</textarea>
                                        <p id="special-requirements-hint" class="mt-2 text-hedge-800/70">Include power, access or location requirements. Enter “None” if there are none.</p>
                                        @error('special_requirements')<p id="special-requirements-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="certificates">Certificates held *</label>
                                        <textarea class="field-control min-h-36 resize-y" id="certificates" name="certificates" required aria-describedby="certificates-hint @error('certificates') certificates-error @enderror" @error('certificates') aria-invalid="true" @enderror>{{ old('certificates') }}</textarea>
                                        <p id="certificates-hint" class="mt-2 text-hedge-800/70">For example, Food Hygiene or Alcohol. Enter “None” if not applicable.</p>
                                        @error('certificates')<p id="certificates-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="border-t-4 border-hedge-700 bg-cream-50 p-6 sm:p-9">
                                <legend class="px-2 font-serif text-2xl font-semibold tracking-tight text-hedge-900">5. Declaration</legend>
                                <div class="grid gap-5">
                                    <label class="grid grid-cols-[auto_1fr] items-start gap-3">
                                        <input class="mt-1 size-5 shrink-0 accent-hedge-700 sm:size-4" name="acceptance" type="checkbox" value="1" @checked(old('acceptance')) required>
                                        <span>I have read and accept the <a class="font-semibold text-barn-700 underline underline-offset-4" href="#stall-booking-terms">stallholder declaration and conditions</a>. *</span>
                                    </label>
                                    @error('acceptance')<p class="font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    <label class="grid grid-cols-[auto_1fr] items-start gap-3">
                                        <input class="mt-1 size-5 shrink-0 accent-hedge-700 sm:size-4" name="privacy_consent" type="checkbox" value="1" @checked(old('privacy_consent')) required>
                                        <span>I agree that my details may be used by the Scalby Fair Committee and Stripe to process this booking. Read the <a class="font-semibold text-barn-700 underline underline-offset-4" href="/privacy">privacy policy</a>. *</span>
                                    </label>
                                    @error('privacy_consent')<p class="font-semibold text-barn-700">{{ $message }}</p>@enderror
                                </div>
                            </fieldset>
                        </div>

                        <aside class="h-fit border-t-4 border-wheat-300 bg-hedge-900 p-7 text-cream-50 lg:sticky lg:top-28 lg:col-span-4" aria-labelledby="order-total-heading">
                            <p class="text-sm font-semibold tracking-[0.16em] text-wheat-300 uppercase">Secure checkout</p>
                            <h2 id="order-total-heading" class="mt-3 font-serif text-3xl font-semibold tracking-tight text-balance">Your total</h2>
                            <p class="mt-5 tabular-nums font-serif text-5xl font-semibold text-wheat-300" data-stall-booking-total>£0.00</p>
                            <p class="mt-5 text-pretty text-cream-100/80">The total is recalculated securely before Stripe takes payment. Card details are entered on Stripe and are not stored by this website.</p>
                            <button class="mt-7 inline-flex min-h-12 w-full items-center justify-center border-2 border-barn-600 bg-barn-600 px-4 py-3 font-semibold text-white hover:-translate-y-0.5 hover:border-barn-700 hover:bg-barn-700 focus-visible:outline-barn-500 focus-visible:outline-3 focus-visible:outline-offset-2" type="submit">Continue to secure payment</button>
                        </aside>

                        <div class="hidden" aria-hidden="true">
                            <label for="booking-website">Leave this field empty</label>
                            <input id="booking-website" name="website" type="text" tabindex="-1" autocomplete="off">
                        </div>
                    </form>
                @else
                    <div class="mt-10 border-l-4 border-wheat-500 bg-cream-50 p-7">
                        <h3 class="font-serif text-2xl font-semibold tracking-tight text-hedge-900">Online stall bookings are currently closed</h3>
                        <p class="mt-3 max-w-[60ch] text-pretty text-hedge-800/75">The committee will reopen this form when the next booking period begins. Contact the Fair committee if you have a stall enquiry.</p>
                        <x-button href="/contact?about=Stall%20bookings" variant="secondary" class="mt-6">Contact the committee</x-button>
                    </div>
                @endif
            </div>
        </section>
    </main>
</x-layouts.app>
