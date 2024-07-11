# sqlite_json_polyfill
A polyfill to make implementations of MySQL JSON functions available in SQLite, although performance will never even approach a native plugin. Supports injecting the necessary functions into connections from PDO or SQLite3 objects, using either PDO::sqliteCreateFunction or SQLite3::createFunction under the hood.
