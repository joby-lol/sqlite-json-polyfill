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

use Joby\SqliteJsonPolyfill\PathLegInterface;
use Joby\SqliteJsonPolyfill\JsonPathValue;

abstract class AbstractLeg implements PathLegInterface
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

    protected static function keyToLeg(string|int $key): PathLegInterface
    {
        if (is_int($key) || intval($key) == $key) {
            return new ArrayIndex(intval($key));
        } else {
            return new Literal($key);
        }
    }
}
