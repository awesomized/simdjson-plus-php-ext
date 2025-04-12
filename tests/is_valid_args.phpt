--TEST--
simdjson_is_valid args test
--FILE--
<?php
$reflection = new \ReflectionFunction('\simdjson_is_valid');
echo $reflection;

?>
--EXPECTF--
Function [ <internal:simdjson_plus> function simdjson_is_valid ] {

  - Parameters [2] {
    Parameter #0 [ <required> string $json ]
    Parameter #1 [ <optional> int%S $depth%S ]
  }
  - Return [ bool%S ]
}