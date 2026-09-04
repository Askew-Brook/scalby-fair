<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use UnexpectedValueException;

class StripeCheckoutClient
{
    /**
     * @param  array<int, array{code: string, name: string, description: string, unit_amount: int, quantity: int, line_total: int}>  $items
     * @return array<string, mixed>
     */
    public function createCheckoutSession(string $bookingId, int $year, string $email, string $businessName, array $items): array
    {
        $response = $this->request()
            ->withHeaders(['Idempotency-Key' => "stall-booking-{$bookingId}"])
            ->post('https://api.stripe.com/v1/checkout/sessions', [
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'customer_email' => $email,
                'client_reference_id' => $bookingId,
                'success_url' => route('stall-bookings.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => url('/stall-bookings').'?payment=cancelled#stall-booking-form',
                'metadata' => [
                    'booking_id' => $bookingId,
                    'booking_type' => 'stall_booking',
                    'booking_year' => (string) $year,
                    'business_name' => $businessName,
                ],
                'line_items' => collect($items)->map(fn (array $item) => [
                    'quantity' => $item['quantity'],
                    'price_data' => [
                        'currency' => 'gbp',
                        'unit_amount' => $item['unit_amount'],
                        'product_data' => array_filter([
                            'name' => $item['name'],
                            'description' => $item['description'] ?: null,
                            'metadata' => ['booking_item' => $item['code']],
                        ]),
                    ],
                ])->values()->all(),
            ])
            ->throw()
            ->json();

        if (! is_array($response) || empty($response['id']) || empty($response['url'])) {
            throw new RuntimeException('Stripe did not return a valid Checkout Session.');
        }

        return $response;
    }

    /**
     * @param  array<int, array{code: string, name: string, description: string, unit_amount: int, quantity: int, line_total: int}>  $items
     * @return array<string, mixed>
     */
    public function createWalkCheckoutSession(string $registrationId, int $year, string $email, string $registrantName, array $items): array
    {
        $response = $this->request()
            ->withHeaders(['Idempotency-Key' => "walk-registration-{$registrationId}"])
            ->post('https://api.stripe.com/v1/checkout/sessions', [
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'customer_email' => $email,
                'client_reference_id' => $registrationId,
                'success_url' => route('walk-bookings.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => url('/walk-bookings').'?payment=cancelled#walk-booking-form',
                'metadata' => [
                    'registration_id' => $registrationId,
                    'booking_type' => 'scalby_walk',
                    'booking_year' => (string) $year,
                    'registrant_name' => $registrantName,
                ],
                'line_items' => collect($items)->map(fn (array $item) => [
                    'quantity' => $item['quantity'],
                    'price_data' => [
                        'currency' => 'gbp',
                        'unit_amount' => $item['unit_amount'],
                        'product_data' => array_filter([
                            'name' => $item['name'],
                            'description' => $item['description'] ?: null,
                            'metadata' => ['booking_item' => $item['code']],
                        ]),
                    ],
                ])->values()->all(),
            ])
            ->throw()
            ->json();

        if (! is_array($response) || empty($response['id']) || empty($response['url'])) {
            throw new RuntimeException('Stripe did not return a valid Checkout Session.');
        }

        return $response;
    }

    /** @return array<string, mixed> */
    public function retrieveCheckoutSession(string $sessionId): array
    {
        $response = $this->request()
            ->get('https://api.stripe.com/v1/checkout/sessions/'.rawurlencode($sessionId))
            ->throw()
            ->json();

        if (! is_array($response) || empty($response['id'])) {
            throw new RuntimeException('Stripe did not return a valid Checkout Session.');
        }

        return $response;
    }

    /** @return array<string, mixed> */
    public function parseWebhook(string $payload, ?string $signatureHeader): array
    {
        $secret = (string) config('services.stripe.webhook_secret');

        if ($secret === '') {
            throw new RuntimeException('The Stripe webhook secret is not configured.');
        }

        $signatures = collect(explode(',', (string) $signatureHeader))
            ->mapWithKeys(function (string $part): array {
                [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);

                return $key && $value ? [$key => $value] : [];
            });

        $timestamp = (int) $signatures->get('t');
        $signature = (string) $signatures->get('v1');

        if ($timestamp <= 0 || $signature === '' || abs(time() - $timestamp) > 300) {
            throw new UnexpectedValueException('The Stripe webhook signature is missing or expired.');
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        if (! hash_equals($expected, $signature)) {
            throw new UnexpectedValueException('The Stripe webhook signature is invalid.');
        }

        $event = json_decode($payload, true, flags: JSON_THROW_ON_ERROR);

        if (! is_array($event)) {
            throw new UnexpectedValueException('The Stripe webhook payload is invalid.');
        }

        return $event;
    }

    private function request(): PendingRequest
    {
        $secret = (string) config('services.stripe.secret');

        if ($secret === '') {
            throw new RuntimeException('The Stripe secret key is not configured.');
        }

        return Http::asForm()
            ->acceptJson()
            ->withBasicAuth($secret, '')
            ->timeout(15)
            ->retry(2, 250);
    }
}
