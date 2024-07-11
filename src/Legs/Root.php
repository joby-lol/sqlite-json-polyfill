<?php

namespace Joby\SqliteJsonPolyfill\Legs;

use Joby\SqliteJsonPolyfill\JsonPath;
use Joby\SqliteJsonPolyfill\JsonPathLeg;
use Joby\SqliteJsonPolyfill\JsonPathValue;

/**
 * Root behaves somewhat differently from other Legs. Other legs are passed an
 * array of every result that was returned by the previous leg. Root is passed
 * the entire starting document, and it just wraps it in an array. This prepares
 * it to be searched by later legs.
 */
class Root implements JsonPathLeg
{
    public function values(array &$document): array
    {
        return [
            new JsonPathValue(new JsonPath($this), $document)
        ];
    }

    public function __toString(): string
    {
        return '$';
    }
}
