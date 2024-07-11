<?php

namespace Joby\SqliteJsonPolyfill\Legs;

use InvalidArgumentException;
use Joby\SqliteJsonPolyfill\JsonPathLeg;

class ArrayIndexRange implements JsonPathLeg
{
    public function __construct(
        protected int $start,
        protected int $end
    ) {
        if ($start < 0) {
            throw new InvalidArgumentException('Array start must be greater than or equal to 0');
        }
        if ($end < 0) {
            throw new InvalidArgumentException('Array end must be greater than or equal to 0');
        }
        if ($start > $end) {
            throw new InvalidArgumentException('Array start must be less than or equal to end');
        }
    }

    public function start(): int
    {
        return $this->start;
    }

    public function end(): int
    {
        return $this->end;
    }

    public function values(array &$values): array
    {
        $new_results = [];
        foreach ($values as &$result) {
            if (is_array($result) && array_key_exists($this->start, $result)) {
                $new_results[] = &$result[$this->start];
            }
        }
        return $new_results;
    }

    public function __toString(): string
    {
        return '[' . $this->start . ' to ' . $this->end . ']';
    }
}
