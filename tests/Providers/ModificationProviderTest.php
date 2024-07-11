<?php

namespace Joby\SqliteJsonPolyfill\Providers;

use PHPUnit\Framework\TestCase;

class ModificationProviderTest extends TestCase
{
    public function test_JSON_UNQUOTE()
    {
        // the current implementation just wraps json_decode, so we don't need to test it extensively
        $this->assertEquals("\t2", ModificationProvider::JSON_UNQUOTE('"\\t\\u0032"'));
        $this->assertNull(ModificationProvider::JSON_UNQUOTE('NULL'));
    }
}
