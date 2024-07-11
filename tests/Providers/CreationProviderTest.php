<?php

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
