<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScalbyWalkRegistrationPaid extends Mailable
{
    use Queueable, SerializesModels;

    /** @param array<string, mixed> $registration */
    public function __construct(public array $registration) {}

    public function envelope(): Envelope
    {
        $name = trim(($this->registration['first_name'] ?? '').' '.($this->registration['last_name'] ?? ''));
        $replyTo = filter_var($this->registration['email'] ?? null, FILTER_VALIDATE_EMAIL)
            ? [new Address($this->registration['email'], $name)]
            : [];

        return new Envelope(
            replyTo: $replyTo,
            subject: sprintf(
                'Scalby Charity Walk %s registration confirmation — %s',
                $this->registration['booking_year'] ?? now()->year,
                $name ?: 'Registrant'
            ),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.scalby-walk-registration-paid');
    }
}
