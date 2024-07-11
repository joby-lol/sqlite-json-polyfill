<?php

namespace Joby\SqliteJsonPolyfill\Legs;

use Joby\SqliteJsonPolyfill\JsonPathLeg;

class Literal extends AbstractLeg
{
    public function __construct(protected string $literal)
    {
        if (strlen($literal) === 0) {
            throw new \InvalidArgumentException('Literal leg must not be empty');
        }
    }

    public function literal(): string
    {
        return $this->literal;
    }

    protected function keys(array $value): array
    {
        return array_key_exists($this->literal, $value) ? [$this->literal] : [];
    }

    public function __toString(): string
    {
        $quoted = json_encode($this->literal);
        if (str_contains($quoted, '\\')) {
            return '.' . $quoted;
        } else {
            return '.' . $this->literal;
        }
    }
}
