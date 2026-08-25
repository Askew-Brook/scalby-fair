<?php

use App\Http\Controllers\PublicAssetController;
use Illuminate\Support\Facades\Route;
use Statamic\Facades\Entry;

Route::get('assets/{path}', PublicAssetController::class)
    ->where('path', '.*')
    ->name('assets.show');

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
