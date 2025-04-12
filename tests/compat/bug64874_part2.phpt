--TEST--
Case-sensitivity part of bug #64874 compat ("json_decode handles whitespace and case-sensitivity incorrectly")
--FILE--
<?php
function decode($json) {
    try {
        var_dump(simdjson_decode($json));
    } catch (SimdJsonDecoderException $e) {
        printf("Caught %s: %s\n", get_class($e), $e->getMessage());
    }
}

// Only lowercase should work
decode('true');
decode('True');
decode('[true]');
decode('[True]');
echo PHP_EOL;

decode('false');
decode('False');
decode('[false]');
decode('[False]');
echo PHP_EOL;

decode('null');
decode('Null');
decode('[null]');
decode('[Null]');
echo PHP_EOL;

echo "Done\n";
?>
--EXPECT--
bool(true)
Caught SimdJsonDecoderException: The JSON document has an improper structure: missing or superfluous commas, braces, missing keys, etc.  This is a fatal and unrecoverable error.
array(1) {
  [0]=>
  bool(true)
}
Caught SimdJsonDecoderException: The JSON document has an improper structure: missing or superfluous commas, braces, missing keys, etc.  This is a fatal and unrecoverable error.

bool(false)
Caught SimdJsonDecoderException: The JSON document has an improper structure: missing or superfluous commas, braces, missing keys, etc.  This is a fatal and unrecoverable error.
array(1) {
  [0]=>
  bool(false)
}
Caught SimdJsonDecoderException: The JSON document has an improper structure: missing or superfluous commas, braces, missing keys, etc.  This is a fatal and unrecoverable error.

NULL
Caught SimdJsonDecoderException: The JSON document has an improper structure: missing or superfluous commas, braces, missing keys, etc.  This is a fatal and unrecoverable error.
array(1) {
  [0]=>
  NULL
}
Caught SimdJsonDecoderException: The JSON document has an improper structure: missing or superfluous commas, braces, missing keys, etc.  This is a fatal and unrecoverable error.

Done
