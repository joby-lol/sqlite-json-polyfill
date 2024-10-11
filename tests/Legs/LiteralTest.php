<?php


/**
 * MIT License
 *
 * SQLite JSON Polyfill: https://go.joby.lol/sqlitejsonpolyfill
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

namespace Joby\SqliteJsonPolyfill\Legs;

use Joby\SqliteJsonPolyfill\JsonPath;
use Joby\SqliteJsonPolyfill\JsonPathValue;
use PHPUnit\Framework\TestCase;

class LiteralTest extends TestCase
{
    public function testValues()
    {
        $data = ['foo' => 'bar'];
        $results = [new JsonPathValue(new JsonPath(new Root()), $data)];
        $leg = new Literal('foo');
        $values = $leg->values($results);
        $this->assertEquals('bar', $values[0]->value());
        $this->assertEquals('$.foo', (string) $values[0]->path());
        // also assert that the result includes a reference to the original document
        $data['foo'] = 'baz';
        $this->assertEquals('baz', $values[0]->value());
    }

    public function testMultipleValues()
    {
        $path = new JsonPath(new Root());
        $a = ['foo' => 'bar'];
        $b = ['foo' => 'baz'];
        $c = ['baz' => 'buzz'];
        $results = [
            new JsonPathValue($path, $a),
            new JsonPathValue($path, $b),
            new JsonPathValue($path, $c),
        ];
        $leg = new Literal('foo');
        $values = $leg->values($results);
        $this->assertEquals('bar', $values[0]->value());
        $this->assertEquals('baz', $values[1]->value());
        $this->assertCount(2, $values);
    }
}
