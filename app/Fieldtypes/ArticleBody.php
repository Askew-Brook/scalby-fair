<?php

namespace App\Fieldtypes;

use Statamic\Facades\Markdown;
use Statamic\Fieldtypes\Bard;

class ArticleBody extends Bard
{
    protected $component = 'bard';

    protected $indexComponent = 'bard';

    public function preProcess($value)
    {
        return parent::preProcess(is_string($value) ? $this->legacyHtml($value) : $value);
    }

    protected function performAugmentation($value, $shallow)
    {
        return is_string($value)
            ? $this->legacyHtml($this->resolveStatamicUrls($value))
            : parent::performAugmentation($value, $shallow);
    }

    private function legacyHtml(string $markdown): string
    {
        return Markdown::parser('default')->withAutoLineBreaks()->parse($markdown);
    }
}
