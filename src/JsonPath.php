<?php

/**
 * SQLite JSON Polyfill: https://go.joby.lol/sqlitejsonpolyfill
 * MIT License: Copyright (c) 2024 Joby Elliott
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE 
 * SOFTWARE.
 */

namespace Joby\SqliteJsonPolyfill;

use Stringable;

/**
 * Parses a JSON path as described in https://dev.mysql.com/doc/refman/8.0/en/json.html#json-path-syntax
 */
class JsonPath implements Stringable
{
    /** @var PathLegInterface[] */
    protected array $legs = [];
    protected PathRootInterface $root;

    /**
     * Parses a JSON path and constructs a JsonPath object from it.
     */
    public static function parse(string $path): JsonPath
    {
        $parts = array_map(
            JsonPathParser::parseLeg(...),
            JsonPathParser::parsePath($path)
        );
        $root = array_shift($parts);
        static::assertIsRoot($root);
        static::assertIsAllLegs($parts);
        return new JsonPath($root, ...$parts);
    }

    /**
     * @phpstan-assert PathRootInterface $root
     */
    protected static function assertIsRoot(mixed $root): void
    {
        assert(
            $root instanceof PathRootInterface,
            'The first part of a JSON path must be a PathRootInterface object'
        );
    }

    /**
     * @phpstan-assert array<PathLegInterface> $parts
     * @param array<mixed> $parts
     */
    protected static function assertIsAllLegs(array $parts): void
    {
        assert(
            count(array_filter($parts, (fn($part) => $part instanceof PathLegInterface))) == 0,
            'All parts of a JSON path must be PathLegInterface objects'
        );
    }

    /**
     * The constructor is left public just for the rare cases when it's useful
     * to be able to create a JsonPath object without parsing a path, from raw
     * legs. Generally this library will be constructing them by parsing
     * strings.
     */
    public function __construct(PathRootInterface $root, PathLegInterface ...$legs)
    {
        $this->root = $root;
        $this->legs = $legs;
    }

    /**
     * Return all the legs of this path.
     * @return PathLegInterface[]
     */
    public function legs(): array
    {
        return $this->legs;
    }

    /**
     * Append a leg to the end of this path.
     */
    public function append(PathLegInterface ...$legs): void
    {
        foreach ($legs as $leg) {
            $this->legs[] = $leg;
        }
    }

    /**
     * Return a string representation of this path.
     */
    public function __toString(): string
    {
        return $this->root . implode('', $this->legs);
    }
}
