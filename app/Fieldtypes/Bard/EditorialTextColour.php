<?php

namespace App\Fieldtypes\Bard;

use Tiptap\Core\Mark;

class EditorialTextColour extends Mark
{
    public static $name = 'editorialTextColour';

    private const COLOURS = ['red', 'blue', 'green', 'gold'];

    public function addAttributes()
    {
        return ['colour' => ['default' => null, 'rendered' => false]];
    }

    public function parseHTML()
    {
        return [[
            'tag' => 'span[data-editorial-colour]',
            'getAttrs' => static function ($node) {
                $colour = $node->getAttribute('data-editorial-colour');

                return in_array($colour, self::COLOURS, true) ? ['colour' => $colour] : false;
            },
        ]];
    }

    public function renderHTML($mark, $HTMLAttributes = [])
    {
        $colour = $mark->attrs->colour ?? null;

        return in_array($colour, self::COLOURS, true)
            ? ['span', ['data-editorial-colour' => $colour], 0]
            : ['span', 0];
    }
}
