<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class APC
{

    private static $xcobj;

    private function __construct()
    {
        
    }

    public final function __clone()
    {
        throw new BadMethodCallException("Clone is not allowed");
    }

    /**
     * getInstance 
     * 
     * @static
     * @access public
     * @return object CXCache instance
     */
    public static function getInstance()
    {
        if (!(self::$xcobj instanceof APC))
        {
            self::$xcobj = new APC();
        }
        return self::$xcobj;
    }

    /**
     * xset 
     * 
     * @param mixed $name 
     * @param mixed $value 
     * @access public
     * @return void
     */
    public function aset($name, $value, $expire = 0)
    {
        return apc_store($name, $value, $expire);
    }

    public function asetObj($name, $value, $expire = 0)
    {
        return apc_store($name, serialize($value), $expire);
    }

    /**
     * xget 
     * 
     * @param mixed $name 
     * @access public
     * @return void
     */
    public function aget($name)
    {
        return apc_fetch($name);
    }

    public function agetObj($name)
    {
        return unserialize(apc_fetch($name));
    }

    public function amget($name)
    {
        try
        {
            if (is_array($name))
            {
                if (empty($name))
                {
                    return array();
                }
                $keys = array_unique($name);
                $ret = array();
                foreach ($keys as $subkey)
                {
                    $val = apc_fetch($subkey);
                    if ($val)
                    {
                        $ret[$subkey] = $val;
                    }
                }
                return $ret;
            } else
            {
                return apc_fetch($name);
            }
        } catch (Exception $ex)
        {
            return false;
        }
    }

    public function amgetObj($name)
    {
        try
        {
            if (is_array($name))
            {
                if (empty($name))
                {
                    return array();
                }
                $keys = array_unique($name);
                $ret = array();
                foreach ($keys as $subkey)
                {
                    $val = apc_fetch($subkey);
                    if ($val)
                    {
                        $ret[$subkey] = unserialize($val);
                        if(!$ret[$subkey])
                        {
                            $ret[$subkey] = $val;
                        }
                    }
                }
                return $ret;
            } else
            {
                return unserialize(apc_fetch($name));
            }
        } catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * xisset 
     * 
     * @param mixed $name 
     * @access public
     * @return bool
     */
    public function aisset($name)
    {
        return apc_exists($name);
    }

    /**
     * xunset 
     * 
     * @param mixed $name 
     * @access public
     * @return void
     */
    public function xunset($name)
    {
        if (!$_SERVER['argc'])
        {//??????????ģʽִ?е?ʱ??
            return apc_delete($name);
        }
    }

    public function xinc($name, $value = 1, $expire = 0)
    {
        return apc_add($name, $value, $expire);
    }

    public function dec($name, $value = 1, $expire = 0)
    {
        return apc_dec($name, $value, $expire);
    }

}
