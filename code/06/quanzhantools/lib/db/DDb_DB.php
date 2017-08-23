<?php

/**
 * 业务数据库操作的进一步封装
 *
 * @package
 * @subpackage
 */
class DDb_DB
{

    private static $_mdbinstance = null;
    private static $_sdbinstance = null;
    private static $_dbinstances = array();

    public static function getDefaultInstance($type = "master")
    {
        global $CONFS;
        $dbinstance = null;

        if ($type == "master")
        {
            if (empty(self::$_mdbinstance))
            {
                if (defined("SAE_ACCESSKEY"))
                {
                    self::$_mdbinstance = new SaeMysql(false); ////new DDb_SaeHandle(true);
                }
                else
                {
                    self::$_mdbinstance = new DDb_Handle($CONFS['db']['host']['m'], $CONFS['db']['port'], $CONFS['db']['user'], $CONFS['db']['pass'], $CONFS['db']['name'], "UTF8");
                }
                if ($CONFS['db']['host']['m'] == $CONFS['db']['host']['s'])
                {
                    self::$_sdbinstance = self::$_mdbinstance;
                }
            }

            return self::$_mdbinstance;
        }
        else if ($type == "slave")
        {
            if (empty(self::$_sdbinstance))
            {
                if (defined("SAE_ACCESSKEY"))
                {
                    self::$_sdbinstance = new SaeMysql(false); //new DDb_SaeHandle(false);
                }
                else
                {
                    self::$_sdbinstance = new DDb_Handle($CONFS['db']['host']['s'], $CONFS['db']['port'], $CONFS['db']['user'], $CONFS['db']['pass'], $CONFS['db']['name'], "UTF8");
                }
                if ($CONFS['db']['host']['m'] == $CONFS['db']['host']['s'])
                {
                    self::$_mdbinstance = self::$_sdbinstance;
                }
            }

            return self::$_sdbinstance;
        }
    }

    public static function getConfigInstance(&$kind, $hintid = 1, $type = "master")
    {
        require_once ROOT_DIR . DS . "conf" . DS . "db" . DS . "dbconf.php";
        global $table_map;
        global $server_map;
        global $kind_map;
        $table_num = $kind_map[$kind]["table_num"];
        $index = $hintid % $table_num;
      //  echo "<font color=red>{$kind}</font>1<br />";

        //同一个进程，共用连接
        if (!empty(self::$_dbinstances) && !empty(self::$_dbinstances[$kind][$type][$index]))
        {
            $oldkind = $kind;
            if ($table_num > 1)
            {
                $kind = $kind . "_" . $index;
            } 
            return self::$_dbinstances[$oldkind][$type][$index];
        }

        if (empty($table_map))
        {
            $code = DExcept_Const::DB_EXCEPTION_CODE_INVALID_CONFIG;
            throw new DExcept_DBException($code);
        }
        //没有找到相应表的配置
        if (!isset($table_map[$kind]) || !isset($kind_map[$kind]))
        {
            $code = DExcept_Const::DB_EXCEPTION_CODE_INVALID_CONFIG;
            //echo $kind."\n";
            throw new DExcept_DBException($code);
        }

        if ($type == "master")
        {
            $table_num = $kind_map[$kind]["table_num"];
            $index = $hintid % $table_num;

            $sid = $table_map[$kind][$index]["sid"];
            $db_name = $table_map[$kind][$index]["db_name"];
            $config = $server_map[$sid];

            if (empty($config) || !$config["active"])
            {
                $code = DExcept_Const::DB_EXCEPTION_CODE_INVALID_MASTER;
                throw new DExcept_DBException($code);
            }

            self::$_dbinstances[$kind][$type][$index] = new DDb_Handle(
                    $config['host'], $config['port'], $config['user'], $config['passwd'], $db_name, "UTF8");

            $oldkind = $kind;
            //	echo "kind $kind ".$table_num."\n";
            if ($table_num > 1)
            {
                $kind = $kind . "_" . $index;
            }
            //	echo "kind $kind ".$table_num."\n";
            return self::$_dbinstances[$oldkind][$type][$index];
        }
        else
        {

            $table_num = $kind_map[$kind]["table_num"];
            $index = $hintid % $table_num;

            $sid = $table_map[$kind][$index]["sid"];
            $db_name = $table_map[$kind][$index]["db_name"];

            //检查主库
            $config = $server_map[$sid];

            if (empty($config) || !$config["active"])
            {
                $code = DExcept_Const::DB_EXCEPTION_CODE_INVALID_MASTER;
                throw new DExcept_DBException($code);
            }
            //没有找到辅库配置，直接调用主库配置
            if (!isset($config["slaves"]))
            {
                return self::getConfigInstance($kind, $hintid, "master");
            }
            $slaves = $config["slaves"];
            if (empty($slaves))
            {
                return self::getConfigInstance($kind, $hintid, "master");
            }

            //净化辅库
            $srvs = array();
            $count = 0;
            foreach ($slaves as $slave)
            {
                if (!$slave['active'])
                {
                    continue;
                }
                $count++;
                $srvs[] = $slave;
            }

            if (!$count)
            {
                $code = DExcept_Const::DB_EXCEPTION_CODE_NO_AVAILABLE_DB;
                throw new DExcept_DBException($code);
            }

            $tmpindex = rand(0, $count - 1);
            $config = $srvs[$tmpindex];
            self::$_dbinstances[$kind][$type][$index] = new DDb_Handle(
                    $config['host'], $config['port'], $config['user'], $config['passwd'], $db_name, "UTF8");
            $oldkind = $kind;
            if ($table_num > 1)
            {
                $kind = $kind . "_" . $index;
            }
            return self::$_dbinstances[$oldkind][$type][$index];
        }
    }

    //把传过来的多个分表id分类，被分到同一张表里的分到一组里
    public static function getTableByMulti($type, $hintids)
    {
        require_once ROOT_DIR . DS . "conf" . DS . "db" . DS . "dbconf.php";
        global $table_map;
        global $server_map;
        global $kind_map;
        $table_num = $kind_map[$kind]["table_num"];
        $index = $hintid % $table_num;

        $ret = array();
        if ($table_num > 1)
        {
            foreach ($hintids as $onehintids)
            {
                $index = $onehintids % $table_num;
                if (!isset($ret[$index]))
                {
                    $ret[$index] = array();
                }

                $ret[$index][] = $onehintids;
            }
        }
        else
        {
            $ret[] = $hintids;
        }

        return $ret;
    }

    public static function getInstance(&$kind, $hintid = 1, $type = "master")
    {

        $filename = ROOT_DIR . DS . "conf" . DS . "db" . DS . "dbconf.php";

        require_once $filename;
        global $table_map;
 
        if (empty($table_map))
        {
            return self::getDefaultInstance($type);
        }
        else
        {
            return self::getConfigInstance($kind, $hintid, $type);
        }
    }

    public static function getTableRow($table, $hintid, $condition, $logic = "AND", $fields = "*")
    {
        $db = self::getInstance($table, $hintid, "slave");

        return DDb_DBRaw::getTableRow($db, $table, $condition, $logic, $fields);
    }

    public static function getTableRowsWithOptions($table, $hintid, $options)
    {
        $db = self::getInstance($table, $hintid, "slave");
        return DDb_DBRaw::getTableRowsWithOptions($db, $table, $options);
    }

    public static function getTableRows($table, $hintid, $condition, $fields = "*")
    {
        $db = self::getInstance($table, $hintid, "slave");
        return DDb_DBRaw::getTableRows($db, $table, $condition, $fields);
    }

    public static function getTableLimitRows($table, $hintid, $condition, $start = 0, $num = 20, $order = "", $fields = "*")
    {
        $db = self::getInstance($table, $hintid, "slave");
        return DDb_DBRaw::getTableLimitRows($db, $table, $condition, $start, $num, $order, $fields);
    }

    public static function update($table, $hintid, $arr, $where)
    {
        $db = self::getInstance($table, $hintid, "master");
        return DDb_DBRaw::update($db, $table, $arr, $where);
    }

    public static function updateEx($table, $hintid, $arr, $change, $where)
    {
        $db = self::getInstance($table, $hintid, "master");
        return DDb_DBRaw::updateEx($db, $table, $arr, $change, $where);
    }

    public static function insert($table, $hintid, $arr, $updatefield = array(), $changefield = array())
    {
        $db = self::getInstance($table, $hintid, "master");
        //	echo $table."\n";
       // DUtil_Debug::pr($db);
        return DDb_DBRaw::insert($db, $table, $arr, $updatefield, $changefield);
    }

    public static function replace($table, $hintid, $arr)
    {
        $db = self::getInstance($table, $hintid, "master");
        return DDb_DBRaw::replace($db, $table, $arr);
    }

    public static function delete($table, $hintid, $condition)
    {
        $db = self::getInstance($table, $hintid, "master");
        return DDb_DBRaw::delete($db, $table, $condition);
    }

    public static function count($table, $hintid, $condition = null, $sum = null)
    {
        $db = self::getInstance($table, $hintid, "slave");
        return DDb_DBRaw::count($db, $table, $condition, $sum);
    }

    public static function doSql($table, $sql, $hintid = 1, $type = "master")
    {
        $db = self::getInstance($table, $hintid, $type);
        return DDb_DBRaw::doSql($db, $sql);
    }

    public static function getData($table, $sql, $hintid = 1)
    {
        $db = self::getInstance($table, $hintid, "slave");
        return $db->getData($sql);
    }
     
}

?>
