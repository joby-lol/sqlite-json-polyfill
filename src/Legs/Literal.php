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

use InvalidArgumentException;
use RuntimeException;

class Literal extends AbstractLeg
{
    public function __construct(protected string $literal)
    {
        if (strlen($literal) === 0) {
            throw new InvalidArgumentException('Literal leg must not be empty');
        }
    }

    public function literal(): string
    {
        return $this->literal;
    }

    public function keys(array $value): array
    {
        return array_key_exists($this->literal, $value) ? [$this->literal] : [];
    }

    public function __toString(): string
    {
        $quoted = json_encode($this->literal);
        if ($quoted === false) {
            throw new RuntimeException('Failed to encode literal as JSON');
        }
        if (str_contains($quoted, '\\')) {
            return '.' . $quoted;
        } else {
            return '.' . $this->literal;
        }
    }
}
