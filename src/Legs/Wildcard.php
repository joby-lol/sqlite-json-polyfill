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

class Wildcard extends AbstractLeg
{
    public function __construct(
        protected string $suffix,
        protected string|null $prefix = null
    ) {
    }

    public function keys(array $data): array
    {
        $keys = [];
        foreach (array_keys($data) as $key) {
            if (!str_ends_with($key, $this->suffix)) continue;
            if (!is_null($this->prefix) && !str_starts_with($key, $this->prefix)) continue;
            $keys[] = $key;
        }
        return $keys;
    }

    public function prefix(): string|null
    {
        return $this->prefix;
    }

    public function suffix(): string
    {
        return $this->suffix;
    }

    public function __toString(): string
    {
        return '.'.$this->prefix . '**' . $this->suffix;
    }
}
