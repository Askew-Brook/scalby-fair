<?php

namespace Tests\Feature;

use App\Mail\ScalbyWalkRegistrationPaid;
use App\Services\ScalbyWalkRegistrationFinaliser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Statamic\Facades\Form;
use Tests\TestCase;

class ScalbyWalkRegistrationTest extends TestCase
{
    public function test_a_paid_walk_registration_uses_server_prices_and_emails_every_recipient(): void
    {
        config(['services.stripe.secret' => 'sk_test_example']);

        Mail::fake();

        Http::fake([
            'api.stripe.com/v1/checkout/sessions' => Http::response([
                'id' => 'cs_test_walk_registration',
                'url' => 'https://checkout.stripe.com/c/pay/cs_test_walk_registration',
            ]),
        ]);

        $form = Form::find('walk_registration');
        $existingIds = $form->submissions()->map->id();
        $created = collect();

        try {
            $response = $this->post(route('walk-bookings.checkout'), [
                'first_name' => 'Test',
                'last_name' => 'Registrant',
                'email' => 'registrant@example.com',
                'email_confirmation' => 'registrant@example.com',
                'phone' => '01234 567890',
                'address_line_1' => '1 Test Street',
                'address_line_2' => '',
                'town' => 'Scalby',
                'county' => 'North Yorkshire',
                'postcode' => 'YO13 0AA',
                'country' => 'United Kingdom',
                'adult_walkers' => [
                    ['first_name' => 'Adult', 'last_name' => 'One', 'age' => 40, 'gender' => 'F', 'postcode' => 'YO13 0AA'],
                    ['first_name' => 'Adult', 'last_name' => 'Two', 'age' => 41, 'gender' => 'M', 'postcode' => 'YO13 0AA'],
                ],
                'junior_walkers' => [
                    ['first_name' => 'Junior', 'last_name' => 'One', 'age' => 14, 'gender' => 'F', 'postcode' => 'YO13 0AA'],
                    ['first_name' => 'Junior', 'last_name' => 'Two', 'age' => 10, 'gender' => 'M', 'postcode' => 'YO13 0AA'],
                ],
                'dogs' => [
                    ['name' => 'Bertie', 'age' => 4],
                ],
                'donation' => '20.00',
                'walker_details_confirmation' => '1',
                'rules_confirmation' => '1',
                'privacy_consent' => '1',
                'website' => '',
            ]);

            $created = $form->submissions()->reject(fn ($submission) => $existingIds->contains($submission->id()));
            $registration = $created->sole();

            $response->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_walk_registration');
            $this->assertSame(6000, $registration->get('total_pence'));
            $this->assertSame(2, $registration->get('adult_count'));
            $this->assertSame(2, $registration->get('junior_count'));
            $this->assertSame(1, $registration->get('dog_count'));
            $this->assertSame('Adult One|Adult Two', $registration->get('adult_walkers_names'));
            $this->assertSame('Junior One|Junior Two', $registration->get('junior_walkers_names'));
            $this->assertSame('Bertie', $registration->get('dog_names'));
            $this->assertSame('awaiting_payment', $registration->get('payment_status'));

            app(ScalbyWalkRegistrationFinaliser::class)->finalise([
                'id' => 'cs_test_walk_registration',
                'payment_status' => 'paid',
                'amount_total' => 6000,
                'payment_intent' => 'pi_test_walk_registration',
                'metadata' => ['registration_id' => $registration->id()],
            ]);

            $registration = $form->submission($registration->id());

            $this->assertSame('paid', $registration->get('payment_status'));
            Mail::assertSent(ScalbyWalkRegistrationPaid::class, fn (ScalbyWalkRegistrationPaid $mail) => $mail->hasTo('registrant@example.com')
                && $mail->hasBcc('m.whiteley@chaoslab.co.uk')
                && $mail->hasBcc('sheppardnigel@hotmail.com'));
        } finally {
            $created->each->deleteQuietly();
        }
    }
}
