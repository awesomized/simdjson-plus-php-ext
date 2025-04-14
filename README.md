`awesome/simdjson_plus`
=======================

[![Tests status](https://github.com/awesomized/simdjson-plus-php-ext/workflows/Tests/badge.svg)](https://github.com/awesomized/simdjson-plus-php-ext/actions?query=workflow%3ATests)
[![Latest Stable Version](https://img.shields.io/packagist/v/awesome/simdjson_plus)](https://packagist.org/packages/awesome/simdjson_plus)

🚀 Blazing-fast JSON encoding and decoding for PHP, powered by the 
[simdjson project](https://github.com/lemire/simdjson).

*This is a fork of [JakubOnderka/simdjson_php](https://github.com/JakubOnderka/simdjson_php) (which is a fork 
of [crazyxman/simdjson_php](https://github.com/crazyxman/simdjson_php))*

Since the [simdjson](https://pecl.php.net/package/simdjson) PECL extension seems to be unmaintained, or at least slow 
to [accept PRs for improvements](https://github.com/crazyxman/simdjson_php/pulls), we packaged this up under a new
name (`simdjson_plus`) to avoid naming conflicts and published it on Packagist (instead of PECL) for easier
installation.

It's a drop-in replacement for the PECL extension, with additional features from JakubOnderka, such as accelerated
JSON encoding (not just decoding) and optimizations.

## Performance Comparison: How Fast is simdjson_plus?

| Operation             | PHP Built-in | simdjson_plus | Speedup  |
|-----------------------|--------------|---------------|----------|
| Decode to array       | 1.48 ms      | 0.46 ms       | **3.2×** |
| Decode to object      | 1.56 ms      | 0.54 ms       | **2.9×** |
| Encode                | 0.67 ms      | 0.26 ms       | **2.5×** |
| Encode (pretty print) | 0.83 ms      | 0.31 ms       | **2.6×** |
| Validate              | 1.37 ms      | 0.22 ms       | **6.2×** |
| Count items           | 1.51 ms      | 0.16 ms       | **9.4×** |

Tests were conducted using PHP 8.3 on an [Apple M1 Max](https://en.wikipedia.org/wiki/Apple_M1#M1_Pro_and_M1_Max). For test specification see `TwitterDecodeBench.php` and `TwitterEncoderBench.php`.

Additionally, simdjson_plus reduces memory usage compared to `json_decode()`. For example, when decoding twitter.json, memory consumption drops from 3.01 MB to 2.47 MB due to efficient array key deduplication.

## Related SIMD-accelerated PHP extensions
* [crc_fast](https://packagist.org/packages/awesome/crc_fast) PHP extension for SIMD-accelerated CRC calculations
  at >100GiB/s.
* [simdutf](https://packagist.org/packages/awesome/simdutf) PHP extension for Unicode validation and transcoding at
  billions of characters per second using the [simdutf](https://github.com/simdutf/simdutf) project.

## Requirements

* PHP 8.1+ (PHP 8.2+ recommended for maximum performance)
* g++ (version 7 or better) or clang++ (version 6 or better)
* A 64-bit system with a command-line shell (e.g., Linux, macOS, FreeBSD)

## Changes

See the [change log](CHANGELOG.md).

## Installing

Use [Composer](https://getcomposer.org) to install this library using [PIE](https://github.com/php/pie):

```bash
composer install awesome/simdjson-plus
```

## Compilation Instructions for Linux

To compile simdjson_plus, run the following commands:

```bash
phpize
./configure
make
make test
make install
```

Once installed, add this line to your `php.ini` file:

```ini
extension=simdjson_plus.so
```

## Usage Examples
```php
$jsonString = <<<'JSON'
{
  "Image": {
    "Width":  800,
    "Height": 600,
    "Title":  "View from 15th Floor",
    "Thumbnail": {
      "Url":    "http://www.example.com/image/481989943",
      "Height": 125,
      "Width":  100
    },
    "Animated" : false,
    "IDs": [116, 943, 234, 38793, {"p": "30"}]
  }
}
JSON;

// Check if a JSON string is valid:
$isValid = simdjson_validate($jsonString); //return bool
var_dump($isValid);  // true

// Parsing a JSON string. Similar to the json_decode() function but without the fourth argument
try {
    // returns array|stdClass|string|float|int|bool|null.
    $parsedJSON = simdjson_decode($jsonString, true, 512);
    var_dump($parsedJSON); // PHP array
} catch (RuntimeException $e) {
    echo "Failed to parse $jsonString: {$e->getMessage()}\n";
}

// Encode to JSON string
var_dump(simdjson_encode($parsedJSON));

// note. "/" is a separator. Can be used as the "key" of the object and the "index" of the array
// E.g. "/Image/Thumbnail/Url" is recommended starting in simdjson 4.0.0,
// but "Image/Thumbnail/Url" is accepted for now.

// get the value of a "key" in a json string
// (before simdjson 4.0.0, the recommended leading "/" had to be omitted)
$value = simdjson_key_value($jsonString, "/Image/Thumbnail/Url");
var_dump($value); // string(38) "http://www.example.com/image/481989943"

$value = simdjson_key_value($jsonString, "/Image/IDs/4", true);
var_dump($value);
/*
array(1) {
  ["p"]=>
  string(2) "30"
}
*/

// check if the key exists. return true|false|null. "true" exists, "false" does not exist,
// throws for invalid JSON.
$res = simdjson_key_exists($jsonString, "/Image/IDs/1");
var_dump($res) //bool(true)

// count the values
$res = simdjson_key_count($jsonString, "/Image/IDs");
var_dump($res) //int(5)
```

## Encoder

Most of available options of default `json_encode()` method are not supported by `simdjson_encode()` as they are usually useless.

`simdjson_encode($value)` method has similar behaviour as `json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)`

Supported options are:
* `SIMDJSON_PRETTY_PRINT` - use whitespace in returned data to format it
* `SIMDJSON_INVALID_UTF8_SUBSTITUTE` - convert invalid UTF-8 characters to `\0xfffd` (Unicode Character 'REPLACEMENT CHARACTER' �)
* `SIMDJSON_INVALID_UTF8_IGNORE` - ignore invalid UTF-8 characters
* `SIMDJSON_APPEND_NEWLINE` - append new line character (`\n`) to end of encoded string. This is useful when encoding data to JSONL format as PHP strings are immutable.

Differences are:
* uses different algorithm to convert floating-point number to string, so string format can be slightly different
* even when `JSON_UNESCAPED_UNICODE` is enabled, PHP `json_encode()` escapes some Unicode chars that do not need to be escaped. `simdjson_encode()` escape just Unicode chars that needs to be escaped by JSON spec.
* simdjson will throw `SimdJsonEncoderException` exception in case of error

### Base64 encoding

JSON format do not support binary data. Common way how to transfer binary data in JSON encoding is using base64 encoding.
If you need to include base64 encoded value into JSON, you can use `SimdJsonBase64Encode` class that offers optimised converting to base64 value into JSON and use less memory.
As creating new object in PHP is relatively slow, this approach make sense for string longer than 1 kB.

```php
$fileContent = file_get_contents("example.jpg");
$fileContentEncoded = new SimdJsonBase64Encode($fileContent);
simdjson_encode(['image' => $fileContentEncoded]); // returns {"image":"TWFueSBoYW5kcyBtYWtlIGxpZ2h0IHdvcmsu..."}
```

You can also use base64url encoding (RFC 4648 §5) by setting second argument to true: `new SimdJsonBase64Encode($fileContent, true);`

### Encode to stream

For large data sets, simdjson_plus provides the `simdjson_encode_to_stream()` function to save data directly to a file or output buffer.

```php
$bigStructure = [...];
simdjson_encode_to_stream($bigStructure, fopen("file.json", "w")); // save to file.json
simdjson_encode_to_stream($bigStructure, fopen("php://output", "w")); // send to output buffer
```

## Decoder

There are some differences from `json_decode()` due to the implementation of the underlying simdjson library. This will throw a `SimdJsonDecoderException` if simdjson rejects the JSON.

Note that the simdjson PECL is using a fork of the simdjson C library to imitate php's handling of integers and floats in JSON.

1) The maximum string length that can be passed to `simdjson_decode()` is 4GiB (4294967295 bytes).
   `json_decode()` can decode longer strings.

2) The handling of max depth is counted slightly differently for empty vs non-empty objects/arrays.
   In `json_decode`, an array with a scalar has the same depth as an array with no elements.
   In `simdjson_decode`, an array with a scalar is one level deeper than an array with no elements.
   For typical use cases, this shouldn't matter.
   (e.g. `simdjson_decode('[[]]', true, 2)` will succeed but `json_decode('[[]]', true, 2)` and `simdjson_decode('[[1]]', true, 2)` will fail.)

### Decode from stream

If you need to decode a big file from JSON format that you want to save to a file or send to a user, you can use the `simdjson_decode_from_stream` method.

```php
simdjson_decode_from_stream(fopen("file.json", "r")); // load from file.json
simdjson_decode_from_stream(fopen("php://input", "r")); // send by user
```

## Benchmarks
See the [benchmark](./benchmark) folder for more benchmarks.