<?php

namespace App\Services;

use App\Mail\StallBookingPaid;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Statamic\Facades\Form;
use UnexpectedValueException;

class StallBookingPaymentFinaliser
{
    /** @param array<string, mixed> $checkoutSession */
    public function finalise(array $checkoutSession): bool
    {
        if (($checkoutSession['payment_status'] ?? null) !== 'paid') {
            return false;
        }

        $bookingId = (string) data_get($checkoutSession, 'metadata.booking_id', $checkoutSession['client_reference_id'] ?? '');

        if ($bookingId === '') {
            throw new UnexpectedValueException('The paid Stripe session has no booking reference.');
        }

        return Cache::lock("stall-booking-payment:{$bookingId}", 15)->block(5, function () use ($bookingId, $checkoutSession): bool {
            $submission = Form::find('stall_booking')?->submission($bookingId);

            if (! $submission) {
                throw new UnexpectedValueException("Stall booking {$bookingId} could not be found.");
            }

            $sessionId = (string) ($checkoutSession['id'] ?? '');
            $storedSessionId = (string) $submission->get('stripe_checkout_session_id');

            if ($storedSessionId !== '' && ! hash_equals($storedSessionId, $sessionId)) {
                throw new UnexpectedValueException('The Stripe session does not match the stall booking.');
            }

            $expectedAmount = (int) $submission->get('total_pence');
            $paidAmount = (int) ($checkoutSession['amount_total'] ?? 0);

            if ($expectedAmount <= 0 || $paidAmount !== $expectedAmount) {
                throw new UnexpectedValueException('The Stripe payment total does not match the stall booking total.');
            }

            $submission
                ->set('payment_status', 'paid')
                ->set('stripe_checkout_session_id', $sessionId)
                ->set('stripe_payment_intent_id', (string) ($checkoutSession['payment_intent'] ?? ''))
                ->set('paid_at', now()->toIso8601String())
                ->saveQuietly();

            if ($submission->get('confirmation_sent_at')) {
                return true;
            }

            $recipient = (string) $submission->get('recipient_email');

            if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('The stall booking recipient email is invalid.');
            }

            Mail::to($recipient)->send(new StallBookingPaid($submission->data()->all()));

            $submission
                ->set('confirmation_sent_at', now()->toIso8601String())
                ->saveQuietly();

            return true;
        });
    }

    public function markExpired(string $bookingId, string $sessionId): void
    {
        $submission = Form::find('stall_booking')?->submission($bookingId);

        if (! $submission || $submission->get('payment_status') === 'paid') {
            return;
        }

        if ($submission->get('stripe_checkout_session_id') !== $sessionId) {
            return;
        }

        $submission->set('payment_status', 'expired')->saveQuietly();
    }
}
