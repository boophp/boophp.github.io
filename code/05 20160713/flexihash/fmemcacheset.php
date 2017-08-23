<?php
require_once "config.php";
$data = array();

for($i=1; $i<=1000; $i++)
{
    $val = rand(10000000, 99999999);
    $mc->set("key_".$i, $i, 60);
}
