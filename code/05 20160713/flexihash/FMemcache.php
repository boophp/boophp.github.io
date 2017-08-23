<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */ 
require_once 'classes/Flexihash.php';

Class FMemcache
{

    public $hash = null;
    public $memcache = null;
    public $connectPool = null;

    public function __construct()
    {
        $this->hash = new Flexihash();
    }

    public function addServers($servers)
    {
        foreach ($servers as $server)
        { 
            $this->connectPool[$server] = false;
            $targets[] = $server;
        }
        $this->hash->addTargets($targets);
    }

    public function set($key, $value, $expire = 60)
    {
        $nodes = $this->hash->lookupList($key, count($this->connectPool));
       // print_r($nodes);
        foreach ($nodes as $node)
        {
            if (!$this->connectPool[$node])
            {
                $server = explode(':', $node);
                $this->connectPool[$node] = new Memcache();
                $this->connectPool[$node]->connect($server[0], $server[1]);
            }
            //只要一个设置成功就返回
            if ($this->connectPool[$node])
            {
                if ($this->connectPool[$node]->set($key, $value, MEMCACHE_COMPRESSED, $expire))
                {
                    return true;
                }
            }
        }
        return false;
    }
    public function _get($key)
    {
        $nodes = $this->hash->lookupList($key, count($this->connectPool));
        //print_r($nodes);
        foreach ($nodes as $node)
        {
            if (!$this->connectPool[$node])
            {
                $server = explode(':', $node);
                $this->connectPool[$node] = new Memcache();
                $this->connectPool[$node]->connect($server[0], $server[1]);
            }
            if ($this->connectPool[$node])
            {
                $val = $this->connectPool[$node]->get($key);
                if($val)
                {
                    return $val;
                }
            }
        }
        return false;
    }
    public function get($key)
    {   
        if(is_array($key))
        {
            $ret  = array();
            foreach($key as $skey)
            {
                $ret[$skey] = $this->_get($skey);
            }
            return $ret;
        }
        return $this->_get($key);
    }

}
