<?php

use Statamic\Facades\GlobalSet;
use Statamic\Globals\Variables;
use Statamic\Tags\FluentTag;

if (! function_exists('glide')) {
    function glide(string $path, array $parameters = []): FluentTag
    {
        return Statamic\Statamic::tag('glide')->params(array_merge([
            'path' => $path,
            'fit' => 'crop_focal',
        ], $parameters));
    }
}

if (! function_exists('globalSet')) {
    function globalSet(string $handle): ?Variables
    {
        return GlobalSet::findByHandle($handle)?->inCurrentSite();
    }
}
