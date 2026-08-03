<?php

use Statamic\Facades\GlobalSet;
use Statamic\Globals\Variables;
use Statamic\Tags\FluentTag;

if (! function_exists('glide')) {
    function glide(mixed $asset, array $parameters = []): FluentTag
    {
        return Statamic\Statamic::tag('glide')->params(array_merge([
            'src' => $asset,
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
