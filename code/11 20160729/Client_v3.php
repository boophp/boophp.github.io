

<?php

require 'Ice.php';
require 'MObjectService.class.php';
try
{
    $mservice = new MObjectService();
    $uids = array(
    );
    for($i=1; $i<=88; $i++)
    {
        $uids[] = array("uid"=>$i, "uid"=>$i);
    }
     
    $ret = $mservice->getDetails("ly_user", $uids, "uid", "uid");
   
    print_r($ret);
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