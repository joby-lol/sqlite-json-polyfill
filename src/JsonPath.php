<?php

namespace Joby\SqliteJsonPolyfill;

use Stringable;

/**
 * Parses a JSON path as described in https://dev.mysql.com/doc/refman/8.0/en/json.html#json-path-syntax
 */
class JsonPath implements Stringable
{
    /** @var JsonPathLeg[] */
    protected array $legs = [];

    /**
     * Parses a JSON path and constructs a JsonPath object from it.
     */
    public static function parse(string $path): JsonPath
    {
        return new JsonPath(...array_map(
            static::parseLeg(...),
            static::parsePath($path)
        ));
    }

    /**
     * The constructor is left public just in case it's useful to be able to
     * create a JsonPath object without parsing a path, from raw legs. Generally
     * this library will be constructing them by parsing strings.
     */
    public function __construct(JsonPathLeg ...$legs)
    {
        $this->legs = $legs;
    }

    /**
     * Return all the legs of this path.
     * @return JsonPathLeg[]
     */
    public function legs(): array
    {
        return $this->legs;
    }

    /**
     * Append a leg to the end of this path.
     */
    public function append(JsonPathLeg $leg): void
    {
        $this->legs[] = $leg;
    }

    public function __toString(): string
    {
        return implode('', $this->legs);
    }
}
