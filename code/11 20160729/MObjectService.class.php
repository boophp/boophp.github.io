<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include_once("func.php");
require 'MObject.php';
class MObjectService
{

    private static $_moman = null;

    public function __construct()
    {
        self::$_moman = createIceProxy("MObjectBuffer", 13040);
        print_r(self::$_moman);
    }

    public function getDetail($type, $uid, $id, $cache_time = 60)
    {
        $uoids = array();
        $uoids[0] = new Space_MOID($uid, $id); 
        $ctx = ice_ctx_cache($cache_time);
        return self::$_moman->getUObjectDetailLong($type, $uoids, $ctx);
    }

    public function getDetails($type, $objs, $uidkey, $idkey, $cache_time = 60)
    {
        $uoids = array();
        $ucount = count($objs);
        for ($i = 0; $i < $ucount; $i++)
        {
            $uoids[$i] =new Space_MOID( $objs[$i][$uidkey], $objs[$i][$idkey]);
        }
        if (!$uoids)
        {
            return array();
        }  
        $ctx = ice_ctx_cache($cache_time);
        return self::$_moman->getMObjectDetailLong($type, $uoids, $ctx);
    }

}

?>
