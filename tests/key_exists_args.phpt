--TEST--
simdjson_key_exists args test
--FILE--
<?php
$reflection = new \ReflectionFunction('\simdjson_key_exists');
echo $reflection;

?>
--EXPECTF--
Function [ <internal:simdjson_plus> function simdjson_key_exists ] {

  - Parameters [3] {
    Parameter #0 [ <required> string $json ]
    Parameter #1 [ <required> string $key ]
    Parameter #2 [ <optional> int%S $depth%S ]
  }
  - Return [ bool%S ]
}