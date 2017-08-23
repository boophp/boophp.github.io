<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require("config_memcache.php");
function checkServers() {
    global $servers;
    foreach ($servers as $server) {
        list($ip, $port) = explode(":", $server);
        $mc = new Memcache;
        $mc->connect($ip, $port);
        $key = "atserver";
        $val = $server;
        $mc->set($key, $val, MEMCACHE_COMPRESSED, 60);
        $val = $mc->get($key);
        if ($val) {
            echo $server . " " . " SET $val OK\n";
        } else {
            echo $server . " " . " SET $val FAILED\n";
        }
    }
}

function onlyGet($key) {
    global $servers;
    foreach ($servers as $server) {
        list($ip, $port) = explode(":", $server);
        $mc = new Memcache;
        $mc->connect($ip, $port);
        $var = $mc->get($key);
        if ($var) {
            echo $server . " " . " GET $var OK\n";
        } else {
            echo $server . " " . " GET $var Failed\n";
        }
    }
}

function msetgettest() {
    global $servers;
    global $mc;
//    $servers = array_reverse($servers);
//    $mc = new Memcache();
//    foreach ($servers as $server) {
//        list($ip, $port) = explode(":", $server);
//        $mc->addserver($ip, $port);
//    }
    foreach ($servers as $server) {
        $key = $server;
        $val = $server;
        $mc->set($key, $val, MEMCACHE_COMPRESSED, 60);
    }
    var_dump($mc->get($servers));
}
