<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

global $ic;

$ic = Ice_initialize();


function createIceProxy($proxystr, $port)
{
    global $ic;
    global $ice_ctx;

    if (!isset($ice_ctx["CALLER"]))
    {
        ice_ctx_caller();
    }
    $tmp = explode('#', $proxystr, 2);
    $typestr = $tmp[0];
    $proxystr = $proxystr . ":tcp -p $port -t 70000";
    echo $proxystr;
    $prx = $ic->stringToProxy($proxystr)->ice_uncheckedCast("::Space::" . $typestr)->ice_context($ice_ctx);
 
    echo "connect ok!\n";
    return $prx;
}

function ice_ctx_cache($second)
{
    global $ice_ctx;
    if (!isset($ice_ctx["CALLER"]))
    {
        ice_ctx_caller();
    }
    $ctx = $ice_ctx;
    $ctx['CACHE'] = strval($second);
    return $ctx;
}

function ice_ctx_caller($caller = "")
{
    global $ice_ctx;
    if ($caller == "")
    {
        $myip = getCallerIp();
        $caller = sprintf("%s:%s:%08x", $_SERVER['PHP_SELF'], $myip, mt_rand());
    }
    $ice_ctx["CALLER"] = $caller;
}

function getCallerIp()
{
    $myip = $_SERVER['SERVER_ADDR'];
    if ($myip == '' || $myip == "127.0.0.1")
    {
        $filename =  "/localip";
        if (is_file($filename))
        {
            $myip = trim(file_get_contents($filename));
        }
        else if ($myip == '')
        {
            $myip = "unknown";
        }
    }
    return $myip;
}

include_once("userfunc.php");