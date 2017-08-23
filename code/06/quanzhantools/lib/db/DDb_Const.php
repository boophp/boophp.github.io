<?php

/**
 *
 *  数据库常量类
 * 
 * @package
 * @subpackage
 */
class DDb_Const
{
    /**
     * 防止 ID 滥用，所以 ID 要在这里注册一下
     * 维护一份ID增长器
     * @var type 
     */
  
     public static $id_map = array(
        "uid" => 'int',
        "gid" => "int",
        "cartid" => "int",
        "catid" => "int"
    );

    const ID_TABLE_KIND = 'qz_id_generator';
    const SERVER_TABLE_KIND = "qz_server_setting";
    const KIND_TABLE_KIND = "qz_kind_setting";
    const TABLE_TABLE_KIND = "qz_table_setting";
    const MEMCACHE_TABLE_KIND="qz_memcache_setting";


    const CHAR_ENCODING = "UTF-8";

    const SPLIT_TABLE_NUM = 4;
}

?>
