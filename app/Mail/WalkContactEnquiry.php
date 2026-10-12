<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WalkContactEnquiry extends Mailable
{
    use Queueable, SerializesModels;

    /** @param array<string, mixed> $enquiry */
    public function __construct(public array $enquiry) {}

    public function envelope(): Envelope
    {
        $name = trim((string) ($this->enquiry['name'] ?? ''));
        $replyTo = filter_var($this->enquiry['email'] ?? null, FILTER_VALIDATE_EMAIL)
            ? [new Address($this->enquiry['email'], $name)]
            : [];

        return new Envelope(
            replyTo: $replyTo,
            subject: 'Scalby Walk website enquiry'.($name ? ' — '.$name : ''),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.walk-contact-enquiry');
    }
}
