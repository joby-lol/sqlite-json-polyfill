<?php

namespace Joby\SqliteJsonPolyfill\Providers;

use Joby\SqliteJsonPolyfill\JsonPath;
use Joby\SqliteJsonPolyfill\PolyfillProvider;

/**
 * @ref https://dev.mysql.com/doc/refman/8.0/en/json-search-functions.html
 */
class SearchProvider implements PolyfillProvider
{
    public static function JSON_CONTAINS(string $target, string $candidate, string $path = '$'): int
    {
        $path = new JsonPath($path);
        return static::JSON_CONTAINS_array(json_decode($target), json_decode($candidate), $path);
    }

    protected static function JSON_CONTAINS_array($target, $candidate, JsonPath $path): int
    {
        foreach ($target as $value) {
            if (static::jsonContains($value, $candidate, $path)) {
                return true;
            }
        }
        return false;
    }
}
