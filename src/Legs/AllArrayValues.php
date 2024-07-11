<?php

namespace Joby\SqliteJsonPolyfill\Legs;

class AllArrayValues extends AbstractLeg
{
    protected function keys(array $value): array
    {
        return array_keys($value);
    }

    public function __toString(): string
    {
        return '[*]';
    }
}
