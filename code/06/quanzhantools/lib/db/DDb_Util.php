<?php

/* * *************************************************************************
 *
 * Copyright (c) 2012 HighPer.cn, All Rights Reserved
 *
 *
 * ************************************************************************ */

/**
 * 数据操作相关工具方法
 *
 * @package
 * @subpackage
 */
class DDb_Util
{

    public static function arr2int(&$arr)
    {
        $arr = array_map("intval", $arr);
    }

//指定数组或者对象，返回给定字段
    public static function item_arr2ids($objs, $key)
    {
        $uids = array();
        if (is_array($objs))
        {
            foreach ($objs as $obj)
            {
                $uids[] = $obj[$key];
            }
        }
        else if (is_object($objs))
        {
            $uids = DDb_DB::objs2ids($objs, $key);
        }
        return $uids;
    }

    public static function item_objs2ids($objs, $key)
    {
        $uids = array();
        if (is_array($objs))
        {
            foreach ($objs as $obj)
            {
                $uids[] = $obj->$key;
            }
        }
        else if (is_object($objs))
        {
            $uids = DDb_DB::objs2ids($objs, $key);
        }
        return $uids;
    }

    public static function objs2ids($objs, $key)
    {
        $uids = array();
        if (is_array($objs))
        {
            foreach ($objs as $obj)
            {
                if (is_array($obj))
                {
                    $uids[] = $obj[$key];
                }
                else if (is_object($obj))
                {
                    $uids[] = $obj->$key;
                }
                else
                {
                    $uids[] = $obj;
                }
            }
        }
        else if (is_object($objs))
        {
            
        }

        return $uids;
    }

    public static function getIndexArray($arr, $field)
    {
        return DUtil_Base::toKeyIndexed($arr, $field);
        /*$indexarr = array();
        foreach ($arr as $item)
        {
            $key = $item[$field];
            $indexarr[$key] = $item;
        }
        return $indexarr;*/
    }

}

?>
