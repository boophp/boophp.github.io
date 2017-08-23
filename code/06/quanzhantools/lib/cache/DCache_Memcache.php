<?php

/**
 * memcache类的封装
 * @author chenlei
 *
 */
class DCache_Memcache
{

    private static $instance = null;

    public static function getInstance()
    {
        if (!is_null(self::$instance))
            return self::$instance;

        if (defined("SAE_ACCESSKEY"))
        {
            self::$instance = memcache_init();
            return;
        }

        if (class_exists('mmcache'))
        {
            //TODO .....
            return;
        }

        self::$instance = new Memcache();
        require_once ROOT_DIR . DS . "conf" . DS . "cache" . DS . "memcacheconf.php";
        foreach ($memcache_map as $key => $item)
        {
            self::$instance->addServer($item['host'], $item['port']); //TODO
        }
        return self::$instance;
    }

    /**
     * 设置指定键的值到 memcached，值以对象形式传入
     * @param $key 
     * @param $value
     * @param $expire
     */
    public static function setObj($key, $value, $expire = 0, $compressed=false)
    {
        self::getInstance();
        try
        {
            $value = serialize($value);
            $compressed = self::compressed($compressed);
            return self::$instance->set($key . "", $value . "", $compressed, intval($expire));
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * 设置指定键的值到 memcached，值以数或者字符串等简单变量形式传入
     * @param $key
     * @param $value
     * @param $expire
     */
    public static function set($key, $value, $expire = 0, $compressed=false)
    {
        self::getInstance();
        try
        {
            $compressed = self::compressed($compressed);
            return self::$instance->set($key . "", $value . "", $compressed, intval($expire));
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * 设置指定键的值到 memcached，值以对象形式传入，与 setObj 不同的是，
     * key 不能存在
     * @param $key
     * @param $value
     * @param $expire
     */
    public static function addObj($key, $value, $expire = 0)
    {
        self::getInstance();
        try
        {
            $value = serialize($value);
            return self::$instance->add($key . "", $value . "", intval($expire));
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * 设置指定键的值到 memcached，值以数或者字符串等简单变量形式传入
     * 与 set 不同的是，key 不能存在。
     * @param $key
     * @param $value
     * @param $expire
     */
    public static function add($key, $value, $expire = 0)
    {
        self::getInstance();
        try
        {
            return self::$instance->add($key . "", $value . "", intval($expire));
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * 设置指定键的值到 memcached，值以对象形式传入，与 setObj 不同的是，
     * key 必须已经存在
     * @param $key
     * @param $value
     * @param $expire
     */
    public static function replaceObj($key, $value, $expire = 0)
    {
        self::getInstance();
        try
        {
            $value = serialize($value);
            return self::$instance->replace($key . "", $value . "", intval($expire));
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * 设置指定键的值到 memcached，值以数或者字符串等简单变量形式传入
     * 与 set 不同的是，key 必须已经存在。
     * @param $key
     * @param $value
     * @param $expire
     */
    public static function replace($key, $value, $expire = 0)
    {
        self::getInstance();
        try
        {
            return self::$instance->replace($key . "", $value . "", intval($expire));
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     *
     * 查询指定键值被分配到哪个服务器 IP 了。
     * @param $key
     */
    public static function whichServer($key)
    {
        self::getInstance();
        try
        {
            return self::$instance->whichServer($key);
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    public static function spx_get($key, $obj = false)
    {
        self::getInstance();
        try
        {
            if ($key == "")
            {
                return false;
            }

            if (is_array($key))
            {

                if (empty($key))
                {
                    return array();
                }

                foreach ($key as $k => $v)
                {
                    $key[$k] = $v . "";
                }
                $key = array_unique($key);
                $len = count($key);
                $tmpret = array();
                for ($i = 0; $i < $len; $i+=500)
                {
                    $subkey = array_slice($key, $i, 500);
                    $tmpret = array_merge($tmpret, self::$instance->getAll($subkey));
                }

                $ret = array();
                if ($obj)
                {
                    foreach ($tmpret as $k => $v)
                    {
                        if (strlen($v))
                        {
                            $ret[$k] = unserialize($v);
                        }
                    }
                }
                else
                {
                    foreach ($tmpret as $k => $v)
                    {
                        if (strlen($v))
                        {
                            $ret[$k] = $v;
                        }
                    }
                }
            }
            else
            {
                $ret = self::$instance->get($key . "");
                if (strlen($ret) == 0)
                {
                    $ret = false;
                }
                else
                {
                    if ($obj)
                    {
                        $ret = unserialize($ret);
                    }
                }
            }
            return $ret;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * 取得指定键的值，值以对象形式的返回。
     * @param $key
     */
    public static function getObj($key)
    {
        return self::spx_get($key, true);
    }

    /**
     * 取得指定键的值，值以整数字符串等简单类型返回
     * @param $key
     */
    public static function get($key)
    {
        self::getInstance();
        return self::spx_get($key);
    }

    /**
     * 删除指定键的值
     * @param $key
     * @param $timeout
     */
    public static function delete($key, $timeout = 0)
    {
        self::getInstance();
        try
        {
            return self::$instance->delete($key . "", intval($timeout));
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * 对指定键的值添加一定数量, 指定键值必须存在
     * @param $key
     * @param $value
     */
    public static function increment($key, $value = 1)
    {
        self::getInstance();
        try
        {
            if ($value < 0)
            {
                return self::decrement($key, abs($value));
            }

            $ret = self::$instance->increment($key . "", intval($value));

            if ($ret < 0)
            {
                return false;
            }
            return $ret;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * 对指定键的值添加一定数值, 指定键值不一定必须存在
     * @param $key
     * @param $value
     * @param $expire
     */
    public static function incrementEx($key, $value = 1, $expire = 0)
    {
        self::getInstance();
        $ret = self::increment($key, $value);
        if ($ret !== false)
        {
            return $ret;
        }
        $ret = self::add($key, $value, $expire);
        if ($ret !== false)
        {
            return $value;
        }
        return self::increment($key, $value);
    }

    /**
     * 对指定键的值减少一定数值
     * @param $key
     * @param $value
     */
    public static function decrement($key, $value = 1)
    {
        self::getInstance();
        try
        {
            $ret = self::$instance->decrement($key . "", intval($value));
            if ($ret < 0)
            {
                return false;
            }
            return $ret;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    protected static function compressed($compressed)
    {
        return $compressed == false ? 0 : MEMCACHE_COMPRESSED;
    }

}

?>