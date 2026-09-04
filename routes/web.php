<?php

use App\Http\Controllers\ScalbyWalkRegistrationController;
use App\Http\Controllers\StallBookingController;
use Illuminate\Support\Facades\Route;
use Statamic\Facades\Entry;

Route::post('stall-bookings/checkout', [StallBookingController::class, 'checkout'])
    ->middleware('throttle:10,1')
    ->name('stall-bookings.checkout');

Route::get('stall-bookings/success', [StallBookingController::class, 'success'])
    ->name('stall-bookings.success');

Route::post('stripe/webhooks/stall-bookings', [StallBookingController::class, 'webhook'])
    ->name('stall-bookings.webhook');

Route::post('walk-bookings/checkout', [ScalbyWalkRegistrationController::class, 'checkout'])
    ->middleware('throttle:10,1')
    ->name('walk-bookings.checkout');

Route::get('walk-bookings/success', [ScalbyWalkRegistrationController::class, 'success'])
    ->name('walk-bookings.success');

Route::post('stripe/webhooks/walk-bookings', [ScalbyWalkRegistrationController::class, 'webhook'])
    ->name('walk-bookings.webhook');

Route::get('sitemap.xml', function () {
    $entries = Entry::query()
        ->whereIn('collection', ['pages', 'events', 'news', 'photography_competitions'])
        ->whereStatus('published')
        ->get()
        ->filter(fn ($entry) => $entry->url());

    return response()
        ->view('sitemap', compact('entries'))
        ->header('Content-Type', 'application/xml');
});
