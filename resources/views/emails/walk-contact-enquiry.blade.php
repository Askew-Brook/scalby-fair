<!doctype html>
<html lang="en">
    <body style="margin: 0; background: #f7f0df; color: #24251f; font-family: Arial, sans-serif;">
        <div style="margin: 0 auto; max-width: 680px; padding: 32px 20px;">
            <div style="background: #ffffff; border-top: 5px solid #b78d3b; padding: 32px;">
                <p style="margin: 0 0 8px; color: #74332d; font-size: 13px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">Website enquiry</p>
                <h1 style="margin: 0; color: #1d2c20; font-family: Georgia, serif; font-size: 30px;">Scalby Walk enquiry</h1>

                <table role="presentation" style="margin-top: 24px; width: 100%; border-collapse: collapse; line-height: 1.5;">
                    <tr><td style="width: 32%; border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Name</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;">{{ $enquiry['name'] ?? '' }}</td></tr>
                    <tr><td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Email</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;"><a href="mailto:{{ $enquiry['email'] ?? '' }}">{{ $enquiry['email'] ?? '' }}</a></td></tr>
                    @if($enquiry['phone'] ?? null)<tr><td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Phone</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;">{{ $enquiry['phone'] }}</td></tr>@endif
                    @if($enquiry['related_to'] ?? null)<tr><td style="border-top: 1px solid #eadcbd; padding: 10px 8px 10px 0; font-weight: 700;">Related to</td><td style="border-top: 1px solid #eadcbd; padding: 10px 0;">{{ $enquiry['related_to'] }}</td></tr>@endif
                </table>

                <h2 style="margin: 28px 0 10px; color: #1d2c20; font-size: 20px;">Message</h2>
                <p style="margin: 0; white-space: pre-line; line-height: 1.7;">{{ $enquiry['message'] ?? '' }}</p>
            </div>
        </div>
    </body>
</html>
