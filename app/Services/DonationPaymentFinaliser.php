<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Statamic\Facades\Form;
use UnexpectedValueException;

class DonationPaymentFinaliser
{
    /** @param array<string, mixed> $checkoutSession */
    public function finalise(array $checkoutSession): bool
    {
        if (($checkoutSession['payment_status'] ?? null) !== 'paid') {
            return false;
        }

        $donationId = (string) data_get($checkoutSession, 'metadata.donation_id', $checkoutSession['client_reference_id'] ?? '');

        if ($donationId === '') {
            throw new UnexpectedValueException('The paid Stripe session has no donation reference.');
        }

        return Cache::lock("donation-payment:{$donationId}", 15)->block(5, function () use ($donationId, $checkoutSession): bool {
            $donation = Form::find('donation')?->submission($donationId);

            if (! $donation) {
                throw new UnexpectedValueException("Donation {$donationId} could not be found.");
            }

            $sessionId = (string) ($checkoutSession['id'] ?? '');
            $storedSessionId = (string) $donation->get('stripe_checkout_session_id');

            if ($storedSessionId !== '' && ! hash_equals($storedSessionId, $sessionId)) {
                throw new UnexpectedValueException('The Stripe session does not match the donation.');
            }

            $expectedAmount = (int) $donation->get('amount_pence');
            $paidAmount = (int) ($checkoutSession['amount_total'] ?? 0);

            if ($expectedAmount <= 0 || $paidAmount !== $expectedAmount) {
                throw new UnexpectedValueException('The Stripe payment total does not match the donation amount.');
            }

            $donation
                ->set('payment_status', 'paid')
                ->set('stripe_checkout_session_id', $sessionId)
                ->set('stripe_payment_intent_id', (string) ($checkoutSession['payment_intent'] ?? ''))
                ->set('paid_at', now()->toIso8601String())
                ->saveQuietly();

            return true;
        });
    }

    public function markExpired(string $donationId, string $sessionId): void
    {
        $donation = Form::find('donation')?->submission($donationId);

        if (! $donation || $donation->get('payment_status') === 'paid') {
            return;
        }

        if ($donation->get('stripe_checkout_session_id') !== $sessionId) {
            return;
        }

        $donation->set('payment_status', 'expired')->saveQuietly();
    }
}
