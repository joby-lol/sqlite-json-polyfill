<?php

namespace Joby\SqliteJsonPolyfill\Legs;

use Joby\SqliteJsonPolyfill\JsonPathLeg;
use Joby\SqliteJsonPolyfill\JsonPathValue;

abstract class AbstractLeg implements JsonPathLeg
{
    /**
     * @param JsonPathValue[] $values
     * @return JsonPathValue[]
     */
    public function values(array &$values): array
    {
        $output_values = [];
        foreach ($values as $result) {
            if (!is_array($result->value())) continue;
            foreach ($this->keys($result->value()) as $key) {
                $path = clone $result->path();
                $path->append(static::keyToLeg($key));
                $output_values[] = new JsonPathValue(
                    $path,
                    $result->value()[$key]
                );
            }
        }
        return $output_values;
    }

    protected static function keyToLeg(string $key): JsonPathLeg
    {
        if (is_numeric($key) && intval($key) == $key) {
            return new ArrayIndex($key);
        } else {
            return new Literal($key);
        }
    }
}
