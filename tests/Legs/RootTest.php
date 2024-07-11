<?php

namespace Joby\SqliteJsonPolyfill\Legs;

use Joby\SqliteJsonPolyfill\JsonPath;
use PHPUnit\Framework\TestCase;

class RootTest extends TestCase
{
    public function testValues()
    {
        $data = ['foo'=>'bar'];
        $leg = new Root();
        $results = $leg->values($data);
        $this->assertEquals($data, $results[0]->value());
        $this->assertEquals(new JsonPath($leg), $results[0]->path());
    }
}
