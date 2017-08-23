<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'FMemcache.php';
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
for($i=1; $i<=1000; $i++)
{
    if(onlyGet("key_".$i))
    {
        echo "key_".$i." OK\n";
    }
    else
    {
        echo "key_".$i." FAILED\n";
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

exit;

function fmsetgettest() {
    global $servers;

    $mc = new FMemcache();
   // $servers = array_reverse($servers);
    $mc->addServers($servers);

    for ($i = 0; $i < 1000; $i++) {
        $key = "UCAI_" . $i;
        $mc->set($key, $i, MEMCACHE_COMPRESSED, 60);
    }
    return;
}

fmsetgettest();
foreach ($servers as $server) {
        list($ip, $port) = explode(":", $server);
        $mc = new Memcache;
        $mc->connect($ip, $port);
        for($i=0; $i<1000; $i++)
        {
            $key = "UCAI_".$i;
            $val = $mc->get($key);
            if ($val) {
                echo $server . " " . " GET $key $val OK\n";
            } 
        }
        $mc->close();
}