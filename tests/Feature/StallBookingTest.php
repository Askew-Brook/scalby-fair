<?php

namespace Tests\Feature;

use App\Mail\StallBookingPaid;
use App\Services\StallBookingPaymentFinaliser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Statamic\Facades\Form;
use Tests\TestCase;

class StallBookingTest extends TestCase
{
    public function test_a_paid_stall_booking_uses_server_prices_and_emails_the_organiser(): void
    {
        config(['services.stripe.secret' => 'sk_test_example']);

        Mail::fake();

        Http::fake([
            'api.stripe.com/v1/checkout/sessions' => Http::response([
                'id' => 'cs_test_stall_booking',
                'url' => 'https://checkout.stripe.com/c/pay/cs_test_stall_booking',
            ]),
        ]);

        $form = Form::find('stall_booking');
        $existingIds = $form->submissions()->map->id();
        $created = collect();

        try {
            $response = $this->post(route('stall-bookings.checkout'), [
                'first_name' => 'Test',
                'last_name' => 'Stallholder',
                'email' => 'stallholder@example.com',
                'email_confirmation' => 'stallholder@example.com',
                'phone' => '01234 567890',
                'business_name' => 'Test Crafts',
                'address_line_1' => '1 High Street',
                'address_line_2' => '',
                'town' => 'Scalby',
                'postcode' => 'YO13 0AA',
                'stall_purpose' => 'Handmade craft items.',
                'special_requirements' => 'None',
                'certificates' => 'None',
                'items' => [
                    'single_stall' => 2,
                    'electric_hookup' => 1,
                ],
                'acceptance' => '1',
                'privacy_consent' => '1',
                'website' => '',
            ]);

            $created = $form->submissions()->reject(fn ($submission) => $existingIds->contains($submission->id()));
            $submission = $created->sole();

            $response->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_stall_booking');
            $this->assertSame(4700, $submission->get('total_pence'));
            $this->assertSame('awaiting_payment', $submission->get('payment_status'));

            Http::assertSent(fn ($request) => $request->url() === 'https://api.stripe.com/v1/checkout/sessions');

            app(StallBookingPaymentFinaliser::class)->finalise([
                'id' => 'cs_test_stall_booking',
                'payment_status' => 'paid',
                'amount_total' => 4700,
                'payment_intent' => 'pi_test_stall_booking',
                'metadata' => ['booking_id' => $submission->id()],
            ]);

            $submission = $form->submission($submission->id());

            $this->assertSame('paid', $submission->get('payment_status'));
            Mail::assertSent(StallBookingPaid::class, fn (StallBookingPaid $mail) => $mail->hasTo('thomas.owen@live.co.uk'));
        } finally {
            $created->each->deleteQuietly();
        }
    }
}
