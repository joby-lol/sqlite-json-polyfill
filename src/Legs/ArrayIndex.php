<?php

namespace Joby\SqliteJsonPolyfill\Legs;

use InvalidArgumentException;
use Joby\SqliteJsonPolyfill\JsonPathLeg;

class ArrayIndex implements JsonPathLeg
{
    public function __construct(protected int $index)
    {
        if ($index < 0) {
            throw new InvalidArgumentException('Array index must be greater than or equal to 0');
        }
    }

    public function index(): int
    {
        return $this->index;
    }

    public function values(array &$values): array
    {
        $new_results = [];
        foreach ($values as &$result) {
            if (is_array($result) && array_key_exists($this->index, $result)) {
                $new_results[] = &$result[$this->index];
            }
        }
        return $new_results;
    }

    public function __toString(): string
    {
        return '[' . $this->index . ']';
    }
}
