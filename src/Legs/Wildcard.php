<?php

namespace Joby\SqliteJsonPolyfill\Legs;

use Joby\SqliteJsonPolyfill\JsonPathLeg;

class Wildcard implements JsonPathLeg
{
    public function __construct(
        protected string $suffix,
        protected string|null $prefix = null
    ) {
    }

    public function values(array &$values): array
    {
        $new_results = [];
        foreach ($values as &$result) {
            if (is_array($result)) {
                foreach ($result as $key => &$value) {
                    if (!str_ends_with($key, $this->suffix)) continue;
                    if (!is_null($this->prefix) && !str_starts_with($key, $this->prefix)) continue;
                    $new_results[] = $value;
                }
            }
        }
        return $new_results;
    }

    public function prefix(): string|null
    {
        return $this->prefix;
    }

    public function suffix(): string
    {
        return $this->suffix;
    }

    public function __toString(): string
    {
        return '.'.$this->prefix . '**' . $this->suffix;
    }
}
