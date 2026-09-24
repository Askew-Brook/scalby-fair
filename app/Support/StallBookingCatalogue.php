<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StallBookingCatalogue
{
    public function bookingsAreOpen(): bool
    {
        return filter_var(globalSet('site')?->get('stall_bookings_open', false), FILTER_VALIDATE_BOOL);
    }

    public function year(): int
    {
        return (int) (globalSet('site')?->get('stall_booking_year') ?: now()->year);
    }

    public function recipientEmail(): ?string
    {
        $email = globalSet('site')?->get('stall_booking_recipient_email');

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? (string) $email : null;
    }

    /**
     * @return Collection<int, array{code: string, name: string, description: string, unit_amount: int, available: bool, max_quantity: int}>
     */
    public function items(): Collection
    {
        return collect(globalSet('site')?->get('stall_booking_items', []))
            ->filter(fn ($item) => is_array($item))
            ->map(function (array $item): array {
                $code = Str::slug((string) ($item['code'] ?? $item['name'] ?? ''), '_');

                return [
                    'code' => $code,
                    'name' => trim((string) ($item['name'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                    'unit_amount' => (int) round(((float) ($item['price'] ?? 0)) * 100),
                    'available' => filter_var($item['available'] ?? false, FILTER_VALIDATE_BOOL),
                    'max_quantity' => min(20, max(1, (int) ($item['max_quantity'] ?? 5))),
                ];
            })
            ->filter(fn (array $item) => $item['code'] !== '' && $item['name'] !== '' && $item['unit_amount'] > 0)
            ->unique('code')
            ->values();
    }

    /**
     * @return Collection<string, array{code: string, name: string, description: string, unit_amount: int, available: bool, max_quantity: int}>
     */
    public function availableItems(): Collection
    {
        return $this->items()
            ->where('available', true)
            ->keyBy('code');
    }
}
