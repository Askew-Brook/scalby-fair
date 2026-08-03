@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\MessageBag;

    $entryYear = \Statamic\View\Blade\value($year);
    $categoryItems = collect(\Statamic\View\Blade\value($categories))->filter()->values();
    $ruleItems = collect(\Statamic\View\Blade\value($rules))->filter()->values();
    $prizeItems = collect(\Statamic\View\Blade\value($prizes))->filter()->values();
    $entriesAreOpen = (bool) \Statamic\View\Blade\value($entries_open);
    $showWinners = (bool) \Statamic\View\Blade\value($winners_announced);
    $winnerImagesValue = \Statamic\View\Blade\value($winners_gallery);
    $winnerImages = ($winnerImagesValue instanceof \Statamic\Contracts\Query\Builder ? $winnerImagesValue->get() : collect($winnerImagesValue))->filter()->values();
    $closingDateValue = \Statamic\View\Blade\value($closing_date);
    $closingDateLabel = $closingDateValue ? Carbon::parse($closingDateValue)->format('l j F Y') : null;
    $formErrorBag = session('errors')?->getBag('form.photography_competition') ?? new MessageBag;
@endphp

<x-layouts.app :title="$title" :seo-title="$seo_title" :seo-description="$seo_description ?: $summary" :share-image="$share_image ?: $featured_image">
    <main id="main-content">
        <x-page-hero :title="$title" :eyebrow="$eyebrow" :introduction="$introduction" :image="$featured_image" :supporting-image="$supporting_image" />

        <section class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-20">
            <x-breadcrumbs :items="[['title' => $title]]" />
            <div class="mt-10 grid gap-10 lg:grid-cols-12 lg:items-start">
                <div class="lg:col-span-7">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Open to every kind of photographer</p>
                    <h2 class="mt-3 font-serif text-4xl tracking-tight text-balance text-hedge-900 sm:text-5xl">Capture the spirit of Scalby Fair</h2>
                    <p class="mt-6 max-w-3xl font-serif text-xl leading-9 text-pretty text-hedge-800/85">{{ $summary }}</p>
                    <p class="mt-5 max-w-3xl text-lg leading-8 text-pretty text-hedge-800/80">Enter for the chance to have your images featured across future Scalby Fair publicity, the website and social media channels.</p>
                </div>
                <aside class="border-t-4 border-wheat-300 bg-cream-100 p-7 shadow-sm lg:col-span-4 lg:col-start-9" aria-labelledby="closing-date-heading">
                    <p class="text-xs font-semibold tracking-[0.15em] text-barn-600 uppercase">Closing date</p>
                    <h2 id="closing-date-heading" class="mt-2 font-serif text-3xl tracking-tight text-hedge-900">{{ $closingDateLabel }}</h2>
                    @if($winners_announcement)<p class="mt-4 text-pretty text-hedge-800/80">{{ $winners_announcement }}</p>@endif
                    <a href="#enter" class="mt-6 inline-flex min-h-12 items-center font-semibold text-barn-700 underline decoration-2 underline-offset-4 hover:text-barn-600">Go to the entry form <span class="ml-2" aria-hidden="true">↓</span></a>
                </aside>
            </div>
        </section>

        <section class="border-y border-hedge-700/15 bg-cream-100 py-16 sm:py-24" aria-labelledby="categories-heading">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="grid gap-8 lg:grid-cols-12 lg:items-end">
                    <div class="lg:col-span-5"><x-section-heading id="categories-heading" eyebrow="What to photograph" heading="Competition categories" /></div>
                    <p class="max-w-2xl text-lg leading-8 text-pretty text-hedge-800/80 lg:col-span-6 lg:col-start-7">Look for the moments that feel unmistakably like Scalby Fair—from the big spectacle to the quiet details happening just beyond the crowd.</p>
                </div>
                <ol class="mt-12 grid gap-px bg-hedge-700/15 sm:grid-cols-2 lg:grid-cols-4" role="list">
                    @foreach($categoryItems as $category)
                        <li class="group min-h-40 bg-cream-50 p-6 transition-colors hover:bg-hedge-50">
                            <span class="font-serif text-2xl text-wheat-500" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <p class="mt-6 font-serif text-2xl tracking-tight text-balance text-hedge-900">{{ $category }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-24">
            <div class="grid gap-12 lg:grid-cols-12">
                <article class="lg:col-span-5" aria-labelledby="how-to-enter-heading">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Your photographs</p>
                    <h2 id="how-to-enter-heading" class="mt-3 font-serif text-4xl tracking-tight text-hedge-900">How to enter</h2>
                    <div class="prose mt-6">{!! \Statamic\Statamic::modify($how_to_enter)->markdown() !!}</div>
                    <div class="mt-8 border-l-2 border-wheat-300 pl-5">
                        <p class="font-semibold text-hedge-900">Upload requirements</p>
                        <p class="mt-2 text-hedge-800/80">Up to 5 photographs · 10MB maximum per image · JPG, PNG or HEIC</p>
                    </div>
                </article>
                <article class="bg-hedge-900 p-8 text-cream-50 shadow-soft sm:p-10 lg:col-span-6 lg:col-start-7" aria-labelledby="rules-heading">
                    <p class="text-sm font-semibold tracking-[0.16em] text-wheat-300 uppercase">Please read before entering</p>
                    <h2 id="rules-heading" class="mt-3 font-serif text-4xl tracking-tight">Competition rules</h2>
                    <ul class="mt-7 grid gap-4" role="list">
                        @foreach($ruleItems as $rule)
                            <li class="flex gap-4 border-t border-cream-50/15 pt-4"><span class="mt-1 text-wheat-300" aria-hidden="true">◆</span><span class="text-pretty text-hedge-100">{{ $rule }}</span></li>
                        @endforeach
                    </ul>
                </article>
            </div>
        </section>

        <section class="relative isolate overflow-hidden bg-hedge-800 py-16 text-cream-50 sm:py-24" aria-labelledby="prizes-heading">
            @if($card_image ?: $featured_image)
                <x-responsive-image :asset="$card_image ?: $featured_image" :width="1800" :height="900" sizes="100vw" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" />
            @endif
            <div class="absolute inset-0 -z-10 bg-hedge-900/90" aria-hidden="true"></div>
            <div class="mx-auto grid max-w-7xl gap-10 px-5 sm:px-8 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-4">
                    <p class="text-sm font-semibold tracking-[0.16em] text-wheat-300 uppercase">Recognition</p>
                    <h2 id="prizes-heading" class="mt-3 font-serif text-4xl tracking-tight text-balance sm:text-5xl">Where winning photographs will appear</h2>
                </div>
                <div class="grid gap-px bg-cream-50/15 sm:grid-cols-2 lg:col-span-7 lg:col-start-6">
                    @foreach($prizeItems as $prize)
                        <p class="bg-hedge-900/70 p-6 font-semibold text-cream-50">{{ $prize }}</p>
                    @endforeach
                </div>
                @if($additional_prize_text)<p class="text-sm text-hedge-100/80 lg:col-span-7 lg:col-start-6">{{ $additional_prize_text }}</p>@endif
            </div>
        </section>

        <section id="enter" class="scroll-mt-6 py-16 sm:py-24" aria-labelledby="entry-form-heading">
            <div class="mx-auto max-w-5xl px-5 sm:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="text-sm font-semibold tracking-[0.16em] text-barn-600 uppercase">Share your view of the Fair</p>
                    <h2 id="entry-form-heading" class="mt-3 font-serif text-4xl tracking-tight text-balance text-hedge-900 sm:text-5xl">Enter the {{ $entryYear }} competition</h2>
                    <p class="mt-5 text-lg leading-8 text-pretty text-hedge-800/80">Complete the form and upload your chosen photographs. Required fields are marked with an asterisk.</p>
                </div>

                @if($entriesAreOpen)
                    <s:form:photography_competition class="mt-12 border border-hedge-700/15 bg-cream-100 p-6 shadow-soft sm:p-10" id="photography-entry-form">
                        @if($success)
                            <div class="border-l-4 border-hedge-600 bg-hedge-50 p-6" role="status">
                                <p class="text-sm font-semibold tracking-[0.16em] text-hedge-700 uppercase">Entry received</p>
                                <h3 class="mt-2 font-serif text-3xl tracking-tight text-hedge-900">Thank you for sharing your photographs</h3>
                                <p class="mt-3 text-pretty text-hedge-800/80">Your entry has been sent to the Scalby Fair Committee.</p>
                            </div>
                        @else
                            @if($formErrorBag->isNotEmpty())
                                <div class="mb-8 border-l-4 border-barn-600 bg-barn-100 p-5 text-barn-700" role="alert">
                                    <p class="font-semibold">Please check the highlighted fields and try again.</p>
                                </div>
                            @endif

                            <input type="hidden" name="competition_year" value="{{ $entryYear }}">
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div>
                                    <label class="field-label" for="competition-name">Your name <span aria-hidden="true">*</span></label>
                                    <input class="field-control" id="competition-name" name="entrant_name" type="text" autocomplete="name" required maxlength="120" value="{{ old('entrant_name') }}" @if($formErrorBag->has('entrant_name')) aria-invalid="true" aria-describedby="competition-name-error" @endif>
                                    @if($formErrorBag->has('entrant_name'))<p id="competition-name-error" class="mt-2 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('entrant_name') }}</p>@endif
                                </div>
                                <div>
                                    <label class="field-label" for="competition-email">Email address <span aria-hidden="true">*</span></label>
                                    <input class="field-control" id="competition-email" name="email" type="email" autocomplete="email" required maxlength="160" value="{{ old('email') }}" @if($formErrorBag->has('email')) aria-invalid="true" aria-describedby="competition-email-error" @endif>
                                    @if($formErrorBag->has('email'))<p id="competition-email-error" class="mt-2 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('email') }}</p>@endif
                                </div>
                                <div>
                                    <label class="field-label" for="competition-category">Competition category <span aria-hidden="true">*</span></label>
                                    <select class="field-control" id="competition-category" name="category" required @if($formErrorBag->has('category')) aria-invalid="true" aria-describedby="competition-category-error" @endif>
                                        <option value="">Choose a category</option>
                                        @foreach([
                                            'fair_day' => 'Fair Day',
                                            'fair_week_events' => 'Fair Week Events',
                                            'community_spirit' => 'Community Spirit',
                                            'families_having_fun' => 'Families Having Fun',
                                            'performers_entertainment' => 'Performers & Entertainment',
                                            'village_life' => 'Village Life',
                                            'behind_the_scenes' => 'Behind the Scenes',
                                            'creative_artistic' => 'Creative / Artistic Image',
                                        ] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @if($formErrorBag->has('category'))<p id="competition-category-error" class="mt-2 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('category') }}</p>@endif
                                </div>
                                <div>
                                    <label class="field-label" for="competition-title">Photo title <span class="font-normal normal-case text-hedge-800/60">(optional)</span></label>
                                    <input class="field-control" id="competition-title" name="photo_title" type="text" maxlength="160" value="{{ old('photo_title') }}">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="field-label" for="competition-location">Where was the photograph taken? <span aria-hidden="true">*</span></label>
                                    <input class="field-control" id="competition-location" name="photo_location" type="text" required maxlength="200" value="{{ old('photo_location') }}" @if($formErrorBag->has('photo_location')) aria-invalid="true" aria-describedby="competition-location-error" @endif>
                                    @if($formErrorBag->has('photo_location'))<p id="competition-location-error" class="mt-2 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('photo_location') }}</p>@endif
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="field-label" for="competition-description">Short description <span class="font-normal normal-case text-hedge-800/60">(optional)</span></label>
                                    <textarea class="field-control min-h-32" id="competition-description" name="photo_description" maxlength="1000" rows="4">{{ old('photo_description') }}</textarea>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="field-label" for="competition-photos">Upload photographs <span aria-hidden="true">*</span></label>
                                    <div class="border-2 border-dashed border-hedge-300 bg-cream-50 p-6 transition-colors focus-within:border-hedge-700 hover:border-hedge-600">
                                        <input class="block w-full text-sm file:mr-4 file:min-h-11 file:border-0 file:bg-hedge-700 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-hedge-800" id="competition-photos" name="photographs[]" type="file" accept=".jpg,.jpeg,.png,.heic,image/jpeg,image/png,image/heic,image/heif" multiple required aria-describedby="competition-photos-help{{ $formErrorBag->has('photographs') ? ' competition-photos-error' : '' }}" @if($formErrorBag->has('photographs')) aria-invalid="true" @endif>
                                        <p id="competition-photos-help" class="mt-3 text-sm text-hedge-800/70">Choose between 1 and 5 images. Maximum 10MB per image.</p>
                                    </div>
                                    @if($formErrorBag->has('photographs'))<p id="competition-photos-error" class="mt-2 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('photographs') }}</p>@endif
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="flex items-start gap-3 border-t border-hedge-700/20 pt-6 text-pretty text-hedge-800" for="competition-terms">
                                        <input class="mt-1 size-5 shrink-0 accent-hedge-700" id="competition-terms" name="terms_agreed" type="checkbox" value="1" required @checked((bool) old('terms_agreed')) @if($formErrorBag->has('terms_agreed')) aria-invalid="true" aria-describedby="competition-terms-error" @endif>
                                        <span>I have read and accept the competition rules, including permission for the Scalby Fair Committee to use my submitted photographs as described above. <span aria-hidden="true">*</span></span>
                                    </label>
                                    @if($formErrorBag->has('terms_agreed'))<p id="competition-terms-error" class="mt-2 text-sm font-semibold text-barn-700">{{ $formErrorBag->first('terms_agreed') }}</p>@endif
                                </div>
                            </div>
                            <input type="text" name="{{ $honeypot ?? 'website' }}" class="sr-only" tabindex="-1" autocomplete="off" aria-hidden="true">
                            <button type="submit" class="mt-8 inline-flex min-h-12 items-center justify-center border-2 border-barn-600 bg-barn-600 px-6 py-3 text-center font-semibold text-white shadow-sm hover:-translate-y-0.5 hover:border-barn-700 hover:bg-barn-700 hover:shadow-md active:translate-y-0 focus-visible:outline-barn-600">Submit photographs</button>
                        @endif
                    </s:form:photography_competition>
                @else
                    <x-empty-state class="mt-12" heading="Entries are now closed" text="Thank you to everyone who shared their view of Scalby Fair. Winners will be announced through the website and Facebook page." />
                @endif
            </div>
        </section>

        @if($showWinners)
            <section class="border-y border-hedge-700/15 bg-cream-100 py-16 sm:py-24" aria-labelledby="winners-heading">
                <div class="mx-auto max-w-7xl px-5 sm:px-8">
                    <x-section-heading eyebrow="The judges’ selection" heading="{{ $entryYear }} competition winners" :text="$winners_intro" />
                    @if($winnerImages->isNotEmpty())
                        <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">@foreach($winnerImages as $image)<div class="image-zoom"><x-responsive-image :asset="$image" class="aspect-[4/3] w-full object-cover" /></div>@endforeach</div>
                    @endif
                    @if($winner_notes)<div class="prose mt-10 max-w-3xl">{!! \Statamic\Statamic::modify($winner_notes)->markdown() !!}</div>@endif
                </div>
            </section>
        @endif

        <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-20">
            <x-cta heading="Need help with your entry?" :text="$help_text" :href="'/contact?about=Photography+Competition+' . $entryYear . '#contact-form'" label="Contact the committee" />
        </section>
    </main>
</x-layouts.app>
