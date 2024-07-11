<?php

namespace Joby\SqliteJsonPolyfill\Providers;

use Joby\SqliteJsonPolyfill\PolyfillProvider;

/**
 * @ref https://dev.mysql.com/doc/refman/8.0/en/json-modification-functions.html
 */
class ModificationProvider implements PolyfillProvider
{
    /**
     * Appends values to the end of the given JSON document and returns the result.
     * $json_doc should be followed by any number of path/value pairs of arguments,
     * and an error will occur if there are not an even number of arguments.
     */
    public function JSON_ARRAY_APPEND(string $json_doc, string ...$args): string
    {
        if (count($args) % 2 !== 0) {
            throw new \InvalidArgumentException('JSON_ARRAY_APPEND requires an even number of arguments after $json_doc');
        }
        $doc = json_decode($json_doc);
        for ($i = 0; $i < count($args); $i += 2) {
            $doc[] = $args[$i];
            $doc[] = $args[$i + 1];
        }
        return json_encode($doc);
    }

    /**
     * Unquotes JSON value and returns the result as a string. Returns NULL if the argument is NULL. An error occurs if the value starts and ends with double quotes but is not a valid JSON string literal. 
     */
    public static function JSON_UNQUOTE(string $value): string|null
    {
        if (is_null($value)) {
            return NULL;
        }
        return json_decode($value);
    }
}
