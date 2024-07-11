# PHP SQLite JSON Polyfill

A polyfill to make implementations of most MySQL JSON functions available in SQLite, although performance will never even approach a native plugin. Supports injecting the necessary functions into connections from PDO or SQLite3 objects, using either PDO::sqliteCreateFunction or SQLite3::createFunction under the hood.

## Development status

**Super duper under construction** do not use in production! Once a significant portion of the most-used functions are available I'll probably at least start on the 0.x version numbers, but until then it's the wild west.
