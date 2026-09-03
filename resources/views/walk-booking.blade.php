@php
    $pageContent = \Statamic\View\Blade\value($content);
    $catalogue = app(\App\Support\ScalbyWalkCatalogue::class);
    $adultAvailable = $catalogue->adultBookingsAreAvailable();
    $juniorAvailable = $catalogue->juniorBookingsAreAvailable();
    $bookingOpen = $catalogue->registrationsAreOpen() && ($adultAvailable || $juniorAvailable);
    $adultWalkers = old('adult_walkers');
    $juniorWalkers = old('junior_walkers');

    if ($adultWalkers === null && $juniorWalkers === null) {
        $adultWalkers = $adultAvailable ? [['name' => '', 'age' => '', 'gender' => '', 'postcode' => '']] : [];
        $juniorWalkers = [];
    }

    $adultWalkers ??= [];
    $juniorWalkers ??= [];
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description ?: $introduction" :share-image="$share_image ?: $featured_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" :image="$featured_image" :supporting-image="$supporting_image" />

        <section id="walk-registration-information" class="scroll-mt-24 py-12 sm:py-20" aria-labelledby="walk-booking-heading">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <x-breadcrumbs :items="[['title' => $title]]" />

                <div class="mt-10 grid gap-12 lg:grid-cols-12 lg:items-start">
                    <div class="lg:col-span-7">
                        <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Easter Monday {{ $catalogue->year() }}</p>
                        <h2 id="walk-booking-heading" class="mt-3 max-w-[35ch] font-serif text-4xl font-semibold tracking-tight text-balance text-hedge-900 sm:text-5xl">Before you register</h2>
                        @if($pageContent)<div class="prose mt-7 max-w-[75ch]">{!! \Statamic\Statamic::modify($pageContent)->markdown() !!}</div>@endif
                    </div>

                    <aside class="border-t-4 border-wheat-300 bg-cream-100 p-7 lg:sticky lg:top-28 lg:col-span-4 lg:col-start-9" aria-labelledby="walk-process-heading">
                        <h2 id="walk-process-heading" class="font-serif text-2xl font-semibold tracking-tight text-balance text-hedge-900">How registration works</h2>
                        <ol class="mt-5 grid list-decimal gap-3 pl-5 text-hedge-800/80">
                            <li>Add every adult and junior walker.</li>
                            <li>Check the registrant’s contact details.</li>
                            <li>Pay securely on Stripe.</li>
                            <li>The registrant and Walk organisers receive the paid registration.</li>
                        </ol>
                        <p class="mt-6 border-t border-hedge-900/10 pt-6 text-pretty text-hedge-800/75">Your place is not confirmed until Stripe confirms payment.</p>
                    </aside>
                </div>
            </div>
        </section>

        <section id="walk-booking-form" class="scroll-mt-24 border-y border-hedge-900/10 bg-cream-100 py-14 sm:py-20" aria-labelledby="walk-form-heading">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Register and pay online</p>
                <h2 id="walk-form-heading" class="mt-3 max-w-[35ch] font-serif text-4xl font-semibold tracking-tight text-balance text-hedge-900 sm:text-5xl">Scalby Charity Walk {{ $catalogue->year() }} registration</h2>
                <p class="mt-4 max-w-[56ch] text-base text-pretty text-hedge-800/75">Required fields are marked with an asterisk. You can register several walkers in one payment.</p>

                @if(request('payment') === 'cancelled')
                    <div class="mt-8 border-l-4 border-wheat-500 bg-cream-50 p-5 text-hedge-900" role="status">
                        <p class="font-semibold">Payment was cancelled.</p>
                        <p class="mt-1 text-pretty">Nothing has been charged. Your details remain in this browser so you can review them and try again.</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mt-8 border-l-4 border-barn-600 bg-barn-100 p-5 text-barn-700" role="alert">
                        <p class="font-semibold">Please check the highlighted fields and try again.</p>
                        @foreach(['booking', 'payment', 'walkers'] as $errorKey)
                            @if($errors->has($errorKey))<p class="mt-1">{{ $errors->first($errorKey) }}</p>@endif
                        @endforeach
                    </div>
                @endif

                @if($bookingOpen)
                    <form action="{{ route('walk-bookings.checkout') }}" method="post" class="mt-10 grid gap-10 lg:grid-cols-12 lg:items-start" data-walk-booking-form data-adult-price="{{ $catalogue->adultPrice() }}" data-junior-price="{{ $catalogue->juniorPrice() }}">
                        @csrf

                        <div class="grid gap-10 lg:col-span-8">
                            <fieldset class="border-t-4 border-hedge-700 bg-cream-50 p-6 sm:p-9">
                                <legend class="px-2 font-serif text-2xl font-semibold tracking-tight text-hedge-900">1. Registrant details</legend>
                                <p class="mb-6 text-pretty text-hedge-800/70">This person will receive the booking confirmation and can register on behalf of the whole group.</p>
                                <div class="grid gap-6 sm:grid-cols-2">
                                    <div>
                                        <label class="field-label" for="walk-first-name">First name *</label>
                                        <input class="field-control" id="walk-first-name" name="first_name" type="text" value="{{ old('first_name') }}" autocomplete="given-name" required @error('first_name') aria-invalid="true" aria-describedby="walk-first-name-error" @enderror>
                                        @error('first_name')<p id="walk-first-name-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="walk-last-name">Last name *</label>
                                        <input class="field-control" id="walk-last-name" name="last_name" type="text" value="{{ old('last_name') }}" autocomplete="family-name" required @error('last_name') aria-invalid="true" aria-describedby="walk-last-name-error" @enderror>
                                        @error('last_name')<p id="walk-last-name-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="walk-email">Email address *</label>
                                        <input class="field-control" id="walk-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required @error('email') aria-invalid="true" aria-describedby="walk-email-error" @enderror>
                                        @error('email')<p id="walk-email-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="walk-email-confirmation">Confirm email address *</label>
                                        <input class="field-control" id="walk-email-confirmation" name="email_confirmation" type="email" value="{{ old('email_confirmation') }}" autocomplete="email" required @error('email_confirmation') aria-invalid="true" aria-describedby="walk-email-confirmation-error" @enderror>
                                        @error('email_confirmation')<p id="walk-email-confirmation-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="field-label" for="walk-phone">Telephone number *</label>
                                        <input class="field-control" id="walk-phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" required @error('phone') aria-invalid="true" aria-describedby="walk-phone-error" @enderror>
                                        @error('phone')<p id="walk-phone-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="border-t-4 border-hedge-700 bg-cream-50 p-6 sm:p-9">
                                <legend class="px-2 font-serif text-2xl font-semibold tracking-tight text-hedge-900">2. Registrant address</legend>
                                <div class="grid gap-6 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <label class="field-label" for="walk-address-line-1">Address line 1 *</label>
                                        <input class="field-control" id="walk-address-line-1" name="address_line_1" type="text" value="{{ old('address_line_1') }}" autocomplete="address-line1" required @error('address_line_1') aria-invalid="true" aria-describedby="walk-address-line-1-error" @enderror>
                                        @error('address_line_1')<p id="walk-address-line-1-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="field-label" for="walk-address-line-2">Address line 2 (optional)</label>
                                        <input class="field-control" id="walk-address-line-2" name="address_line_2" type="text" value="{{ old('address_line_2') }}" autocomplete="address-line2" @error('address_line_2') aria-invalid="true" aria-describedby="walk-address-line-2-error" @enderror>
                                        @error('address_line_2')<p id="walk-address-line-2-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="walk-town">Town or city *</label>
                                        <input class="field-control" id="walk-town" name="town" type="text" value="{{ old('town') }}" autocomplete="address-level2" required @error('town') aria-invalid="true" aria-describedby="walk-town-error" @enderror>
                                        @error('town')<p id="walk-town-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="field-label" for="walk-postcode">Postcode *</label>
                                        <input class="field-control" id="walk-postcode" name="postcode" type="text" value="{{ old('postcode') }}" autocomplete="postal-code" required @error('postcode') aria-invalid="true" aria-describedby="walk-postcode-error" @enderror>
                                        @error('postcode')<p id="walk-postcode-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="field-label" for="walk-country">Country *</label>
                                        <input class="field-control" id="walk-country" name="country" type="text" value="{{ old('country', 'United Kingdom') }}" autocomplete="country-name" required @error('country') aria-invalid="true" aria-describedby="walk-country-error" @enderror>
                                        @error('country')<p id="walk-country-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="border-t-4 border-hedge-700 bg-cream-50 p-6 sm:p-9">
                                <legend class="px-2 font-serif text-2xl font-semibold tracking-tight text-hedge-900">3. Adult walkers</legend>
                                <div class="flex flex-wrap items-baseline justify-between gap-3">
                                    <p class="text-pretty text-hedge-800/70">Add every adult included in this payment.</p>
                                    <p class="tabular-nums font-semibold text-barn-700">£{{ number_format($catalogue->adultPrice() / 100, 2) }} each</p>
                                </div>
                                @if($adultAvailable)
                                    <div class="mt-5" data-walker-list="adult">
                                        @foreach($adultWalkers as $index => $walker)
                                            <x-walker-fields group="adult_walkers" :index="$index" :walker="$walker" category="Adult" />
                                        @endforeach
                                    </div>
                                    <button class="mt-6 border border-hedge-700 px-3 py-2 text-base font-semibold text-hedge-800 hover:bg-hedge-50 sm:text-sm" type="button" data-add-walker="adult">Add another adult</button>
                                @else
                                    <p class="mt-5 border-l-4 border-barn-600 bg-barn-100 p-4 font-semibold text-barn-700">Adult bookings are currently unavailable.</p>
                                @endif
                            </fieldset>

                            <fieldset class="border-t-4 border-hedge-700 bg-cream-50 p-6 sm:p-9">
                                <legend class="px-2 font-serif text-2xl font-semibold tracking-tight text-hedge-900">4. Junior walkers</legend>
                                <div class="flex flex-wrap items-baseline justify-between gap-3">
                                    <p class="text-pretty text-hedge-800/70">Add every junior included in this payment.</p>
                                    <p class="tabular-nums font-semibold text-barn-700">£{{ number_format($catalogue->juniorPrice() / 100, 2) }} each</p>
                                </div>
                                @if($juniorAvailable)
                                    <div class="mt-5" data-walker-list="junior">
                                        @foreach($juniorWalkers as $index => $walker)
                                            <x-walker-fields group="junior_walkers" :index="$index" :walker="$walker" category="Junior" />
                                        @endforeach
                                    </div>
                                    <button class="mt-6 border border-hedge-700 px-3 py-2 text-base font-semibold text-hedge-800 hover:bg-hedge-50 sm:text-sm" type="button" data-add-walker="junior">Add another junior</button>
                                @else
                                    <p class="mt-5 border-l-4 border-barn-600 bg-barn-100 p-4 font-semibold text-barn-700">Junior bookings are currently unavailable.</p>
                                @endif
                                @error('walkers')<p class="mt-5 font-semibold text-barn-700">{{ $message }}</p>@enderror
                            </fieldset>

                            @if($catalogue->donationsAreEnabled())
                                <fieldset class="border-t-4 border-hedge-700 bg-cream-50 p-6 sm:p-9">
                                    <legend class="px-2 font-serif text-2xl font-semibold tracking-tight text-hedge-900">5. Optional charity donation</legend>
                                    <p class="mb-5 max-w-[60ch] text-pretty text-hedge-800/70">Add a donation to the Scalby Walk charity. Leave this blank if you do not wish to donate.</p>
                                    <div class="max-w-xs">
                                        <label class="field-label" for="walk-donation">Donation amount</label>
                                        <div class="grid grid-cols-[auto_1fr] items-center">
                                            <span class="col-start-1 row-start-1 pl-4 font-semibold text-hedge-800" aria-hidden="true">£</span>
                                            <input class="field-control col-span-full row-start-1 pl-9 tabular-nums" id="walk-donation" name="donation" type="number" value="{{ old('donation') }}" min="0" max="10000" step="0.01" inputmode="decimal" data-walk-donation @error('donation') aria-invalid="true" aria-describedby="walk-donation-error" @enderror>
                                        </div>
                                        @error('donation')<p id="walk-donation-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    </div>
                                </fieldset>
                            @endif

                            <fieldset class="border-t-4 border-hedge-700 bg-cream-50 p-6 sm:p-9">
                                <legend class="px-2 font-serif text-2xl font-semibold tracking-tight text-hedge-900">6. Confirmations</legend>
                                <div class="grid gap-5">
                                    <label class="grid grid-cols-[auto_1fr] items-start gap-3">
                                        <input class="mt-1 size-5 shrink-0 accent-hedge-700 sm:size-4" name="walker_details_confirmation" type="checkbox" value="1" @checked(old('walker_details_confirmation')) required>
                                        <span>I confirm that I have entered the full name, age, gender and postcode for every walker included above. *</span>
                                    </label>
                                    @error('walker_details_confirmation')<p class="font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    <label class="grid grid-cols-[auto_1fr] items-start gap-3">
                                        <input class="mt-1 size-5 shrink-0 accent-hedge-700 sm:size-4" name="rules_confirmation" type="checkbox" value="1" @checked(old('rules_confirmation')) required>
                                        <span>I confirm that all walkers are aware of the <a class="font-semibold text-barn-700 underline underline-offset-4" href="#walk-registration-information">Scalby Walk rules and event information</a> and will follow them. *</span>
                                    </label>
                                    @error('rules_confirmation')<p class="font-semibold text-barn-700">{{ $message }}</p>@enderror
                                    <label class="grid grid-cols-[auto_1fr] items-start gap-3">
                                        <input class="mt-1 size-5 shrink-0 accent-hedge-700 sm:size-4" name="privacy_consent" type="checkbox" value="1" @checked(old('privacy_consent')) required>
                                        <span>I agree that these details may be used by the Scalby Fair Committee and Stripe to process this registration. Read the <a class="font-semibold text-barn-700 underline underline-offset-4" href="/privacy">privacy policy</a>. *</span>
                                    </label>
                                    @error('privacy_consent')<p class="font-semibold text-barn-700">{{ $message }}</p>@enderror
                                </div>
                            </fieldset>
                        </div>

                        <aside class="h-fit border-t-4 border-wheat-300 bg-hedge-900 p-7 text-cream-50 lg:sticky lg:top-28 lg:col-span-4" aria-labelledby="walk-total-heading">
                            <p class="text-sm font-semibold tracking-[0.16em] text-wheat-300 uppercase">Secure checkout</p>
                            <h2 id="walk-total-heading" class="mt-3 font-serif text-3xl font-semibold tracking-tight text-balance">Your total</h2>
                            <p class="mt-5 tabular-nums font-serif text-5xl font-semibold text-wheat-300" data-walk-booking-total>£0.00</p>
                            <dl class="mt-6 grid gap-2 text-cream-100/80">
                                <div class="flex justify-between gap-4"><dt>Adults</dt><dd class="tabular-nums" data-walker-count="adult">0</dd></div>
                                <div class="flex justify-between gap-4"><dt>Juniors</dt><dd class="tabular-nums" data-walker-count="junior">0</dd></div>
                            </dl>
                            <p class="mt-5 border-t border-cream-50/15 pt-5 text-pretty text-cream-100/80">The total is recalculated securely before Stripe takes payment. Card details are entered on Stripe and are not stored by this website.</p>
                            <button class="mt-7 inline-flex min-h-12 w-full items-center justify-center border-2 border-barn-600 bg-barn-600 px-4 py-3 font-semibold text-white hover:-translate-y-0.5 hover:border-barn-700 hover:bg-barn-700 focus-visible:outline-barn-500 focus-visible:outline-3 focus-visible:outline-offset-2" type="submit">Continue to secure payment</button>
                        </aside>

                        <div class="hidden" aria-hidden="true">
                            <label for="walk-booking-website">Leave this field empty</label>
                            <input id="walk-booking-website" name="website" type="text" tabindex="-1" autocomplete="off">
                        </div>

                        <template data-walker-template="adult">
                            <x-walker-fields group="adult_walkers" index="__INDEX__" :walker="[]" category="Adult" />
                        </template>
                        <template data-walker-template="junior">
                            <x-walker-fields group="junior_walkers" index="__INDEX__" :walker="[]" category="Junior" />
                        </template>
                    </form>
                @else
                    <div class="mt-10 border-l-4 border-wheat-500 bg-cream-50 p-7">
                        <h3 class="font-serif text-2xl font-semibold tracking-tight text-hedge-900">Online Walk registrations are currently closed</h3>
                        <p class="mt-3 max-w-[60ch] text-pretty text-hedge-800/75">The committee will reopen this form when the next registration period begins. Contact the Walk organisers if you have a question.</p>
                        <x-button href="/contact?about=Scalby%20Walk" variant="secondary" class="mt-6">Contact the committee</x-button>
                    </div>
                @endif
            </div>
        </section>
    </main>
</x-layouts.app>
