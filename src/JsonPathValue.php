<?php

namespace Joby\SqliteJsonPolyfill;

class JsonPathValue
{
    public function __construct(
        protected JsonPath $path,
        protected mixed &$value,
    ) {
    }

    public function path(): JsonPath
    {
        return $this->path;
    }

    public function &value(): mixed
    {
        return $this->value;
    }
}
