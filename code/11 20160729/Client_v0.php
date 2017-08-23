

<?php

require 'Ice.php';
require 'DBQuery.php';
require "func.php";
try
{
    
    
    $prx = createIceProxy("DBQuery", 12112);
    $dbproxy = Space_DBQueryPrxHelper::checkedCast($prx);
    if (!$dbproxy)
        throw new RuntimeException("Invalid proxy");
    $ret = $dbproxy->sQuery("ly_user_0", 1,"SELECT * FROM ly_user_0");
    
    print_r($ret);
    $salt = rand(100000,99999);
    $password = md5("123456#".$salt);
    $ret = $dbproxy->sQuery("ly_user_0", 9, "INSERT INTO ly_user_0(uid,username,email,password,salt) VALUES(9,'tianbo','tianbo@ucai.cn','".$password."',$salt)");
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