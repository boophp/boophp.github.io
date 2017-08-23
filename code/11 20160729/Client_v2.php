

<?php

require 'Ice.php';
require 'DBAgent.php';
require "func.php";
try
{
    
    
    $prx = createIceProxy("DBAgent", 12115);
    $dbproxy = Space_DBAgentPrxHelper::checkedCast($prx);
    if (!$dbproxy)
        throw new RuntimeException("Invalid proxy");
    $ret = $dbproxy->sQuery("ly_user", 2,"SELECT * FROM ly_user");
    print_r($ret);
   /*  $ret = $dbproxy->sQuery("ly_user", 1,"SELECT * FROM ly_user");
    print_r($ret);*/
    
    $sql = "UPDATE ly_user SET username='wuxing103' WHERE username='wuxing10'";
    $ret = $dbproxy->sQuery("ly_user", 10, $sql);
    print_r($ret);
  /*  $sql = addUserSql(9,"zhihua9","zhihua.cn9","zhihua");
    $ret = $dbproxy->sQuery("ly_user", 9,$sql);
    print_r($ret);
    $sql = addUserSql(10, "wuxing10","wuxing10@ucai.cn","wuxing");
    $ret = $dbproxy->sQuery("ly_user", 10,$sql); 
    print_r($ret);*/
    $ret = $dbproxy->sQuery("ly_user", 9,"SELECT * FROM ly_user WHERE userid='9'");
    print_r($ret);
    $ret = $dbproxy->sQuery("ly_user", 10,"SELECT * FROM ly_user");
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