<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "config.php";
 
for($i=1; $i<=1000; $i++)
{ 
    if($mc->get("key_".$i))
	{
		echo "key_".$i." OK\n";
	}
	else
	{
		echo "key_".$i." FAILED\n";
	}
}
