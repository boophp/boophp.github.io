<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//Memcache到底是不是一致性哈希
require_once "config.php";
$data = array();
$count = count($srvs);
for($i=1; $i<=1000; $i++)
{
    $val = rand(10000000, 99999999);
    $data["key_".$i] = $val;
    $srvs[$i%$count]->set("key_".$i, $val,MEMCACHE_COMPRESSED, 60);
}
