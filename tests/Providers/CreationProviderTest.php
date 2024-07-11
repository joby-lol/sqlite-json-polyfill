<?php

/**
 * MIT License
 *
 * SQLite JSON Polyfill: https://code.byjoby.com/sqlite-json-polyfill/
 * Copyright (c) 2024 Joby Elliott
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

use PHPUnit\Framework\TestCase;

class CreationProviderTest extends TestCase
{
    public function test_JSON_ARRAY()
    {
        $this->assertEquals('[]', CreationProvider::JSON_ARRAY());
        $this->assertEquals('[1,"abc",null,true]', CreationProvider::JSON_ARRAY(1, "abc", NULL, TRUE));
    }

    public function test_JSON_OBJECT()
    {
        $this->assertEquals('{}', CreationProvider::JSON_OBJECT());
        $this->assertEquals('{"a":1,"b":"abc","c":null,"d":true}', CreationProvider::JSON_OBJECT('a', 1, 'b', "abc", 'c', NULL, 'd', TRUE));
        $this->assertEquals('{"3":"a","5":"b"}', CreationProvider::JSON_OBJECT(3, "a", 5, "b"));
        $this->expectException(\InvalidArgumentException::class);
        CreationProvider::JSON_OBJECT('a', 1, 'b', "abc", 'c', NULL, 'd');
        $this->expectException(\InvalidArgumentException::class);
        CreationProvider::JSON_OBJECT('a', 1, 'b', "abc", 'c', NULL, NULL, TRUE);
    }

    public function test_JSON_QUOTE()
    {
        // the current implementation just wraps json_encode, so we don't need to test it extensively
        $this->assertEquals('"abc"', CreationProvider::JSON_QUOTE('abc'));
        $this->assertEquals('NULL', CreationProvider::JSON_QUOTE(null));
    }
}
