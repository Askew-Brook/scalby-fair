@php
    use Illuminate\Support\MessageBag;
    $pageContent = \Statamic\View\Blade\value($content);
    $formErrorBag = session('errors')?->getBag('form.newsletter') ?? new MessageBag;
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description ?: $introduction" :share-image="$share_image ?: $featured_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" :image="$featured_image" :supporting-image="$supporting_image" />

        <section class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-20" aria-labelledby="newsletter-heading">
            <x-breadcrumbs :items="[['title' => $title]]" />
            <div class="mt-10 grid gap-12 lg:grid-cols-12 lg:items-start">
                <div class="lg:col-span-7">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Occasional updates</p>
                    <h2 id="newsletter-heading" class="mt-3 font-serif text-4xl font-semibold tracking-tight text-balance text-hedge-900 sm:text-5xl">Keep me up to date</h2>
                    @if($pageContent)<div class="prose mt-6">{!! \Statamic\Statamic::modify($pageContent)->markdown() !!}</div>@endif

                    <section id="newsletter-form" class="mt-10 scroll-mt-28 border-t-4 border-wheat-300 bg-cream-100 p-6 sm:p-9" aria-labelledby="newsletter-form-heading">
                        <h3 id="newsletter-form-heading" class="font-serif text-3xl font-semibold tracking-tight text-hedge-900">Newsletter sign-up</h3>
                        <p class="mt-2 text-hedge-800/75">Required fields are marked with an asterisk.</p>

                        @if(request('sent') || session('success'))<div class="mt-6 border-l-4 border-hedge-700 bg-hedge-50 p-4 font-semibold text-hedge-900" role="status">Thank you. Your details have been added to the newsletter list.</div>@endif
                        @if($formErrorBag->any())<div class="mt-6 border-l-4 border-barn-600 bg-barn-100 p-4 text-barn-700" role="alert"><p class="font-semibold">Please check the following details:</p><ul class="mt-2 list-disc space-y-1 pl-5">@foreach(collect($formErrorBag->all())->unique() as $message)<li>{{ $message }}</li>@endforeach</ul></div>@endif

                        <s:form:newsletter class="mt-8 grid gap-6" redirect="/newsletter?sent=1#newsletter-form">
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div><label class="field-label" for="newsletter-first-name">First name *</label><input class="field-control" id="newsletter-first-name" name="first_name" type="text" value="{{ old('first_name') }}" autocomplete="given-name" required @if($formErrorBag->has('first_name')) aria-invalid="true" @endif>@if($formErrorBag->has('first_name'))<p class="mt-2 font-semibold text-barn-700">{{ $formErrorBag->first('first_name') }}</p>@endif</div>
                                <div><label class="field-label" for="newsletter-last-name">Last name *</label><input class="field-control" id="newsletter-last-name" name="last_name" type="text" value="{{ old('last_name') }}" autocomplete="family-name" required @if($formErrorBag->has('last_name')) aria-invalid="true" @endif>@if($formErrorBag->has('last_name'))<p class="mt-2 font-semibold text-barn-700">{{ $formErrorBag->first('last_name') }}</p>@endif</div>
                                <div><label class="field-label" for="newsletter-email">Email address *</label><input class="field-control" id="newsletter-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required @if($formErrorBag->has('email')) aria-invalid="true" @endif>@if($formErrorBag->has('email'))<p class="mt-2 font-semibold text-barn-700">{{ $formErrorBag->first('email') }}</p>@endif</div>
                                <div><label class="field-label" for="newsletter-email-confirmation">Confirm email address *</label><input class="field-control" id="newsletter-email-confirmation" name="email_confirmation" type="email" value="{{ old('email_confirmation') }}" autocomplete="email" required @if($formErrorBag->has('email_confirmation')) aria-invalid="true" @endif>@if($formErrorBag->has('email_confirmation'))<p class="mt-2 font-semibold text-barn-700">{{ $formErrorBag->first('email_confirmation') }}</p>@endif</div>
                            </div>
                            <label class="flex items-start gap-3 text-sm text-hedge-800"><input class="mt-1 size-5 shrink-0 accent-hedge-700" name="privacy_consent" type="checkbox" value="1" @checked(old('privacy_consent')) required><span>I agree that Scalby Fair may store these details and use them to send newsletter updates. Read the <a class="font-semibold text-barn-700 underline underline-offset-4" href="/privacy">privacy notice</a>. *</span></label>
                            @if($formErrorBag->has('privacy_consent'))<p class="-mt-4 font-semibold text-barn-700">{{ $formErrorBag->first('privacy_consent') }}</p>@endif
                            <div class="hidden" aria-hidden="true"><label for="newsletter-reference">Leave this field empty</label><input id="newsletter-reference" name="newsletter_reference" type="text" tabindex="-1" autocomplete="off"></div>
                            <div><button class="inline-flex min-h-12 items-center justify-center border-2 border-barn-600 bg-barn-600 px-6 py-3 font-semibold text-white hover:-translate-y-0.5 hover:border-barn-700 hover:bg-barn-700" type="submit">Keep me up to date</button></div>
                        </s:form:newsletter>
                    </section>
                </div>

                <aside class="border-t-4 border-wheat-300 bg-hedge-900 p-7 text-cream-50 lg:sticky lg:top-28 lg:col-span-4 lg:col-start-9">
                    <p class="text-xs font-semibold tracking-[0.16em] text-wheat-300 uppercase">What to expect</p>
                    <h2 class="mt-3 font-serif text-3xl font-semibold">Fair news without the noise</h2>
                    <p class="mt-5 text-pretty text-cream-100/80">Occasional updates about Fair Week, Fair Day, Scalby Walk and ways to take part. Your details remain managed by the Fair committee.</p>
                </aside>
            </div>
        </section>
    </main>
</x-layouts.app>
