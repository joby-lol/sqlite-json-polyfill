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

use Joby\SqliteJsonPolyfill\DeterministicMethod;
use Joby\SqliteJsonPolyfill\InjectableMethod;
use Joby\SqliteJsonPolyfill\PolyfillProvider;
use ReflectionFunction;

/**
 * Abstract class for building providers in an easy way. All providers need to
 * be able to produce a list of the methods they need to have injected, and this
 * class automatically builds that list by introspecting on the called class and
 * looking for methods that have their names in ALL_CAPS.
 */
abstract class AbstractProvider implements PolyfillProvider
{
    /** @var InjectableMethod[]|null */
    protected static array|null $injected_methods = null;

    public static function getInjectableMethods(): array
    {
        if (is_null(static::$injected_methods)) {
            $methods = get_class_methods(static::class);
            $injected = [];
            foreach ($methods as $method_name) {
                if (strtoupper($method_name) === $method_name) {
                    $callable = (static::class)::$method_name(...);
                    $reflection = new ReflectionFunction($callable);
                    if ($reflection->isVariadic()) {
                        $num_args = -1;
                    } else {
                        $num_args = $reflection->getNumberOfParameters();
                    }
                    if ($reflection->getAttributes(DeterministicMethod::class)) {
                        $deterministic = true;
                    } else {
                        $deterministic = false;
                    }
                    $injected[] = new InjectableMethod(
                        $method_name,
                        $callable,
                        $num_args,
                        $deterministic,
                    );
                }
            }
            static::$injected_methods = $injected;
        }
        return static::$injected_methods;
    }
}
