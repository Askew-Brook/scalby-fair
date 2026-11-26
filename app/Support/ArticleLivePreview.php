<?php

namespace App\Support;

use Illuminate\Support\Str;
use Statamic\Contracts\Entries\Entry;
use Statamic\Contracts\Tokens\Token;
use Statamic\CP\LivePreview;

class ArticleLivePreview extends LivePreview
{
    public function tokenize($token, $item): Token
    {
        // Statamic's entry repository requires an ID to substitute an unsaved article.
        if ($item instanceof Entry && $item->collectionHandle() === 'news' && $item->id() === null) {
            $item->id((string) Str::uuid());
        }

        return parent::tokenize($token, $item);
    }
}
