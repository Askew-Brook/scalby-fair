<?php

namespace App\Listeners;

use App\Mail\WalkContactEnquiry;
use App\Support\ScalbyWalkCatalogue;
use Illuminate\Support\Facades\Mail;
use Statamic\Events\FormSubmitted;
use Throwable;

class SendWalkContactEnquiry
{
    public function __construct(private ScalbyWalkCatalogue $catalogue) {}

    public function handle(FormSubmitted $event): void
    {
        $submission = $event->submission;

        if ($submission->form()->handle() !== 'contact' || $submission->get('enquiry_type') !== 'walk') {
            return;
        }

        $recipient = $this->catalogue->recipientEmails()->first();

        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            report(new \RuntimeException('The Walk contact enquiry recipient is not configured.'));

            return;
        }

        try {
            Mail::to($recipient)->send(new WalkContactEnquiry($submission->data()->all()));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
