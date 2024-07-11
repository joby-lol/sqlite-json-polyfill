<?php

namespace Joby\SqliteJsonPolyfill\Providers;

use Joby\SqliteJsonPolyfill\PolyfillProvider;

/**
 * @ref https://dev.mysql.com/doc/refman/8.0/en/json-creation-functions.html#function_json-array
 */
class CreationProvider implements PolyfillProvider
{
    /**
     * Evaluates a (possibly empty) list of values and returns a JSON array
     * containing those values. 
     */
    public static function JSON_ARRAY(...$args): string
    {
        return json_encode($args);
    }

    /**
     * Evaluates a (possibly empty) list of key-value pairs and returns a JSON
     * object containing those pairs. An error occurs if any key name is NULL or
     * the number of arguments is odd. 
     */
    public static function JSON_OBJECT(...$args): string
    {
        if (count($args) === 0) {
            return '{}';
        }
        if (count($args) % 2 !== 0) {
            throw new \InvalidArgumentException('JSON_OBJECT requires an even number of arguments');
        }
        $result = [];
        for ($i = 0; $i < count($args); $i += 2) {
            if (is_null($args[$i])) {
                throw new \InvalidArgumentException('JSON_OBJECT requires non-null key names');
            }
            $result[$args[$i]] = $args[$i + 1];
        }
        return json_encode($result);
    }

    /**
     * Quotes a string as a JSON value by wrapping it with double quote
     * characters and escaping interior quote and other characters, then
     * returning the result as a utf8mb4 string. Returns "NULL" if the argument
     * is NULL.
     *
     * This function is typically used to produce a valid JSON string literal
     * for inclusion within a JSON document. 
     */
    public static function JSON_QUOTE(string|null $value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }
        return json_encode($value);
    }
}
