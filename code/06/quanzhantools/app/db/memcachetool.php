<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class mem extends DCore_BackApp
{

    protected $act;
    protected $host;
    protected $port;

    protected function getParameter()
    {
        $this->act = $this->getParam("act");
        $this->host = $this->getParam("host");
        $this->port = $this->getParam("port");
    }

    protected function checkParameter()
    {
        if (!$this->act)
        {
            $this->outputUsage();
        }
    }

    protected function outputUsage()
    {
        global $argv;
        echo "php " . $argv[0] . " act=<act> host=<host> port=<port>\n";
        echo " \t\t\tact=add      add  specify erver\n";
        echo " \t\t\tact=on        set specify server online \n";
        echo " \t\t\tact=off        set specify server offline \n";
        echo " \t\t\tact=delete  delete specify server \n";
        echo " \t\t\tact=liston    list online servers\n";
        echo " \t\t\tact=listoff    list offline servers\n";
        exit;
    }

    protected function main()
    {
        switch ($this->act)
        {
            case "add":
                $ret = DDb_Config::addMemCache($this->host, $this->port);
                break;
            case "on":
                $ret = DDb_Config::updateMemCache($this->host, $this->port);
                break;
            case "off":
                $ret = DDb_Config::updateMemCache($this->host, $this->port, 0);
                break;
            case "delete":
                $ret = DDb_Config::deleteMemCache($this->host, $this->port);
                break;
            case "liston":
                $ret = DDb_Config::getMemCacheServers();
                foreach ($ret as $item)
                {
                    echo $item['host'].":".$item['port']." active:".$item['active']."\n";
                }
                break;
            case "listoff":
                $ret = DDb_Config::getMemCacheServers(0);
                 foreach ($ret as $item)
                {
                    echo $item['host'].":".$item['port']." active:".$item['active']."\n";
                }
                break;
        }
    }

}

$mem = new mem();
$mem->run();
?>
