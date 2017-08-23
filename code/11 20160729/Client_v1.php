

<?php

require 'Ice.php';
require 'DBPQuery.php';
require "func.php";
try
{
    $prx = createIceProxy("DBPQuery", 12113);
    $dbproxy = Space_DBPQueryPrxHelper::checkedCast($prx);
    if (!$dbproxy)
        throw new RuntimeException("Invalid proxy");
    $ret = $dbproxy->sQuery("ly_user_1", 1,"SELECT * FROM ly_user_1");
    print_r($ret);
    $ret = $dbproxy->sQuery("ly_user_1", 1,"UPDATE ly_user_1 SET email='zhihua.cn9@ucai.cn' WHERE email='zhihua.cn9'");
    print_r($ret);
    $ret = $dbproxy->sQuery("ly_user_1", 1,"SELECT * FROM ly_user_1");
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