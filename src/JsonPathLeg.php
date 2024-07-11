<?php

namespace Joby\SqliteJsonPolyfill;

use Stringable;

interface JsonPathLeg extends Stringable
{
    /**
     * @return JsonPathValue[]
     */
    public function values(array &$values): array;

    /**
     * @return array<string|int>
     */
    public function keys(array $data): array;

    // public function unset(array &$results): void;
}
