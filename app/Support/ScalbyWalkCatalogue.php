<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ScalbyWalkCatalogue
{
    public function registrationsAreOpen(): bool
    {
        return filter_var(globalSet('site')?->get('walk_registrations_open', false), FILTER_VALIDATE_BOOL);
    }

    public function year(): int
    {
        return (int) (globalSet('site')?->get('walk_booking_year') ?: now()->year);
    }

    public function eventDate(): Carbon
    {
        $configuredDate = globalSet('site')?->get('walk_event_date');

        return Carbon::parse($configuredDate ?: "{$this->year()}-01-01");
    }

    public function eventDateLabel(): string
    {
        return $this->eventDate()->format('j F Y');
    }

    public function rulesDocument(): ?string
    {
        return $this->assetPath('walk_rules_document');
    }

    public function mapDocument(): ?string
    {
        return $this->assetPath('walk_map_document');
    }

    public function sponsorshipDocument(): ?string
    {
        return $this->assetPath('walk_sponsorship_document');
    }

    public function adultBookingsAreAvailable(): bool
    {
        return filter_var(globalSet('site')?->get('walk_adult_bookings_available', false), FILTER_VALIDATE_BOOL);
    }

    public function juniorBookingsAreAvailable(): bool
    {
        return filter_var(globalSet('site')?->get('walk_junior_bookings_available', false), FILTER_VALIDATE_BOOL);
    }

    public function donationsAreEnabled(): bool
    {
        return filter_var(globalSet('site')?->get('walk_donations_enabled', true), FILTER_VALIDATE_BOOL);
    }

    public function adultPrice(): int
    {
        return $this->priceInPence('walk_adult_price');
    }

    public function juniorPrice(): int
    {
        return $this->priceInPence('walk_junior_price');
    }

    /** @return Collection<int, string> */
    public function recipientEmails(): Collection
    {
        return collect(globalSet('site')?->get('walk_booking_recipients', []))
            ->filter(fn ($recipient) => is_array($recipient))
            ->pluck('email')
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn (string $email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();
    }

    private function priceInPence(string $handle): int
    {
        return max(0, (int) round(((float) globalSet('site')?->get($handle, 0)) * 100));
    }

    private function assetPath(string $handle): ?string
    {
        $value = globalSet('site')?->get($handle);
        $path = is_array($value) ? ($value[0] ?? null) : $value;

        return filled($path) ? (string) $path : null;
    }
}
