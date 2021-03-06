<?php

include_once("../../conf/global.php");

/**
 * 新建表的脚本,用于只有一组服务器时（配置库和数据库本身在一起时用）
 *
 * @package
 * @subpackage
 */
class dba extends DCore_BackApp
{

    protected $kind;
    protected $sid;
    protected $db_name;
    protected $sqlfile;
    protected $errmsg = "";

    protected function getParameter()
    {
        $this->kind = $this->getParam("kind");
        $this->sid = $this->getParam("sid");
        $this->db_name = $this->getParam("db");
        $this->sqlfile = $this->getParam("sqlfile");
        $this->configdb = $this->getParam("configdb");
    }

    protected function checkParameter()
    {
        global $argv;
        if (!$this->sid || !$this->kind || !$this->db_name || !$this->sqlfile || !$this->configdb)
        {
            $this->errmsg = "Usage: " . $argv[0] . " kind=<kind> sid=<sid-sid> db=<db_name> configdb=<config_name> sqlfile=<sqlfile>";
            return;
        }
    }

    protected function main()
    {
        if ($this->errmsg)
        {
            return;
        }
        if (!isset(DGroups_Config::$kind_config[$this->kind]))
        {
            $this->errmsg = "不是允许创建的表格 Kind";

            return;
        }
        $config = DGroups_Config::getTableConfig($this->kind);

        $table_num = $config['table_num'];

        $sids = explode("-", $this->sid);
        if ($table_num % count($sids))
        {
            $this->errmsg = "table_num 和 server 数量不匹配";
            return;
        }
        //先进行清理
        $log = "正在清理...";
        $this->outputLog($log);

        $configdb = new DDb_Config();
        $configdb->deleteKind($this->kind);
        $configdb->deleteTableByKind($this->kind);

        $id_field = $config['id_field'];
        //添加 Kind
        $log = "正在创建 Kind : {$this->kind}...";
        $this->outputLog($log);

        $configdb->addKind($this->kind, $id_field, $table_num);

        $sqlcontent = file_get_contents($this->sqlfile);

        $log = "正在添加表配置  table_num =$table_num ...";
        $this->outputLog($log);


        for ($i = 0; $i < $table_num; $i++)
        {
            $idx = $i % count($sids);
            $configdb->addTable($this->kind, $sids[$idx], $i, $this->db_name);
            $serveritem = $configdb->getServer($sids[$idx]);
            $serveritem = $serveritem[0];
            $host = $serveritem['host'];
            $port = $serveritem['port'];
            $user = $serveritem['user'];
            $passwd = $serveritem['passwd'];
            if ($table_num > 1)
            {
                $content = str_replace($this->kind . "_0", $this->kind . "_" . $i, $sqlcontent);
            }
            else
            {
                $content = $sqlcontent;
            }
            echo $this->kind . "_" . $i . " at " . $host . ":" . $port . " " . $this->db_name . "\n";
            mysql_select_db($this->db_name);
            //$handle = new DDb_Handle($host, $port, $user, $passwd, $this->db_name);
            mysql_query($content);
            //$handle->closeDB();
            mysql_select_db($this->configdb);
        }
    }

    protected function outputPage()
    {
        if ($this->errmsg)
        {
            $this->outputLog($this->errmsg);
        }
        else
        {
            $msg = "创建表格成功！";
            $this->outputLog($msg);
        }
    }

    protected function outputLog($msg)
    {
        $msg = DUtil_Convert::UTF8toGBK($msg);
        echo "--------------------------------------------------------------\n";
        echo "" . $msg . "\n";
        echo "--------------------------------------------------------------\n";
    }

}

$dba = new dba();
$dba->run();
?>
 
