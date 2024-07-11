<?php

/**
 * SQLite JSON Polyfill: https://code.byjoby.com/sqlite-json-polyfill/
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

namespace Joby\SqliteJsonPolyfill\Legs;

use Joby\SqliteJsonPolyfill\JsonPath;
use Joby\SqliteJsonPolyfill\JsonPathValue;
use Joby\SqliteJsonPolyfill\PathRootInterface;

/**
 * Root behaves somewhat differently from other Legs. Other legs are passed an
 * array of every result that was returned by the previous leg. Root is passed
 * the entire starting document, and it just wraps it in an array. This prepares
 * it to be searched by later legs.
 */
class Root implements PathRootInterface
{
    public function keys(array $data): array
    {
        return array_keys($data);
    }

    public function values(array &$document): array
    {
        return [
            new JsonPathValue(new JsonPath($this), $document)
        ];
    }

    public function __toString(): string
    {
        return '$';
    }
}
