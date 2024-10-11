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

use InvalidArgumentException;
use Joby\SqliteJsonPolyfill\Legs\AllArrayValues;
use Joby\SqliteJsonPolyfill\Legs\AllObjectValues;
use Joby\SqliteJsonPolyfill\Legs\ArrayIndex;
use Joby\SqliteJsonPolyfill\Legs\ArrayIndexRange;
use Joby\SqliteJsonPolyfill\Legs\Literal;
use Joby\SqliteJsonPolyfill\Legs\Root;
use Joby\SqliteJsonPolyfill\Legs\Wildcard;

/**
 * This is an intentionally dead-simple state machine that parses JSON paths
 * without any unnecessary dependencies, regular expressions, or dependencies.
 * It is designed to be as simple as possible, while still being able to parse
 * valid JSON paths.
 * 
 * @see https://dev.mysql.com/doc/refman/8.0/en/json.html#json-path-syntax
 */
class JsonPathParser
{
    /**
     * Parses a JSON path into an array of legs. Any legs that were quoted
     * literals are returned as Literal leg objects, and the rest are returned
     * as strings. This is necessary because quoted literals will always be
     * treated just as strings, while others might contain more specific syntax.
     * 
     * @return array<Legs\Literal|string>
     */
    public static function parsePath(string $path): array
    {
        $legs = [];
        /**
         * @var string|null
         */
        $leg = null;
        /**
         * Used to indicate if we're in a literal. If this is true, we're in a
         * quoted literal.
         * @var bool
         */
        $quoted_literal = false;
        /**
         * Used to indicate if we're in an escape sequence.
         * @var bool
         */
        $escape_sequence = false;
        /**
         * Used to indicate if we're in a unicode escape sequence, as well as
         * store the characters read so far. Will be null if we're not currently
         * in a unicode escape sequence.
         * @var string|null
         */
        $escape_sequence_unicode = null;
        /**
         * Indicate that we're in an array location, which is wrapped in
         * square brackets.
         * @var bool
         */
        $array_location = false;
        for ($i = 0; $i <= strlen($path); $i++) {
            /** 
             * Will be null when we're at the end of the string, otherwise will
             * be the current character to be processed.
             * @var string|null 
             */
            $char = isset($path[$i]) ? $path[$i] : null;
            $char_next = isset($path[$i + 1]) ? $path[$i + 1] : null;
            if (is_null($leg)) {
                // we are starting a leg, reset parameters
                $quoted_literal = false;
                $escape_sequence = false;
                $escape_sequence_unicode = null;
                $array_location = false;
                $leg = '';
                // check if we're starting a quoted literal or other special case
                if ($char == '.') {
                    // we are starting a new leg with a dot.
                    // Ignore it, because it might lead to different behaviors
                    continue;
                } elseif ($char == "\"") {
                    // we are starting a quoted literal
                    $quoted_literal = true;
                } elseif ($char == '[') {
                    // we are starting an array location
                    $array_location = true;
                } else {
                    // otherwise just start the leg with the current character
                    $leg = $char;
                }
            } elseif ($array_location) {
                // we're in an array location
                if ($char == ']') {
                    // we're at the end of the array location, add the leg to the list
                    $legs[] = '[' . $leg . ']';
                    $leg = null;
                } else {
                    // we are in the middle of an array location
                    $leg .= $char;
                }
            } elseif ($quoted_literal) {
                // we're in a quoted literal
                if ($escape_sequence) {
                    // we're in the middle of an escape sequence
                    if ($char == 'u') {
                        // start reading a unicode escape sequence
                        $escape_sequence_unicode = '';
                    } elseif (!is_null($escape_sequence_unicode)) {
                        // we're in a unicode escape sequence
                        // validate that character is a valid hex character
                        if (!in_array($char, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'])) {
                            throw new InvalidArgumentException('Invalid unicode escape sequence in JSON path: ' . $path);
                        }
                        $escape_sequence_unicode .= $char;
                        // if we've read all four characters, add the character to the leg and reset state
                        if (strlen($escape_sequence_unicode)  == 4) {
                            $leg .= chr(intval(hexdec($escape_sequence_unicode) ?: 65533));
                            $escape_sequence = false;
                            $escape_sequence_unicode = false;
                        }
                    } else {
                        // if we're not in a unicode escape sequence, just add the escaped character
                        $leg .= $char;
                        $escape_sequence = false;
                    }
                } elseif ($char == '\\') {
                    // start an escape sequence
                    $escape_sequence = true;
                } elseif ($char == "\"") {
                    // we're at the end of a quoted literal, close it and reset state
                    $legs[] = new Literal($leg);
                    $leg = null;
                    // skip ahead and check that we're either at the end or have
                    // a valid 
                    $i++;
                    if (isset($char_next) && $char_next != '.' && $char_next != '[') {
                        throw new InvalidArgumentException('Invalid JSON path: ' . $path);
                    }
                } else {
                    // just a normal character inside a quoted literal
                    $leg .= $char;
                }
            } else {
                // we are not in a quoted literal, but are in the middle of a leg
                if (is_null($char) || $char == '.' || $char == '[') {
                    // we are at the end of a leg or the string, add it to the list and reset
                    // if ($quoted_literal) $leg = new Literal($leg); - unnecessary, always false
                    $legs[] = $leg;
                    $leg = null;
                    // back up one character if we're starting an array location
                    if ($char == '[') {
                        $i--;
                    }
                } else {
                    // add character to the current leg
                    $leg .= $char;
                }
            }
        }
        // return legs
        return $legs;
    }

    /**
     * Parses a JSON path leg string into an appropriate leg object, depending
     * on the string. Because parsePath returns literals as Literal objects,
     * this method also accepts those, and will return them unchanged.
     */
    public static function parseLeg(string|Literal $leg): PathPartInterface
    {
        if ($leg instanceof Literal) {
            return $leg;
        } elseif ($leg == '$') {
            return new Root;
        } elseif ($leg == '*') {
            return new AllObjectValues;
        } elseif (str_contains($leg, '**')) {
            // this is a wildcard
            $parts = explode('**', $leg);
            if (count($parts) != 2) {
                throw new InvalidArgumentException('Invalid JSON path wildcard ' . $leg);
            }
            return new Wildcard($parts[1], $parts[0]);
        } elseif (str_starts_with($leg, '[') && str_ends_with($leg, ']')) {
            // looks like an array index
            $value = substr($leg, 1, -1);
            // contents are a wildcard array value
            if ($value == '*') {
                return new AllArrayValues;
            }
            // contents look like a range
            if (str_contains($value, ' to ')) {
                $parts = explode(' to ', $value);
                if (count($parts) != 2) {
                    throw new InvalidArgumentException('Invalid JSON path array range ' . $leg);
                }
                $start = intval($parts[0]);
                $end = intval($parts[1]);
                return new ArrayIndexRange($start, $end);
            }
            // must be an integer if nothing else matched
            $int = intval($value);
            if ($int != $value) {
                throw new InvalidArgumentException('Invalid JSON path array location ' . $leg);
            }
            return new ArrayIndex($int);
        } else {
            return new Literal($leg);
        }
    }
}
