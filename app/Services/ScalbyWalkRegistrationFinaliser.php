<?php

namespace App\Services;

use App\Mail\ScalbyWalkRegistrationPaid;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Statamic\Facades\Form;
use UnexpectedValueException;

class ScalbyWalkRegistrationFinaliser
{
    /** @param array<string, mixed> $checkoutSession */
    public function finalise(array $checkoutSession): bool
    {
        if (($checkoutSession['payment_status'] ?? null) !== 'paid') {
            return false;
        }

        $registrationId = (string) data_get($checkoutSession, 'metadata.registration_id', $checkoutSession['client_reference_id'] ?? '');

        if ($registrationId === '') {
            throw new UnexpectedValueException('The paid Stripe session has no Walk registration reference.');
        }

        return Cache::lock("walk-registration-payment:{$registrationId}", 15)->block(5, function () use ($registrationId, $checkoutSession): bool {
            $registration = Form::find('walk_registration')?->submission($registrationId);

            if (! $registration) {
                throw new UnexpectedValueException("Scalby Walk registration {$registrationId} could not be found.");
            }

            $sessionId = (string) ($checkoutSession['id'] ?? '');
            $storedSessionId = (string) $registration->get('stripe_checkout_session_id');

            if ($storedSessionId !== '' && ! hash_equals($storedSessionId, $sessionId)) {
                throw new UnexpectedValueException('The Stripe session does not match the Scalby Walk registration.');
            }

            $expectedAmount = (int) $registration->get('total_pence');
            $paidAmount = (int) ($checkoutSession['amount_total'] ?? 0);

            if ($expectedAmount <= 0 || $paidAmount !== $expectedAmount) {
                throw new UnexpectedValueException('The Stripe payment total does not match the Scalby Walk registration total.');
            }

            $registration
                ->set('payment_status', 'paid')
                ->set('stripe_checkout_session_id', $sessionId)
                ->set('stripe_payment_intent_id', (string) ($checkoutSession['payment_intent'] ?? ''))
                ->set('paid_at', now()->toIso8601String())
                ->saveQuietly();

            if ($registration->get('confirmation_sent_at')) {
                return true;
            }

            $registrantEmail = (string) $registration->get('email');
            $recipientEmails = collect($registration->get('recipient_emails', []))
                ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->values();

            if (! filter_var($registrantEmail, FILTER_VALIDATE_EMAIL) || $recipientEmails->isEmpty()) {
                throw new RuntimeException('The Walk confirmation recipients are invalid.');
            }

            Mail::to($registrantEmail)
                ->bcc($recipientEmails->all())
                ->send(new ScalbyWalkRegistrationPaid($registration->data()->all()));

            $registration
                ->set('confirmation_sent_at', now()->toIso8601String())
                ->saveQuietly();

            return true;
        });
    }

    public function markExpired(string $registrationId, string $sessionId): void
    {
        $registration = Form::find('walk_registration')?->submission($registrationId);

        if (! $registration || $registration->get('payment_status') === 'paid') {
            return;
        }

        if ($registration->get('stripe_checkout_session_id') !== $sessionId) {
            return;
        }

        $registration->set('payment_status', 'expired')->saveQuietly();
    }
}
