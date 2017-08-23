<?php
 
$data = array(
	'key1' => 'hequan',
	'key2' => 'stamhe',
	'key3' => '全!@#$%^&*()":?><|}{[]仔',
);
 
$packdata = msgpack_serialize($data);
var_dump($packdata);
printf("pack = [%s]\n", $packdata);
 
$new_data = msgpack_unserialize($packdata);
var_dump($new_data);