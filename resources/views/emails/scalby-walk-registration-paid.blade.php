<!doctype html>
<html lang="en">
    <body style="margin: 0; background: #f7f0df; color: #24251f; font-family: Arial, sans-serif;">
        <div style="margin: 0 auto; max-width: 680px; padding: 32px 20px;">
            <div style="background: #ffffff; border-top: 5px solid #b78d3b; padding: 32px;">
                <p style="margin: 0 0 8px; color: #74332d; font-size: 13px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">Payment received</p>
                <h1 style="margin: 0; color: #1d2c20; font-family: Georgia, serif; font-size: 30px;">Scalby Charity Walk registration confirmed</h1>
                <p style="margin: 16px 0 0; line-height: 1.6;">This confirms the paid {{ $registration['booking_year'] ?? '' }} registration below. The registrant and Walk organisers have received the same booking details.</p>

                <h2 style="margin: 32px 0 12px; color: #1d2c20; font-size: 20px;">Registrant</h2>
                <table role="presentation" style="width: 100%; border-collapse: collapse; line-height: 1.5;">
                    <tr><td style="width: 38%; border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Name</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;">{{ trim(($registration['first_name'] ?? '').' '.($registration['last_name'] ?? '')) }}</td></tr>
                    <tr><td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Email</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;"><a href="mailto:{{ $registration['email'] ?? '' }}">{{ $registration['email'] ?? '' }}</a></td></tr>
                    <tr><td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Telephone</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;">{{ $registration['phone'] ?? '' }}</td></tr>
                    <tr><td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Address</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;">{{ $registration['address_line_1'] ?? '' }}<br>@if($registration['address_line_2'] ?? null){{ $registration['address_line_2'] }}<br>@endif{{ $registration['town'] ?? '' }}<br>{{ $registration['postcode'] ?? '' }}<br>{{ $registration['country'] ?? '' }}</td></tr>
                </table>

                @foreach(['Adult' => ($registration['adult_walkers'] ?? []), 'Junior' => ($registration['junior_walkers'] ?? [])] as $category => $walkers)
                    @if(count($walkers))
                        <h2 style="margin: 32px 0 12px; color: #1d2c20; font-size: 20px;">{{ $category }} walkers</h2>
                        <table role="presentation" style="width: 100%; border-collapse: collapse; line-height: 1.5;">
                            <tr><th style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; text-align: left;">Name</th><th style="border-top: 1px solid #eadcbd; padding: 10px 8px; text-align: left;">Age</th><th style="border-top: 1px solid #eadcbd; padding: 10px 8px; text-align: left;">Gender</th><th style="border-top: 1px solid #eadcbd; padding: 10px 0 10px 8px; text-align: left;">Postcode</th></tr>
                            @foreach($walkers as $walker)
                                <tr><td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0;">{{ $walker['name'] ?? '' }}</td><td style="border-top: 1px solid #eadcbd; padding: 10px 8px;">{{ $walker['age'] ?? '' }}</td><td style="border-top: 1px solid #eadcbd; padding: 10px 8px;">{{ $walker['gender'] ?? '' }}</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0 10px 8px;">{{ $walker['postcode'] ?? '' }}</td></tr>
                            @endforeach
                        </table>
                    @endif
                @endforeach

                <h2 style="margin: 32px 0 12px; color: #1d2c20; font-size: 20px;">Payment</h2>
                <table role="presentation" style="width: 100%; border-collapse: collapse; line-height: 1.5;">
                    @foreach(($registration['line_items'] ?? []) as $item)
                        <tr><td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0;">{{ $item['quantity'] }} × {{ $item['name'] }}</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0; text-align: right;">£{{ number_format(($item['line_total'] ?? 0) / 100, 2) }}</td></tr>
                    @endforeach
                    <tr><td style="border-top: 2px solid #354d31; padding: 12px 8px 0 0; font-weight: 700;">Total paid</td><td style="border-top: 2px solid #354d31; padding: 12px 0 0; text-align: right; font-weight: 700;">{{ $registration['total'] ?? '' }}</td></tr>
                </table>

                <p style="margin: 32px 0 0; color: #47633f; font-size: 13px;">Stripe Payment Intent: {{ $registration['stripe_payment_intent_id'] ?? 'Not supplied' }}</p>
            </div>
        </div>
    </body>
</html>
