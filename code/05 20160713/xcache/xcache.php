<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CXCache
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
        if (!(self::$xcobj instanceof CXCache))
        {
            self::$xcobj = new CXCache;
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
    public function xset($name, $value, $expire = 0)
    {
        return xcache_set($name, $value, $expire);
    }

    public function xsetObj($name, $value, $expire = 0)
    {
        return xcache_set($name, serialize($value), $expire);
    }

    /**
     * xget 
     * 
     * @param mixed $name 
     * @access public
     * @return void
     */
    public function xget($name)
    {
        return xcache_get($name);
    }

    public function xgetObj($name)
    {
        return unserialize(xcache_get($name));
    }

    public function xmget($name)
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
                    $val = xcache_get($subkey);
                    if ($val)
                    {
                        $ret[$subkey] = $val;
                    }
                }
                return $ret;
            } else
            {
                return xcache_get($name);
            }
        } catch (Exception $ex)
        {
            return false;
        }
    }

    public function xmgetObj($name)
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
                    $val = xcache_get($subkey);
                    if ($val)
                    {
                        $ret[$subkey] = unserialize($val);
                    }
                }
                return $ret;
            } else
            {
                return unserialize(xcache_get($name));
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
    public function xisset($name)
    {
        return xcache_isset($name);
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
            return xcache_unset($name);
        }
    }

    public function xinc($name, $value = 1, $expire = 0)
    {
        return xcache_inc($name, $value, $expire);
    }

    public function dec($name, $value = 1, $expire = 0)
    {
        return xcache_dec($name, $value, $expire);
    }

}
