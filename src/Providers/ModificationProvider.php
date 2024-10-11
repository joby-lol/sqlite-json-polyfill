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

namespace Joby\SqliteJsonPolyfill\Providers;

use InvalidArgumentException;

/**
 * @ref https://dev.mysql.com/doc/refman/8.0/en/json-modification-functions.html
 */
class ModificationProvider extends AbstractProvider
{
    /**
     * Appends values to the end of the given JSON document and returns the result.
     * $json_doc should be followed by any number of path/value pairs of arguments,
     * and an error will occur if there are not an even number of arguments.
     */
    public function JSON_ARRAY_APPEND(string $json_doc, string ...$args): string
    {
        if (count($args) % 2 !== 0) {
            throw new InvalidArgumentException('JSON_ARRAY_APPEND requires an even number of arguments after $json_doc');
        }
        $doc = json_decode($json_doc);
        if (!is_array($doc)) {
            throw new InvalidArgumentException('JSON_ARRAY_APPEND requires a valid JSON array as the first argument');
        }
        for ($i = 0; $i < count($args); $i += 2) {
            $key = $args[$i];
            $value = $args[$i + 1];
            if (array_key_exists($key, $doc)) {
                unset($doc[$key]);
            }
            $doc[$key] = $value;
        }
        // @phpstan-ignore-next-line - failing a type check here is good
        return json_encode($doc);
    }

    /**
     * Unquotes JSON value and returns the result as a string. Returns null if the argument is "NULL". An error occurs if the value starts and ends with double quotes but is not a valid JSON string literal. 
     */
    public static function JSON_UNQUOTE(string $value): string|null
    {
        if ($value == 'NULL') {
            return null;
        }
        // @phpstan-ignore-next-line - failing a type check here is good
        return json_decode($value);
    }
}
