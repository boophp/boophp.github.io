<?php

require 'Ice.php';
require 'Hello.php';

$ic = null;
try
{
    $ic = Ice_initialize();
    $base = $ic->stringToProxy("hello:default -p 10000");
    $printer = Demo_HelloPrxHelper::checkedCast($base);
    if (!$printer)
        throw new RuntimeException("Invalid proxy");

    $printer->sayHello(1);
    $printer->printHello("China");
} catch (Exception $ex)
{
    echo $ex;
}

if ($ic)
{
// Clean up 
    try
    {
        $ic->destroy();
    } catch (Exception $ex)
    {
        echo $ex;
    }
}
?>