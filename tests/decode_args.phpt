--TEST--
simdjson_decode args test
--FILE--
<?php
$reflection = new \ReflectionFunction('\simdjson_decode');
echo $reflection;

?>
--EXPECTF--
Function [ <internal:simdjson_plus> function simdjson_decode ] {

  - Parameters [3] {
    Parameter #0 [ <required> string $json ]
    Parameter #1 [ <optional> bool $associative%s ]
    Parameter #2 [ <optional> int $depth%s ]
  }
  - Return [ mixed ]
}