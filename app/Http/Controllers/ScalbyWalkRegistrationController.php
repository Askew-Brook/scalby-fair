<?php

namespace App\Http\Controllers;

use App\Services\ScalbyWalkRegistrationFinaliser;
use App\Services\StripeCheckoutClient;
use App\Support\ScalbyWalkCatalogue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Statamic\Facades\Form;
use Throwable;
use UnexpectedValueException;

class ScalbyWalkRegistrationController extends Controller
{
    public function checkout(Request $request, ScalbyWalkCatalogue $catalogue, StripeCheckoutClient $stripe): RedirectResponse
    {
        if (! $catalogue->registrationsAreOpen()) {
            throw ValidationException::withMessages([
                'booking' => 'Scalby Walk registrations are not currently open.',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email:rfc', 'max:160', 'confirmed'],
            'email_confirmation' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['required', 'string', 'max:40'],
            'address_line_1' => ['required', 'string', 'max:180'],
            'address_line_2' => ['nullable', 'string', 'max:180'],
            'town' => ['required', 'string', 'max:120'],
            'county' => ['nullable', 'string', 'max:120'],
            'postcode' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:120'],
            'adult_walkers' => ['nullable', 'array', 'max:10'],
            'adult_walkers.*.first_name' => ['required', 'string', 'max:80'],
            'adult_walkers.*.last_name' => ['required', 'string', 'max:80'],
            'adult_walkers.*.age' => ['required', 'integer', 'min:18', 'max:120'],
            'adult_walkers.*.gender' => ['required', 'string', 'in:M,F,Other,Prefer not to say'],
            'adult_walkers.*.postcode' => ['required', 'string', 'max:20'],
            'junior_walkers' => ['nullable', 'array', 'max:10'],
            'junior_walkers.*.first_name' => ['required', 'string', 'max:80'],
            'junior_walkers.*.last_name' => ['required', 'string', 'max:80'],
            'junior_walkers.*.age' => ['required', 'integer', 'min:0', 'max:17'],
            'junior_walkers.*.gender' => ['required', 'string', 'in:M,F,Other,Prefer not to say'],
            'junior_walkers.*.postcode' => ['required', 'string', 'max:20'],
            'dogs' => ['nullable', 'array', 'max:10'],
            'dogs.*.name' => ['required', 'string', 'max:80'],
            'dogs.*.age' => ['required', 'integer', 'min:0', 'max:30'],
            'donation' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'walker_details_confirmation' => ['accepted'],
            'rules_confirmation' => ['accepted'],
            'privacy_consent' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ], [
            'walker_details_confirmation.accepted' => 'You must confirm that the details for every walker are complete.',
            'rules_confirmation.accepted' => 'You must confirm that all walkers will follow the Scalby Walk rules and event instructions.',
            'privacy_consent.accepted' => 'You must agree to the use of these details to process the registration.',
            'adult_walkers.max' => 'You can register up to 10 adult walkers in one booking.',
            'junior_walkers.max' => 'You can register up to 10 under-18 walkers in one booking.',
            'adult_walkers.*.age.min' => 'Adult walkers must be aged 18 or over on the event date.',
            'junior_walkers.*.age.max' => 'Under-18 walkers must be aged 17 or younger on the event date.',
            'dogs.max' => 'You can add up to 10 dogs to one booking.',
        ]);

        $validator->after(function ($validator) use ($catalogue, $request): void {
            $adultWalkers = $request->input('adult_walkers', []);
            $juniorWalkers = $request->input('junior_walkers', []);
            $adultCount = is_array($adultWalkers) ? count($adultWalkers) : 0;
            $juniorCount = is_array($juniorWalkers) ? count($juniorWalkers) : 0;

            if ($adultCount + $juniorCount < 1) {
                $validator->errors()->add('walkers', 'Please add at least one adult or junior walker.');
            }

            if ($adultCount > 0 && ! $catalogue->adultBookingsAreAvailable()) {
                $validator->errors()->add('walkers', 'Adult bookings are no longer available. Please review the walkers in this registration.');
            }

            if ($juniorCount > 0 && ! $catalogue->juniorBookingsAreAvailable()) {
                $validator->errors()->add('walkers', 'Junior bookings are no longer available. Please review the walkers in this registration.');
            }

            if ($request->filled('donation') && ! $catalogue->donationsAreEnabled()) {
                $validator->errors()->add('donation', 'Online donations are not currently available.');
            }
        });

        $validated = $validator->validate();
        $adultWalkers = collect($validated['adult_walkers'] ?? [])->values();
        $juniorWalkers = collect($validated['junior_walkers'] ?? [])->values();
        $dogs = collect($validated['dogs'] ?? [])->values();
        $donationPence = $catalogue->donationsAreEnabled()
            ? (int) round(((float) ($validated['donation'] ?? 0)) * 100)
            : 0;
        $lineItems = $this->lineItems($catalogue, $adultWalkers, $juniorWalkers, $donationPence);
        $totalPence = (int) $lineItems->sum('line_total');
        $recipients = $catalogue->recipientEmails();

        if ($recipients->isEmpty()) {
            throw ValidationException::withMessages([
                'booking' => 'Online registrations are temporarily unavailable. Please contact the Walk organisers.',
            ]);
        }

        $form = Form::find('walk_registration');

        if (! $form) {
            throw new UnexpectedValueException('The Scalby Walk registration form is not configured.');
        }

        $registration = $form->makeSubmission();
        $registration->id();
        $registration->data([
            'booking_year' => $catalogue->year(),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address_line_1' => $validated['address_line_1'],
            'address_line_2' => $validated['address_line_2'] ?? null,
            'town' => $validated['town'],
            'county' => $validated['county'] ?? null,
            'postcode' => $validated['postcode'],
            'country' => $validated['country'],
            'adult_walkers' => $adultWalkers->all(),
            'adult_walkers_summary' => $this->walkerSummary($adultWalkers),
            'adult_walkers_names' => $this->walkerNames($adultWalkers),
            'adult_walkers_ages' => $this->pipeSeparated($adultWalkers, 'age'),
            'adult_walkers_genders' => $this->pipeSeparated($adultWalkers, 'gender'),
            'adult_walkers_postcodes' => $this->pipeSeparated($adultWalkers, 'postcode', true),
            'junior_walkers' => $juniorWalkers->all(),
            'junior_walkers_summary' => $this->walkerSummary($juniorWalkers),
            'junior_walkers_names' => $this->walkerNames($juniorWalkers),
            'junior_walkers_ages' => $this->pipeSeparated($juniorWalkers, 'age'),
            'junior_walkers_genders' => $this->pipeSeparated($juniorWalkers, 'gender'),
            'junior_walkers_postcodes' => $this->pipeSeparated($juniorWalkers, 'postcode', true),
            'adult_count' => $adultWalkers->count(),
            'junior_count' => $juniorWalkers->count(),
            'dogs' => $dogs->all(),
            'dogs_summary' => $this->dogSummary($dogs),
            'dog_count' => $dogs->count(),
            'dog_names' => $this->pipeSeparated($dogs, 'name'),
            'dog_ages' => $this->pipeSeparated($dogs, 'age'),
            'donation_pence' => $donationPence,
            'donation' => '£'.number_format($donationPence / 100, 2),
            'line_items' => $lineItems->all(),
            'total_pence' => $totalPence,
            'total' => '£'.number_format($totalPence / 100, 2),
            'walker_details_confirmation' => true,
            'rules_confirmation' => true,
            'privacy_consent' => true,
            'recipient_emails' => $recipients->all(),
            'payment_status' => 'creating_checkout',
            'stripe_checkout_session_id' => null,
            'stripe_payment_intent_id' => null,
            'paid_at' => null,
            'confirmation_sent_at' => null,
        ]);
        $registration->saveQuietly();

        try {
            $checkoutSession = $stripe->createWalkCheckoutSession(
                registrationId: $registration->id(),
                year: $catalogue->year(),
                email: $validated['email'],
                registrantName: trim($validated['first_name'].' '.$validated['last_name']),
                items: $lineItems->all(),
            );
        } catch (Throwable $exception) {
            report($exception);

            $registration->set('payment_status', 'checkout_error')->saveQuietly();

            return back()
                ->withInput()
                ->withErrors(['payment' => 'We could not start the secure payment. No payment has been taken; please try again.']);
        }

        $registration
            ->set('payment_status', 'awaiting_payment')
            ->set('stripe_checkout_session_id', $checkoutSession['id'])
            ->saveQuietly();

        return redirect()->away($checkoutSession['url']);
    }

    public function success(Request $request, StripeCheckoutClient $stripe, ScalbyWalkRegistrationFinaliser $finaliser): View
    {
        $sessionId = (string) $request->query('session_id');

        abort_unless(preg_match('/^cs_[A-Za-z0-9_]+$/', $sessionId) === 1, 404);

        $paid = false;

        try {
            $checkoutSession = $stripe->retrieveCheckoutSession($sessionId);
            $paid = $finaliser->finalise($checkoutSession);
        } catch (Throwable $exception) {
            report($exception);
        }

        return view('scalby-walk.success', compact('paid'));
    }

    public function webhook(Request $request, StripeCheckoutClient $stripe, ScalbyWalkRegistrationFinaliser $finaliser): JsonResponse
    {
        try {
            $event = $stripe->parseWebhook($request->getContent(), $request->header('Stripe-Signature'));
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['received' => false], 400);
        }

        $checkoutSession = data_get($event, 'data.object');

        if (! is_array($checkoutSession)) {
            return response()->json(['received' => true]);
        }

        if (data_get($checkoutSession, 'metadata.booking_type') !== 'scalby_walk') {
            return response()->json(['received' => true]);
        }

        if (in_array($event['type'] ?? null, ['checkout.session.completed', 'checkout.session.async_payment_succeeded'], true)) {
            $finaliser->finalise($checkoutSession);
        }

        if (($event['type'] ?? null) === 'checkout.session.expired') {
            $registrationId = (string) data_get($checkoutSession, 'metadata.registration_id', '');
            $finaliser->markExpired($registrationId, (string) ($checkoutSession['id'] ?? ''));
        }

        return response()->json(['received' => true]);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $adultWalkers
     * @param  Collection<int, array<string, mixed>>  $juniorWalkers
     * @return Collection<int, array{code: string, name: string, description: string, unit_amount: int, quantity: int, line_total: int}>
     */
    private function lineItems(ScalbyWalkCatalogue $catalogue, Collection $adultWalkers, Collection $juniorWalkers, int $donationPence): Collection
    {
        return collect([
            $adultWalkers->isNotEmpty() ? [
                'code' => 'adult_walkers',
                'name' => 'Adult walker',
                'description' => "Scalby Charity Walk {$catalogue->year()}",
                'unit_amount' => $catalogue->adultPrice(),
                'quantity' => $adultWalkers->count(),
                'line_total' => $catalogue->adultPrice() * $adultWalkers->count(),
            ] : null,
            $juniorWalkers->isNotEmpty() ? [
                'code' => 'junior_walkers',
                'name' => 'Junior walker',
                'description' => "Scalby Charity Walk {$catalogue->year()}",
                'unit_amount' => $catalogue->juniorPrice(),
                'quantity' => $juniorWalkers->count(),
                'line_total' => $catalogue->juniorPrice() * $juniorWalkers->count(),
            ] : null,
            $donationPence > 0 ? [
                'code' => 'charity_donation',
                'name' => 'Donation to the Scalby Walk charity',
                'description' => 'Thank you for supporting the chosen charity.',
                'unit_amount' => $donationPence,
                'quantity' => 1,
                'line_total' => $donationPence,
            ] : null,
        ])->filter()->values();
    }

    /** @param Collection<int, array<string, mixed>> $walkers */
    private function walkerSummary(Collection $walkers): string
    {
        return $walkers->map(fn (array $walker) => sprintf(
            '%s — age %s, %s, %s',
            $this->walkerName($walker),
            $walker['age'],
            $walker['gender'],
            Str::upper($walker['postcode'])
        ))->implode("\n");
    }

    /** @param Collection<int, array<string, mixed>> $walkers */
    private function walkerNames(Collection $walkers): string
    {
        return $walkers->map(fn (array $walker) => $this->walkerName($walker))->implode('|');
    }

    /** @param array<string, mixed> $walker */
    private function walkerName(array $walker): string
    {
        return trim(($walker['first_name'] ?? '').' '.($walker['last_name'] ?? ''));
    }

    /** @param Collection<int, array<string, mixed>> $items */
    private function pipeSeparated(Collection $items, string $key, bool $uppercase = false): string
    {
        return $items->map(function (array $item) use ($key, $uppercase): string {
            $value = (string) ($item[$key] ?? '');

            return $uppercase ? Str::upper($value) : $value;
        })->implode('|');
    }

    /** @param Collection<int, array<string, mixed>> $dogs */
    private function dogSummary(Collection $dogs): string
    {
        return $dogs->map(fn (array $dog) => sprintf(
            '%s — age %s',
            $dog['name'],
            $dog['age']
        ))->implode("\n");
    }
}
