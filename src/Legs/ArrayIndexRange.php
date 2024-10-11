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

namespace Joby\SqliteJsonPolyfill\Legs;

use InvalidArgumentException;

class ArrayIndexRange extends AbstractLeg
{
    public function __construct(
        protected int $start,
        protected int $end
    ) {
        if ($start < 0) {
            throw new InvalidArgumentException('Array start must be greater than or equal to 0');
        }
        if ($end < 0) {
            throw new InvalidArgumentException('Array end must be greater than or equal to 0');
        }
        if ($start > $end) {
            throw new InvalidArgumentException('Array start must be less than or equal to end');
        }
    }

    public function start(): int
    {
        return $this->start;
    }

    public function end(): int
    {
        return $this->end;
    }

    public function keys(array $data): array
    {
        $keys = [];
        for ($i = $this->start; $i <= $this->end; $i++) {
            if (array_key_exists($i, $data)) {
                $keys[] = $i;
            }
        }
        return $keys;
    }

    public function __toString(): string
    {
        return '[' . $this->start . ' to ' . $this->end . ']';
    }
}
