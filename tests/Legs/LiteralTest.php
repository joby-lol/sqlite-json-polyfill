<?php

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
