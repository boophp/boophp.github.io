<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

global  $servers;

$servers = array(
    "127.0.0.1:11212",
    "127.0.0.1:11213",
    "127.0.0.1:11214",
    "127.0.0.1:11215",
    "101.251.196.90:11211",
    "101.251.196.90:11212",
    "101.251.196.90:11213",
    "101.251.196.90:11214"
);
global $srvs;

$srvs = array();
//相当于有多个连接器，每个连接器都是单独的
foreach($servers as $key=>$server)
{
    list($ip, $port) = explode(":", $server);
    $mc = new Memcache;
    $mc->connect($ip, $port);
    $srvs[$key] = $mc;
}