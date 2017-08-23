<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require "checkmemcached.php";


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

exit;
//设置值
msetgettest();
foreach($servers as $server)
{
    onlyGet($server);
}
exit;

checkServers();
exit;
msetgettest();

foreach($servers as $server)
{
    onlyGet($server);
}
//checkServers();

//onlyGet("atserver");

