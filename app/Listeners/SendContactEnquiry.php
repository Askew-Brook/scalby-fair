<?php

namespace App\Listeners;

use App\Mail\ContactEnquiry;
use App\Support\ScalbyWalkCatalogue;
use App\Support\StallBookingCatalogue;
use Illuminate\Support\Facades\Mail;
use Statamic\Events\FormSubmitted;
use Throwable;

class SendContactEnquiry
{
    public function __construct(
        private StallBookingCatalogue $stallCatalogue,
        private ScalbyWalkCatalogue $walkCatalogue,
    ) {}

    public function handle(FormSubmitted $event): void
    {
        $submission = $event->submission;

        if ($submission->form()->handle() !== 'contact') {
            return;
        }

        $enquiryType = (string) $submission->get('enquiry_type');
        $recipient = match ($enquiryType) {
            'stalls' => $this->stallCatalogue->recipientEmail(),
            'walk' => $this->walkCatalogue->recipientEmails()->first(),
            default => globalSet('site')?->get('contact_form_recipient_email'),
        };

        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            report(new \RuntimeException("The contact enquiry recipient for [{$enquiryType}] is not configured."));

            return;
        }

        try {
            Mail::to($recipient)->send(new ContactEnquiry($submission->data()->all()));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
