<?php

namespace App\Http\Controllers;

use App\Services\DonationPaymentFinaliser;
use App\Services\StripeCheckoutClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Statamic\Facades\Form;
use Throwable;
use UnexpectedValueException;

class DonationController extends Controller
{
    public function checkout(Request $request, StripeCheckoutClient $stripe): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email:rfc', 'max:160', 'confirmed'],
            'email_confirmation' => ['required', 'email:rfc', 'max:160'],
            'amount' => ['required', 'numeric', 'min:2', 'max:10000'],
            'privacy_consent' => ['accepted'],
            'donation_reference' => ['nullable', 'max:0'],
        ], [
            'amount.min' => 'The minimum online donation is £2.00.',
            'privacy_consent.accepted' => 'You must agree to the use of your details to process this donation.',
        ]);

        $amountPence = (int) round((float) $validated['amount'] * 100);
        $form = Form::find('donation');

        if (! $form) {
            throw new UnexpectedValueException('The donation form is not configured.');
        }

        $submission = $form->makeSubmission();
        $submission->id();
        $submission->data([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'amount_pence' => $amountPence,
            'amount' => '£'.number_format($amountPence / 100, 2),
            'privacy_consent' => true,
            'payment_status' => 'creating_checkout',
            'stripe_checkout_session_id' => null,
            'stripe_payment_intent_id' => null,
            'paid_at' => null,
        ]);
        $submission->saveQuietly();

        try {
            $checkoutSession = $stripe->createDonationCheckoutSession(
                donationId: $submission->id(),
                email: $validated['email'],
                donorName: trim($validated['first_name'].' '.$validated['last_name']),
                amountPence: $amountPence,
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

    public function success(Request $request, StripeCheckoutClient $stripe, DonationPaymentFinaliser $finaliser): View
    {
        $sessionId = (string) $request->query('session_id');

        abort_unless(preg_match('/^cs_[A-Za-z0-9_]+$/', $sessionId) === 1, 404);

        $paid = false;

        try {
            $paid = $finaliser->finalise($stripe->retrieveCheckoutSession($sessionId));
        } catch (Throwable $exception) {
            report($exception);
        }

        return view('donations.success', compact('paid'));
    }

    public function webhook(Request $request, StripeCheckoutClient $stripe, DonationPaymentFinaliser $finaliser): JsonResponse
    {
        try {
            $event = $stripe->parseWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                config('services.stripe.donation_webhook_secret'),
            );
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['received' => false], 400);
        }

        $checkoutSession = data_get($event, 'data.object');

        if (! is_array($checkoutSession) || data_get($checkoutSession, 'metadata.booking_type') !== 'donation') {
            return response()->json(['received' => true]);
        }

        if (in_array($event['type'] ?? null, ['checkout.session.completed', 'checkout.session.async_payment_succeeded'], true)) {
            $finaliser->finalise($checkoutSession);
        }

        if (($event['type'] ?? null) === 'checkout.session.expired') {
            $finaliser->markExpired(
                (string) data_get($checkoutSession, 'metadata.donation_id', ''),
                (string) ($checkoutSession['id'] ?? ''),
            );
        }

        return response()->json(['received' => true]);
    }
}
