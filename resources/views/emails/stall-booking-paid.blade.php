<!doctype html>
<html lang="en">
    <body style="margin: 0; background: #f7f0df; color: #24251f; font-family: Arial, sans-serif;">
        <div style="margin: 0 auto; max-width: 680px; padding: 32px 20px;">
            <div style="background: #ffffff; border-top: 5px solid #b78d3b; padding: 32px;">
                <p style="margin: 0 0 8px; color: #74332d; font-size: 13px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">Payment received</p>
                <h1 style="margin: 0; color: #1d2c20; font-family: Georgia, serif; font-size: 30px;">New Scalby Fair stall booking</h1>
                <p style="margin: 16px 0 0; line-height: 1.6;">A paid {{ $booking['booking_year'] ?? '' }} stall booking has been received. Replying to this email will reply to the stallholder.</p>

                <h2 style="margin: 32px 0 12px; color: #1d2c20; font-size: 20px;">Contact details</h2>
                <table role="presentation" style="width: 100%; border-collapse: collapse; line-height: 1.5;">
                    <tr><td style="width: 38%; border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Name</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;">{{ trim(($booking['first_name'] ?? '').' '.($booking['last_name'] ?? '')) }}</td></tr>
                    <tr><td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Email</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;"><a href="mailto:{{ $booking['email'] ?? '' }}">{{ $booking['email'] ?? '' }}</a></td></tr>
                    <tr><td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Phone</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;">{{ $booking['phone'] ?? '' }}</td></tr>
                    <tr><td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Organiser or business</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;">{{ $booking['business_name'] ?? '' }}</td></tr>
                    <tr><td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Address</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;">{{ $booking['address_line_1'] ?? '' }}<br>@if($booking['address_line_2'] ?? null){{ $booking['address_line_2'] }}<br>@endif{{ $booking['town'] ?? '' }}<br>{{ $booking['postcode'] ?? '' }}</td></tr>
                </table>

                <h2 style="margin: 32px 0 12px; color: #1d2c20; font-size: 20px;">Booking</h2>
                <table role="presentation" style="width: 100%; border-collapse: collapse; line-height: 1.5;">
                    @foreach(($booking['items'] ?? []) as $item)
                        <tr>
                            <td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0;">{{ $item['quantity'] }} × {{ $item['name'] }}</td>
                            <td style="border-top: 1px solid #eadcbd; padding: 10px 0; text-align: right;">£{{ number_format(($item['line_total'] ?? 0) / 100, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr><td style="border-top: 2px solid #354d31; padding: 12px 8px 0 0; font-weight: 700;">Total paid</td><td style="border-top: 2px solid #354d31; padding: 12px 0 0; text-align: right; font-weight: 700;">{{ $booking['total'] ?? '' }}</td></tr>
                </table>

                <h2 style="margin: 32px 0 12px; color: #1d2c20; font-size: 20px;">Stall information</h2>
                <p style="margin: 0 0 6px; font-weight: 700;">Purpose of stall</p>
                <p style="margin: 0 0 18px; white-space: pre-line; line-height: 1.6;">{{ $booking['stall_purpose'] ?? '' }}</p>
                <p style="margin: 0 0 6px; font-weight: 700;">Special requirements</p>
                <p style="margin: 0 0 18px; white-space: pre-line; line-height: 1.6;">{{ $booking['special_requirements'] ?? '' }}</p>
                <p style="margin: 0 0 6px; font-weight: 700;">Certificates held</p>
                <p style="margin: 0; white-space: pre-line; line-height: 1.6;">{{ $booking['certificates'] ?? '' }}</p>

                <p style="margin: 32px 0 0; color: #47633f; font-size: 13px;">Stripe Payment Intent: {{ $booking['stripe_payment_intent_id'] ?? 'Not supplied' }}</p>
            </div>
        </div>
    </body>
</html>
