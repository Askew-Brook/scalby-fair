<?php

namespace App\Http\Controllers;

use App\Services\StallBookingPaymentFinaliser;
use App\Services\StripeCheckoutClient;
use App\Support\StallBookingCatalogue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Statamic\Facades\Form;
use Throwable;
use UnexpectedValueException;

class StallBookingController extends Controller
{
    public function checkout(Request $request, StallBookingCatalogue $catalogue, StripeCheckoutClient $stripe): RedirectResponse
    {
        if (! $catalogue->bookingsAreOpen()) {
            throw ValidationException::withMessages([
                'booking' => 'Stall bookings are not currently open.',
            ]);
        }

        $availableItems = $catalogue->availableItems();
        $submittedItems = $request->input('items', []);
        $submittedItems = is_array($submittedItems) ? $submittedItems : [];

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email:rfc', 'max:160', 'confirmed'],
            'email_confirmation' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['required', 'string', 'max:40'],
            'business_name' => ['required', 'string', 'max:180'],
            'address_line_1' => ['required', 'string', 'max:180'],
            'address_line_2' => ['nullable', 'string', 'max:180'],
            'town' => ['required', 'string', 'max:120'],
            'postcode' => ['required', 'string', 'max:20'],
            'stall_purpose' => ['required', 'string', 'min:5', 'max:2000'],
            'special_requirements' => ['required', 'string', 'max:2000'],
            'certificates' => ['required', 'string', 'max:2000'],
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0', 'max:20'],
            'acceptance' => ['accepted'],
            'privacy_consent' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ], [
            'items.required' => 'Please choose at least one stall-hire item.',
            'acceptance.accepted' => 'You must accept the stallholder declaration and conditions.',
            'privacy_consent.accepted' => 'You must agree to the use of your details to process this booking.',
        ]);

        $validator->after(function ($validator) use ($availableItems, $submittedItems): void {
            $selectedQuantity = 0;

            foreach ($submittedItems as $code => $quantity) {
                if (! $availableItems->has($code)) {
                    $validator->errors()->add('items', 'One of the selected items is no longer available. Please review your order.');

                    continue;
                }

                $quantity = (int) $quantity;
                $selectedQuantity += max(0, $quantity);

                if ($quantity > $availableItems->get($code)['max_quantity']) {
                    $validator->errors()->add("items.{$code}", 'The requested quantity is higher than the available booking limit.');
                }
            }

            if ((int) ($submittedItems['own_pitch'] ?? 0) > 0 && (int) ($submittedItems['electric_hookup'] ?? 0) > 0) {
                $validator->errors()->add('items', 'An electric hook-up is not available with your own gazebo.');
            }

            if ($selectedQuantity < 1) {
                $validator->errors()->add('items', 'Please choose at least one stall-hire item.');
            }
        });

        $validated = $validator->validate();

        $selectedItems = $availableItems
            ->map(function (array $item) use ($validated): ?array {
                $quantity = (int) data_get($validated, "items.{$item['code']}", 0);

                return $quantity > 0 ? [...$item, 'quantity' => $quantity, 'line_total' => $item['unit_amount'] * $quantity] : null;
            })
            ->filter()
            ->values();

        $totalPence = (int) $selectedItems->sum('line_total');
        $year = $catalogue->year();
        $recipientEmail = $catalogue->recipientEmail();

        if (! $recipientEmail) {
            throw ValidationException::withMessages([
                'booking' => 'Online bookings are temporarily unavailable. Please contact the Fair committee.',
            ]);
        }

        $form = Form::find('stall_booking');

        if (! $form) {
            throw new UnexpectedValueException('The stall booking form is not configured.');
        }

        $submission = $form->makeSubmission();
        $submission->id();
        $submission->data([
            'booking_year' => $year,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'business_name' => $validated['business_name'],
            'address_line_1' => $validated['address_line_1'],
            'address_line_2' => $validated['address_line_2'] ?? null,
            'town' => $validated['town'],
            'postcode' => $validated['postcode'],
            'stall_purpose' => $validated['stall_purpose'],
            'special_requirements' => $validated['special_requirements'],
            'certificates' => $validated['certificates'],
            'items' => $selectedItems->all(),
            'items_summary' => $selectedItems->map(fn (array $item) => sprintf(
                '%d × %s — £%s',
                $item['quantity'],
                $item['name'],
                number_format($item['line_total'] / 100, 2)
            ))->implode("\n"),
            'total_pence' => $totalPence,
            'total' => '£'.number_format($totalPence / 100, 2),
            'acceptance' => true,
            'privacy_consent' => true,
            'recipient_email' => $recipientEmail,
            'payment_status' => 'creating_checkout',
            'stripe_checkout_session_id' => null,
            'stripe_payment_intent_id' => null,
            'paid_at' => null,
            'confirmation_sent_at' => null,
        ]);
        $submission->saveQuietly();

        try {
            $checkoutSession = $stripe->createCheckoutSession(
                bookingId: $submission->id(),
                year: $year,
                email: $validated['email'],
                businessName: $validated['business_name'],
                items: $selectedItems->all(),
            );
        } catch (Throwable $exception) {
            report($exception);

            $submission->set('payment_status', 'checkout_error')->saveQuietly();

            return back()
                ->withInput()
                ->withErrors(['payment' => 'We could not start the secure payment. No payment has been taken; please try again.']);
        }

        $submission
            ->set('payment_status', 'awaiting_payment')
            ->set('stripe_checkout_session_id', $checkoutSession['id'])
            ->saveQuietly();

        return redirect()->away($checkoutSession['url']);
    }

    public function success(Request $request, StripeCheckoutClient $stripe, StallBookingPaymentFinaliser $finaliser): View
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

        return view('stall-bookings.success', compact('paid'));
    }

    public function webhook(Request $request, StripeCheckoutClient $stripe, StallBookingPaymentFinaliser $finaliser): JsonResponse
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

        $bookingType = data_get($checkoutSession, 'metadata.booking_type');

        if ($bookingType && $bookingType !== 'stall_booking') {
            return response()->json(['received' => true]);
        }

        if (in_array($event['type'] ?? null, ['checkout.session.completed', 'checkout.session.async_payment_succeeded'], true)) {
            $finaliser->finalise($checkoutSession);
        }

        if (($event['type'] ?? null) === 'checkout.session.expired') {
            $bookingId = (string) data_get($checkoutSession, 'metadata.booking_id', '');
            $finaliser->markExpired($bookingId, (string) ($checkoutSession['id'] ?? ''));
        }

        return response()->json(['received' => true]);
    }
}
