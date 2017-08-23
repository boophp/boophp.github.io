<?php
/**
 * Created by PhpStorm.
 * User: ucai
 * Date: 2016/7/13 0013
 * Time: 20:43
 */

require("../db.php");
connect_db();

$sql = "select SQL_NO_CACHE * from yhd_item WHERE iid=100";

require 'apc.php';
$handle = APC::getInstance();

$key = "ucai_data";
$data = $handle->agetObj($key);
if(empty($data))
{
    echo "get data from db";
    $data = query($sql);
    $handle->asetObj($key,$data);
    print_r($data);
}
else{
    echo "get data from cache";
    print_r($data);
}
close_db();