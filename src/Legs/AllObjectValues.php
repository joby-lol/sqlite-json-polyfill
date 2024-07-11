<?php

namespace Joby\SqliteJsonPolyfill\Legs;

use Joby\SqliteJsonPolyfill\JsonPathLeg;

class AllObjectValues implements JsonPathLeg
{
    public function values(array &$values): array
    {
        $new_results = [];
        foreach ($values as &$result) {
            if (is_array($result)) {
                foreach ($result as &$value) {
                    $new_results[] = $value;
                }
            }
        }
        return $new_results;
    }

    public function __toString(): string
    {
        return '.*';
    }
}
