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

use PDO;
use SQLite3;

class SqliteJsonPolyfill
{
    /** array<class-string<PolyfillProvider>> */
    const DEFAULT_PROVIDERS = [];

    /**
     * @param array<class-string<PolyfillProvider>>|null $providers 
     */
    public static function shim(PDO|SQLite3 $conn, array $providers = null): void
    {
        if (is_null($providers)) {
            $providers = static::DEFAULT_PROVIDERS;
        }
        $injectMethod = ($conn instanceof PDO) ? 'sqliteCreateFunction' : 'createFunction';
        foreach ($providers as $provider) {
            foreach ($provider::getInjectableMethods() as $m) {
                assert($m instanceof InjectableMethod);
                $conn->$injectMethod(
                    $m->name,
                    $m->method,
                    $m->num_args,
                    $m->deterministic ? PDO::SQLITE_DETERMINISTIC : 0
                );
            }
        }
    }
}
