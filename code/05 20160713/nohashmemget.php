<?php
require_once "config.php";
$count = count($srvs);
 for($i=1; $i<=1000; $i++)
{
    $key  = "key_".$i;
    if($srvs[$i%$count]->get($key))
	{
		echo  $key." OK\n";
	}
	else
	{
		echo  $key." FAILED\n";
	}
}