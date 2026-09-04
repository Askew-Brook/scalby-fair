<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StallBookingPaid extends Mailable
{
    use Queueable, SerializesModels;

    /** @param array<string, mixed> $booking */
    public function __construct(public array $booking) {}

    public function envelope(): Envelope
    {
        $replyTo = filter_var($this->booking['email'] ?? null, FILTER_VALIDATE_EMAIL)
            ? [new Address($this->booking['email'], trim(($this->booking['first_name'] ?? '').' '.($this->booking['last_name'] ?? '')))]
            : [];

        return new Envelope(
            replyTo: $replyTo,
            subject: sprintf(
                'Paid Scalby Fair %s stall booking — %s',
                $this->booking['booking_year'] ?? now()->year,
                $this->booking['business_name'] ?? 'Stallholder'
            ),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.stall-booking-paid');
    }
}
