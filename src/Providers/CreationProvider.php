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

namespace Joby\SqliteJsonPolyfill\Providers;

use InvalidArgumentException;

/**
 * @ref https://dev.mysql.com/doc/refman/8.0/en/json-creation-functions.html#function_json-array
 */
class CreationProvider extends AbstractProvider
{
    /**
     * Evaluates a (possibly empty) list of values and returns a JSON array
     * containing those values. 
     */
    public static function JSON_ARRAY(mixed ...$args): string
    {
        // @phpstan-ignore-next-line - failing a type check here is good
        return json_encode($args);
    }

    /**
     * Evaluates a (possibly empty) list of key-value pairs and returns a JSON
     * object containing those pairs. An error occurs if any key name is null or
     * the number of arguments is odd.
     * 
     * Arguments must alternate between string keys and mixed values.
     */
    public static function JSON_OBJECT(mixed ...$args): string
    {
        if (count($args) === 0) {
            return '{}';
        }
        if (count($args) % 2 !== 0) {
            throw new InvalidArgumentException('JSON_OBJECT requires an even number of arguments');
        }
        $result = [];
        for ($i = 0; $i < count($args); $i += 2) {
            if (empty($args[$i])) {
                throw new InvalidArgumentException('JSON_OBJECT requires non-empty key names');
            }
            if (!is_string($args[$i]) && !is_numeric($args[$i])) {
                throw new InvalidArgumentException('JSON_OBJECT requires string key names');
            }
            $result[$args[$i]] = $args[$i + 1];
        }
        // @phpstan-ignore-next-line - failing a type check here is good
        return json_encode($result);
    }

    /**
     * Quotes a string as a JSON value by wrapping it with double quote
     * characters and escaping interior quote and other characters, then
     * returning the result as a utf8mb4 string. Returns "NULL" if the argument
     * is NULL.
     *
     * This function is typically used to produce a valid JSON string literal
     * for inclusion within a JSON document. 
     */
    public static function JSON_QUOTE(string|null $value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }
        // @phpstan-ignore-next-line - failing a type check here is good
        return json_encode($value);
    }
}
