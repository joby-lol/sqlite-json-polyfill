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

namespace Joby\SqliteJsonPolyfill;

use Joby\SqliteJsonPolyfill\Legs\AllArrayValues;
use Joby\SqliteJsonPolyfill\Legs\AllObjectValues;
use Joby\SqliteJsonPolyfill\Legs\ArrayIndex;
use Joby\SqliteJsonPolyfill\Legs\ArrayIndexRange;
use Joby\SqliteJsonPolyfill\Legs\Literal;
use Joby\SqliteJsonPolyfill\Legs\Root;
use Joby\SqliteJsonPolyfill\Legs\Wildcard;
use PHPUnit\Framework\TestCase;

class JsonPathParserTest extends TestCase
{
    public function testParsePath_simple()
    {
        $this->assertEquals(['$'], JsonPathParser::parsePath('$'));
        $this->assertEquals(['$', 'foo'], JsonPathParser::parsePath('$.foo'));
    }

    public function testParsePath_quoted()
    {
        $this->assertEquals(
            [new Literal('foo')],
            JsonPathParser::parsePath('"foo"')
        );
        $this->assertEquals(
            [new Literal('foo'), 'bar'],
            JsonPathParser::parsePath('"foo".bar')
        );
        $this->assertEquals(
            [new Literal('foo'), new Literal('bar')],
            JsonPathParser::parsePath('"foo"."bar"')
        );
        $this->assertEquals(
            ['foo', new Literal('bar')],
            JsonPathParser::parsePath('foo."bar"')
        );
        $this->assertEquals(
            ['$', new Literal('bar')],
            JsonPathParser::parsePath('$."bar"')
        );
    }

    public function testParsePath_escapedQuotes()
    {
        $this->assertEquals(
            [new Literal('foo"bar')],
            JsonPathParser::parsePath('"foo\"bar"')
        );
        $this->assertEquals(
            [new Literal('foo"bar'), 'baz'],
            JsonPathParser::parsePath('"foo\"bar".baz')
        );
        $this->assertEquals(
            [new Literal('foo"bar'), new Literal('baz')],
            JsonPathParser::parsePath('"foo\"bar"."baz"')
        );
        $this->assertEquals(
            ['foo', new Literal('bar"baz')],
            JsonPathParser::parsePath('foo."bar\"baz"')
        );
        $this->assertEquals(
            ['$', new Literal('bar"baz')],
            JsonPathParser::parsePath('$."bar\"baz"')
        );
    }

    public function testParsePath_escapedBackslashes()
    {
        $this->assertEquals(
            [new Literal('foo\\bar')],
            JsonPathParser::parsePath('"foo\\\\bar"')
        );
        $this->assertEquals(
            [new Literal('foo\\bar'), 'baz'],
            JsonPathParser::parsePath('"foo\\\\bar".baz')
        );
        $this->assertEquals(
            [new Literal('foo\\bar'), new Literal('baz')],
            JsonPathParser::parsePath('"foo\\\\bar"."baz"')
        );
        $this->assertEquals(
            ['foo', new Literal('bar\\baz')],
            JsonPathParser::parsePath('foo."bar\\\\baz"')
        );
        $this->assertEquals(
            ['$', new Literal('bar\\baz')],
            JsonPathParser::parsePath('$."bar\\\\baz"')
        );
    }

    public function testParsePath_escapedUnicode()
    {
        $this->assertEquals(
            [new Literal('$foo')],
            JsonPathParser::parsePath('"\u0024foo"')
        );
        $this->assertEquals(
            [new Literal('foo$')],
            JsonPathParser::parsePath('"foo\u0024"')
        );
        $this->assertEquals(
            [new Literal('$')],
            JsonPathParser::parsePath('"\u0024"')
        );
    }

    public function testParsePath_arrayLocations()
    {
        $this->assertEquals(
            ['foo', '[0]'],
            JsonPathParser::parsePath('foo[0]')
        );
        $this->assertEquals(
            ['foo', '[0]', 'bar'],
            JsonPathParser::parsePath('foo[0].bar')
        );
        $this->assertEquals(
            ['foo', '[0]', 'bar', '[1]'],
            JsonPathParser::parsePath('foo[0].bar[1]')
        );
    }

    public function testParseLeg_root()
    {
        $this->assertEquals(new Root, JsonPathParser::parseLeg('$'));
    }

    public function testParseLeg_literal()
    {
        $this->assertEquals(new Literal('foo'), JsonPathParser::parseLeg('foo'));
        $this->assertEquals(new Literal('foo'), JsonPathParser::parseLeg(new Literal('foo')));
        $this->assertEquals(new Literal('foo'), JsonPathParser::parseLeg(new Literal('foo')));
    }

    public function testParseLeg_wildcard()
    {
        $this->assertEquals(new Wildcard('bar', 'foo'), JsonPathParser::parseLeg('foo**bar'));
        $this->assertEquals(new Wildcard('foo'), JsonPathParser::parseLeg('**foo'));
    }

    public function testParseLeg_allObjectValues()
    {
        $this->assertEquals(new AllObjectValues, JsonPathParser::parseLeg('*'));
    }

    public function testParseLeg_arrayIndexes()
    {
        $this->assertEquals(new ArrayIndex(5), JsonPathParser::parseLeg('[5]'));
        $this->assertEquals(new AllArrayValues(), JsonPathParser::parseLeg('[*]'));
        $this->assertEquals(new ArrayIndexRange(1, 7), JsonPathParser::parseLeg('[1 to 7]'));
    }

    // TODO: test that invalid literal unicode escape sequences put U+FFFD in
    // TODO: implement and test replacing disallowed unicode characters with U+FFFD
    // maybe we need to break out unicode hex to character conversion into its own method for testing/sanity
}
