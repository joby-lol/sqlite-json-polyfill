<?php
namespace Joby\SqliteJsonPolyfill;

use PDO;
use SQLite3;

class SqliteJsonPolyfill {
    /** array<class-string<SQLiteShimProvider>> */
    const DEFAULT_PROVIDERS = [];

    /**
     * @param array<class-string<PolyfillProvider>>|null $providers 
     */
    public static function shim(PDO|SQLite3 $conn, array $providers = null):void {
        if (is_null($providers)) {
            $providers = static::DEFAULT_PROVIDERS;
        }
        foreach ($providers as $provider) {
            
        }
    }
}