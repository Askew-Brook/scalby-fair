@php
    use Illuminate\Support\MessageBag;

    $pageContent = \Statamic\View\Blade\value($content);
    $siteSettings = globalSet('site');
    $formErrorBag = session('errors')?->getBag('form.contact') ?? new MessageBag;
    $relatedDefault = request('event') ?: request('about');
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description ?: $introduction" :share-image="$share_image ?: $featured_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" :image="$featured_image" :supporting-image="$supporting_image" />
        <section class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-20" aria-labelledby="contact-heading">
            <x-breadcrumbs :items="[['title' => $title]]" />
            <div class="mt-10 grid gap-12 lg:grid-cols-12 lg:items-start">
                <div class="lg:col-span-7">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">A real person will read this</p>
                    <h2 id="contact-heading" class="mt-3 font-serif text-4xl tracking-tight text-balance text-hedge-900 sm:text-5xl">Contact the Fair committee</h2>
                    @if($pageContent)<div class="prose mt-6">{!! \Statamic\Statamic::modify($pageContent)->markdown() !!}</div>@endif

                    <section id="contact-form" class="mt-10 scroll-mt-28 border-t-4 border-wheat-300 bg-cream-100 p-6 sm:p-9" aria-labelledby="form-heading">
                        <h3 id="form-heading" class="font-serif text-3xl tracking-tight text-hedge-900">Send us a message</h3>
                        <p class="mt-2 text-hedge-800/75">Required fields are marked with an asterisk.</p>

                        @if(request('sent') || session('success'))<div class="mt-6 border-l-4 border-hedge-700 bg-hedge-50 p-4 font-semibold text-hedge-900" role="status">Thank you. Your message has been sent to the committee.</div>@endif
                        @if($formErrorBag->any())<div class="mt-6 border-l-4 border-barn-600 bg-barn-100 p-4 text-barn-700" role="alert"><p class="font-semibold">Please check the highlighted fields and try again.</p></div>@endif

                        <s:form:contact id="committee-contact" class="mt-8 grid gap-6" redirect="/contact?sent=1#contact-form">
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div><label class="field-label" for="contact-name">Your name *</label><input class="field-control" id="contact-name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required aria-describedby="contact-name-error">@if($formErrorBag->has('name'))<p id="contact-name-error" class="mt-2 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('name') }}</p>@endif</div>
                                <div><label class="field-label" for="contact-email">Email address *</label><input class="field-control" id="contact-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required aria-describedby="contact-email-error">@if($formErrorBag->has('email'))<p id="contact-email-error" class="mt-2 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('email') }}</p>@endif</div>
                            </div>
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div><label class="field-label" for="contact-phone">Phone number (optional)</label><input class="field-control" id="contact-phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" aria-describedby="contact-phone-error">@if($formErrorBag->has('phone'))<p id="contact-phone-error" class="mt-2 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('phone') }}</p>@endif</div>
                                <div><label class="field-label" for="enquiry-type">What is your enquiry about? *</label><select class="field-control" id="enquiry-type" name="enquiry_type" required aria-describedby="enquiry-type-error"><option value="">Choose a subject</option>@foreach(['general' => 'General enquiry', 'fair_week' => 'Fair Week or an event', 'stalls' => 'Stall bookings', 'volunteering' => 'Volunteering', 'walk' => 'Scalby Walk', 'website' => 'Website help'] as $value => $label)<option value="{{ $value }}" @selected(old('enquiry_type') === $value)>{{ $label }}</option>@endforeach</select>@if($formErrorBag->has('enquiry_type'))<p id="enquiry-type-error" class="mt-2 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('enquiry_type') }}</p>@endif</div>
                            </div>
                            <div><label class="field-label" for="related-to">Related event or page (optional)</label><input class="field-control" id="related-to" name="related_to" type="text" value="{{ old('related_to', $relatedDefault) }}" aria-describedby="related-to-hint related-to-error"><p id="related-to-hint" class="mt-2 text-sm text-hedge-800/70">For example, “Ceilidh”, “Fair Day stalls” or “Scalby Walk”.</p>@if($formErrorBag->has('related_to'))<p id="related-to-error" class="mt-2 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('related_to') }}</p>@endif</div>
                            <div><label class="field-label" for="contact-message">Your message *</label><textarea class="field-control min-h-44 resize-y" id="contact-message" name="message" required aria-describedby="contact-message-error">{{ old('message') }}</textarea>@if($formErrorBag->has('message'))<p id="contact-message-error" class="mt-2 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('message') }}</p>@endif</div>
                            <label class="flex items-start gap-3 text-sm text-hedge-800"><input class="mt-1 size-5 shrink-0 accent-hedge-700" name="consent" type="checkbox" value="1" @checked(old('consent')) required><span>I agree that the Scalby Fair Committee may use these details to respond to my enquiry. Read our <a class="font-semibold text-barn-700 underline underline-offset-4" href="/privacy">privacy policy</a>. *</span></label>@if($formErrorBag->has('consent'))<p class="-mt-4 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('consent') }}</p>@endif
                            <div class="hidden" aria-hidden="true"><label for="contact-website">Leave this field empty</label><input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off"></div>
                            <div><button class="inline-flex min-h-12 items-center justify-center border-2 border-barn-600 bg-barn-600 px-6 py-3 font-semibold text-white hover:-translate-y-0.5 hover:border-barn-700 hover:bg-barn-700" type="submit">Send message</button></div>
                        </s:form:contact>
                    </section>
                </div>

                <aside class="border-t-4 border-wheat-300 bg-hedge-900 p-7 text-cream-50 lg:sticky lg:top-28 lg:col-span-4 lg:col-start-9">
                    <p class="text-xs font-semibold tracking-[0.16em] text-wheat-300 uppercase">Before you send</p>
                    <h2 class="mt-3 font-serif text-3xl">Help us help you</h2>
                    <ul class="mt-5 grid gap-4 text-hedge-100"><li>Include the event name and year where relevant.</li><li>For stall applications, use the Stall Bookings page first.</li><li>Committee members are volunteers, so replies may not be immediate.</li></ul>
                    @if($siteSettings?->contact_email)<a class="mt-7 inline-flex font-semibold text-wheat-300 underline underline-offset-4" href="mailto:{{ $siteSettings->contact_email }}">{{ $siteSettings->contact_email }}</a>@endif
                </aside>
            </div>
        </section>
    </main>
</x-layouts.app>
