<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "config_memcache.php";
$data = array();
 
for($i=1; $i<=1000; $i++)
{
    $val = rand(10000000, 99999999);
    $mc->set("key_".$i, $i,MEMCACHE_COMPRESSED, 60);
}
