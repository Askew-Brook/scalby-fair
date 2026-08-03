<?php

use Illuminate\Support\Facades\Route;
use Statamic\Facades\Entry;

Route::get('sitemap.xml', function () {
    $entries = Entry::query()
        ->whereIn('collection', ['pages', 'events', 'news'])
        ->whereStatus('published')
        ->get()
        ->filter(fn ($entry) => $entry->url());

    return response()
        ->view('sitemap', compact('entries'))
        ->header('Content-Type', 'application/xml');
});
