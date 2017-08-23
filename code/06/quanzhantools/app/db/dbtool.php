<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class dbtool extends DCore_BackApp
{

    protected $act;
    protected $host;
    protected $user;
    protected $port;
    protected $passwd;
    protected $master_sid;
    protected $sid;
    protected $cmd;
    protected $kind;

    protected function getParameter()
    {

        $this->act = $this->getParam("act");
        $this->host = $this->getParam("host");
        $this->user = $this->getParam("user", "root");
        $this->port = $this->getParam("port", 3306);
        $this->passwd = $this->getParam("passwd", "");
        $this->master_sid = $this->getParam("msid", 0);
        $this->sid = $this->getParam("sid");
        $this->cmd = $this->getParam("cmd");
        $this->kind = $this->getParam("kind");
    }

    protected function printUsage()
    {
        global $argc, $argv;
        echo "Usage: php " . $argv[0] . "\t act=query host=<ip>\n";
        echo "\t\t\t act=query msid=<master_sid>\n";
        echo "\t\t\t act=query sid=<sid>\n";
        echo "\t\t\t act=add host=<host>  user=<user> passwd=<passwd>  msid=<master_sid> port=<port>\n";
        echo "\t\t\t act=alter kind=<kind> cmd=<cmd>\n";
        echo "\t\t\t act=online host=<host>  port=<port>\n";
        echo "\t\t\t act=offline host=<host>  port=<port>\n";
        echo "\t\t\t act=liston\n";
        echo "\t\t\t act=listoff\n";
    }

    protected function main()
    {

        $config_handle = new DDb_Config();
        switch ($this->act)
        {
            case "query":
                if ($this->host)
                {
                    $ret = $config_handle->getServerByHost($this->host, $this->port);
                }
                else if ($this->sid)
                {
                    $ret = $config_handle->getServer($this->sid);
                }
                else if ($this->master_sid)
                {
                    $ret = $config_handle->getSlavesByMaster($this->master_sid);
                }
                else
                {
                    $this->printUsage();
                }
                break;
            case "online":
                if (!$this->host || !$this->port)
                {
                    $this->printUsage();
                }
                else
                {
                    $item = $config_handle->getServerByHost($this->host, $this->port);
                    if (count($item) == 1)
                    {
                        $item = $item[0];
                        $sid = $item["sid"];
                        $ret = $config_handle->updateActive($sid, 1);
                    }
                }
                break;
            case "offline":
                if (!$this->host || !$this->port)
                {
                    $this->printUsage();
                }
                else
                {
                    $item = $config_handle->getServerByHost($this->host, $this->port);
                    if (count($item) == 1)
                    {
                        $item = $item[0];
                        $sid = $item["sid"];
                        $ret = $config_handle->updateActive($sid, 0);
                    }
                }
                break;
            case "add":
                if (!$this->host || !$this->port || !$this->user || !$this->passwd)
                {
                    $this->printUsage();
                }
                else
                {
                    $ret = $config_handle->addServer($this->host, $this->user, $this->passwd, $this->master_sid, $this->port);
                }
                break;
            case "alter":
                $tables = $config_handle->getTableByKind($this->kind);
                $sids = array_unique(DDb_Util::item_arr2ids($tables, "sid"));
                $servers = $config_handle->getServerBySids($sids);
                $tbl_servers = array();
                foreach ($servers as $item)
                {
                    $tbl_servers[$item['sid']] = $item;
                }
                foreach ($tables as $tableitem)
                {
                    $sid = $tableitem['sid'];
                    $srvitem = $tbl_servers[$sid];
                    $host = $srvitem['host'];
                    $port = $srvitem['port'];
                    $user = $srvitem['user'];
                    $passwd = $srvitem['passwd'];
                    $dbname = $tableitem['db_name'];
                    if (count($tables) > 1)
                    {
                        $sql = str_replace("table_name_xxx", $tableitem["kind"] . "_" . $tableitem['no'], $this->cmd);
                    }
                    else
                    {
                        $sql = str_replace("table_name_xxx", $tableitem["kind"], $this->cmd);
                    }
                    echo $sql . " @ " . $host . ":$port\n";
                    $handle = new DDb_Handle($host, $port, $user, $passwd, $dbname);
                    $handle->runSql($sql);
                    $handle->closeDB();
                }
                break;
            case "liston":
                $ret = $config_handle->getServers();
                foreach ($ret as $item)
                {
                    if (!$item['active'])
                    {
                        continue;
                    }
                    echo $item['host'] . ":" . $item['port'] . " sid:" . $item['sid'] . " master_sid:" . $item['master_sid'] . " active:" . $item['active'] . "\n";
                }
                exit;
                break;
            case "listoff":
                $ret = $config_handle->getServers();
                foreach ($ret as $item)
                {
                    if ($item['active'])
                    {
                        continue;
                    }
                    echo $item['host'] . ":" . $item['port'] . " sid:" . $item['sid'] . " master_sid:" . $item['master_sid'] . " active:" . $item['active'] . "\n";
                }
                exit;
                break;
            default:
                $this->printUsage();
                break;
        }
        if (!empty($ret) && is_array($ret))
        {
            foreach ($ret as $item)
            {

                echo $item['host'] . ":" . $item['port'] . " sid:" . $item['sid'] . " master_sid:" . $item['master_sid'] . " active:" . $item['active'] . "\n";
            }
        }
    }

}

$dbtool = new dbtool();
$dbtool->run();
?>
